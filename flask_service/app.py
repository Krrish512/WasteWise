import os
import csv
from datetime import datetime
from flask import (
    Flask,
    render_template,
    request,
    redirect,
    url_for,
    session,
    jsonify,
    flash
)
from werkzeug.utils import secure_filename

# 5.1.3 Flask application object creation
app = Flask(__name__)

# Security & Session Configuration
app.secret_key = os.environ.get('SECRET_KEY', 'dev-wastewise-flask-secret-key-2026')
app.config['MAX_CONTENT_LENGTH'] = 2 * 1024 * 1024  # 2 MB upload limit

# Secure Upload Directory Configuration
BASE_DIR = os.path.dirname(os.path.abspath(__file__))
UPLOAD_FOLDER = os.path.join(BASE_DIR, 'uploads')
os.makedirs(UPLOAD_FOLDER, exist_ok=True)
app.config['UPLOAD_FOLDER'] = UPLOAD_FOLDER

ALLOWED_EXTENSIONS = {'csv'}

def allowed_file(filename):
    return '.' in filename and filename.rsplit('.', 1)[1].lower() in ALLOWED_EXTENSIONS

# -----------------------------------------------------------------------------
# 5.1.3.1 Route Declarations & Views
# -----------------------------------------------------------------------------

@app.route('/')
def home():
    """Redirect home route to insights analysis page."""
    return redirect(url_for('insights'))

@app.route('/insights', methods=['GET', 'POST'])
def insights():
    """
    5.3 HTTP Methods: GET displays analysis form, POST processes inputs.
    5.4 Request Object: request.form (POST) & request.args (GET pre-fill).
    """
    if request.method == 'POST':
        category = request.form.get('category', '').strip()
        city = request.form.get('city', '').strip()
        searches_raw = request.form.get('searches', '1').strip()

        if not category or not city:
            flash("Please provide both a waste category and a city name.", "error")
            return redirect(url_for('insights'))

        try:
            searches = max(1, int(searches_raw))
        except ValueError:
            flash("Search count must be a valid positive integer.", "error")
            return redirect(url_for('insights'))

        # Simple calculated insight string
        if searches >= 50:
            insight_msg = f"{category} has extremely high search volume in {city} ({searches} searches)."
        elif searches >= 15:
            insight_msg = f"{category} is currently highly searched in {city} ({searches} searches)."
        else:
            insight_msg = f"{category} shows steady search activity in {city} ({searches} searches)."

        # 5.4.2 Flask Session Storage
        session['last_category'] = category
        session['last_city'] = city
        session['last_searches'] = searches
        session['last_insight'] = insight_msg
        session['analyzed_at'] = datetime.now().strftime("%Y-%m-%d %H:%M:%S")

        # 5.4.4 Redirect with url_for
        return redirect(url_for('insights_result'))

    # GET handling with optional request.args pre-fill
    prefill_category = request.args.get('category', 'Plastic Waste')
    prefill_city = request.args.get('city', 'Surat')
    prefill_searches = request.args.get('searches', '20')

    return render_template(
        'index.html',
        prefill_category=prefill_category,
        prefill_city=prefill_city,
        prefill_searches=prefill_searches
    )

@app.route('/insights/result', methods=['GET'])
def insights_result():
    """
    Display result page reading dynamically from Flask session data.
    """
    if 'last_category' not in session:
        flash("No analysis found in session. Please run an analysis first.", "info")
        return redirect(url_for('insights'))

    result_data = {
        'category': session.get('last_category'),
        'city': session.get('last_city'),
        'searches': session.get('last_searches'),
        'insight': session.get('last_insight'),
        'analyzed_at': session.get('analyzed_at'),
        'csv_filename': session.get('csv_filename'),
        'csv_rows': session.get('csv_rows'),
        'csv_top_category': session.get('csv_top_category')
    }

    return render_template('result.html', result=result_data)

@app.route('/upload-csv', methods=['POST'])
def upload_csv():
    """
    5.4.3 Controlled CSV file upload handling with request.files[].
    Validates file extension, sanitizes filename, saves to disk, parses rows.
    """
    if 'file' not in request.files:
        flash("No file field submitted in request.", "error")
        return redirect(url_for('insights'))

    file = request.files['file']

    if file.filename == '':
        flash("No file selected for upload.", "error")
        return redirect(url_for('insights'))

    if not allowed_file(file.filename):
        flash("Invalid file type. Only .csv files are permitted.", "error")
        return redirect(url_for('insights'))

    safe_name = secure_filename(file.filename)
    timestamp = datetime.now().strftime("%Y%m%d_%H%M%S_")
    filename = f"{timestamp}{safe_name}"
    save_path = os.path.join(app.config['UPLOAD_FOLDER'], filename)

    try:
        # Save file to disk
        file.save(save_path)

        # Parse CSV file rows safely using Python standard library
        row_count = 0
        categories = []
        with open(save_path, mode='r', encoding='utf-8-sig', errors='replace') as f:
            reader = csv.reader(f)
            headers = next(reader, None)
            for row in reader:
                if row:
                    row_count += 1
                    if len(row) > 0 and row[0].strip():
                        categories.append(row[0].strip())

        top_cat = "N/A"
        if categories:
            from collections import Counter
            top_cat = Counter(categories).most_common(1)[0][0]

        session['csv_filename'] = safe_name
        session['csv_rows'] = row_count
        session['csv_top_category'] = top_cat

        flash(f"CSV data '{safe_name}' uploaded successfully ({row_count} data rows processed).", "success")
        return redirect(url_for('insights_result'))

    except Exception as e:
        flash(f"Error processing CSV file: {str(e)}", "error")
        return redirect(url_for('insights'))

# -----------------------------------------------------------------------------
# 5.3 & 5.4 JSON API Endpoints
# -----------------------------------------------------------------------------

@app.route('/api/analyze', methods=['POST'])
def api_analyze():
    """
    5.4.1 JSON API endpoint reading request.get_json() and returning JSON output.
    """
    data = request.get_json(silent=True)
    if not data or not isinstance(data, dict):
        return jsonify({
            "success": False,
            "error": "Invalid or missing JSON payload in request body."
        }), 400

    category = str(data.get('category', '')).strip()
    city = str(data.get('city', '')).strip()
    searches = data.get('searches', 1)

    if not category or not city:
        return jsonify({
            "success": False,
            "error": "Missing required fields: 'category' and 'city'."
        }), 422

    try:
        searches_int = int(searches)
    except (ValueError, TypeError):
        searches_int = 1

    if searches_int >= 50:
        insight = f"{category} has extremely high search volume in {city}."
    elif searches_int >= 15:
        insight = f"{category} is currently highly searched in {city}."
    else:
        insight = f"{category} shows steady search activity in {city}."

    return jsonify({
        "success": True,
        "category": category,
        "city": city,
        "searches": searches_int,
        "insight": insight,
        "service": "WasteWise Python Insights",
        "timestamp": datetime.now().strftime("%Y-%m-%d %H:%M:%S")
    }), 200

# -----------------------------------------------------------------------------
# Demonstration of HEAD, PUT, DELETE for Syllabus HTTP Methods Completeness
# -----------------------------------------------------------------------------

@app.route('/api/demo-methods', methods=['GET', 'HEAD', 'PUT', 'DELETE'])
def demo_http_methods():
    """Demonstrate handling of HEAD, PUT, DELETE HTTP methods."""
    if request.method == 'HEAD':
        response = app.make_default_options_response()
        response.headers['X-WasteWise-Service'] = 'Flask Insights'
        return response

    if request.method == 'PUT':
        payload = request.get_json(silent=True) or {}
        return jsonify({"method": "PUT", "status": "updated", "received": payload})

    if request.method == 'DELETE':
        return jsonify({"method": "DELETE", "status": "resource_cleared"})

    return jsonify({"method": "GET", "available_methods": ["GET", "HEAD", "PUT", "DELETE"]})

# -----------------------------------------------------------------------------
# 5.1.3.1 Explicit add_url_rule() Demonstration
# -----------------------------------------------------------------------------

def health_check():
    """
    Health check function registered via app.add_url_rule() instead of decorator.
    """
    return jsonify({
        "status": "ok",
        "service": "WasteWise Python Insights",
        "timestamp": datetime.now().strftime("%Y-%m-%d %H:%M:%S")
    })

# Demonstrate 5.1.3.1 add_url_rule()
app.add_url_rule('/health', view_func=health_check, methods=['GET'])

# -----------------------------------------------------------------------------
# Execution Entry Point
# -----------------------------------------------------------------------------

if __name__ == '__main__':
    # 5.1.3.1 app.run()
    port = int(os.environ.get('FLASK_PORT', 5000))
    app.run(host='127.0.0.1', port=port, debug=True)
