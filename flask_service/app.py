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
    5.3 HTTP Methods: GET displays landing page, POST processes analytics focus.
    5.4 Request Object: request.form (POST) & request.args (GET filter parameters).
    """
    if request.method == 'POST':
        focus = request.form.get('focus', 'all').strip()
        custom_category = request.form.get('category', '').strip()
        custom_city = request.form.get('city', '').strip()
        custom_searches = request.form.get('searches', '').strip()

        # Determine category, city, search count based on focus or custom values
        if custom_category and custom_city:
            category = custom_category
            city = custom_city
            try:
                searches = max(1, int(custom_searches)) if custom_searches else 20
            except ValueError:
                searches = 20
        elif focus == 'electronic':
            category = 'Electronic Waste'
            city = 'Surat'
            searches = 45
        elif focus == 'plastic':
            category = 'Plastic Waste'
            city = 'Surat'
            searches = 30
        elif focus == 'organic':
            category = 'Organic Waste'
            city = 'Ahmedabad'
            searches = 25
        else:
            category = 'Plastic Waste'
            city = 'Surat'
            searches = 20

        # Calculate insight string
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
        session['analysis_source'] = 'WasteWise Dataset'

        # Clear CSV metadata when running quick analysis
        session.pop('csv_filename', None)
        session.pop('csv_rows', None)
        session.pop('csv_top_category', None)

        # 5.4.4 Redirect with url_for
        return redirect(url_for('insights_result'))

    # GET handling with optional request.args parameters (5.4.1 request.args)
    selected_focus = request.args.get('focus', 'all')
    prefill_category = request.args.get('category', 'Plastic Waste')
    prefill_city = request.args.get('city', 'Surat')
    prefill_searches = request.args.get('searches', '20')

    return render_template(
        'index.html',
        selected_focus=selected_focus,
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
        'analysis_source': session.get('analysis_source', 'System'),
        'csv_filename': session.get('csv_filename'),
        'csv_rows': session.get('csv_rows'),
        'csv_top_category': session.get('csv_top_category')
    }

    return render_template('result.html', result=result_data)

@app.route('/upload-csv', methods=['POST'])
def upload_csv():
    """
    5.4.3 Controlled CSV file upload handling with request.files[].
    Validates file extension, sanitizes filename, saves to disk, parses rows,
    determines the row with highest search count, and sets calculated session result.
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

        row_count = 0
        parsed_rows = []

        with open(save_path, mode='r', encoding='utf-8-sig', errors='replace') as f:
            reader = csv.reader(f)
            headers = next(reader, None)

            cat_idx = 0
            city_idx = 1
            search_idx = 2

            if headers:
                header_lower = [h.strip().lower() for h in headers]
                for idx, h in enumerate(header_lower):
                    if 'category' in h:
                        cat_idx = idx
                    elif 'city' in h:
                        city_idx = idx
                    elif 'search' in h or 'count' in h:
                        search_idx = idx

            for row in reader:
                if not row or not any(field.strip() for field in row):
                    continue

                row_count += 1
                cat = row[cat_idx].strip() if len(row) > cat_idx and row[cat_idx].strip() else "Uncategorized"
                city = row[city_idx].strip() if len(row) > city_idx and row[city_idx].strip() else "Unknown City"

                search_cnt = 1
                if len(row) > search_idx:
                    try:
                        search_cnt = int(row[search_idx].strip())
                    except (ValueError, TypeError):
                        search_cnt = 1

                parsed_rows.append({
                    'category': cat,
                    'city': city,
                    'searches': search_cnt
                })

        if not parsed_rows:
            flash("The uploaded CSV file contains no valid data rows.", "error")
            return redirect(url_for('insights'))

        # Find row with highest search count in the uploaded CSV
        top_row = max(parsed_rows, key=lambda r: r['searches'])
        top_category = top_row['category']
        top_city = top_row['city']
        top_searches = top_row['searches']

        # Calculate insight string for CSV top row
        if top_searches >= 50:
            insight_msg = f"{top_category} has extremely high search volume in {top_city} ({top_searches} searches)."
        elif top_searches >= 15:
            insight_msg = f"{top_category} is currently highly searched in {top_city} ({top_searches} searches)."
        else:
            insight_msg = f"{top_category} shows steady search activity in {top_city} ({top_searches} searches)."

        # Store calculated CSV results in Flask session
        session['last_category'] = top_category
        session['last_city'] = top_city
        session['last_searches'] = top_searches
        session['last_insight'] = insight_msg
        session['analyzed_at'] = datetime.now().strftime("%Y-%m-%d %H:%M:%S")
        session['analysis_source'] = 'CSV Upload'
        session['csv_filename'] = safe_name
        session['csv_rows'] = row_count
        session['csv_top_category'] = top_category

        flash(f"CSV data '{safe_name}' processed successfully ({row_count} data rows). Top trend: {top_category} in {top_city}.", "success")
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
