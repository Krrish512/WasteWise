import os
import csv
import json
import urllib.request
import urllib.error
from datetime import datetime
from flask import (
    Flask,
    render_template,
    request,
    redirect,
    url_for,
    session,
    jsonify,
    flash,
    send_from_directory
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

# Laravel API base URL (configurable via environment variable)
LARAVEL_API_BASE = os.environ.get('LARAVEL_API_BASE', 'http://127.0.0.1:8000')

def allowed_file(filename):
    return '.' in filename and filename.rsplit('.', 1)[1].lower() in ALLOWED_EXTENSIONS


def fetch_laravel_search_trends(focus='all'):
    """
    Fetch real WasteWise search history data from the Laravel JSON API.
    Uses urllib.request (Python standard library) — no extra dependencies.
    Returns parsed JSON dict on success, or None on failure.
    """
    url = f"{LARAVEL_API_BASE}/api/search-trends?focus={focus}"
    try:
        req = urllib.request.Request(url, headers={
            'Accept': 'application/json',
            'User-Agent': 'WasteWise-Flask-Insights/1.0',
        })
        with urllib.request.urlopen(req, timeout=10) as response:
            raw = response.read().decode('utf-8')
            data = json.loads(raw)
            if isinstance(data, dict) and data.get('success'):
                return data
    except urllib.error.URLError:
        pass
    except urllib.error.HTTPError:
        pass
    except (json.JSONDecodeError, ValueError, OSError):
        pass
    return None


def analyze_search_records(records):
    """
    Perform Python-side analysis on search history records fetched from Laravel.
    Returns a dict with: top_category, top_item, total_searches,
    category_breakdown, and a human-readable insight string.
    """
    total = len(records)
    if total == 0:
        return {
            'total_searches': 0,
            'top_category': 'N/A',
            'top_item': 'N/A',
            'category_breakdown': [],
            'insight': 'No search history data available yet. Use the main WasteWise app to search for waste items.',
        }

    # Count categories
    cat_counts = {}
    item_counts = {}
    for r in records:
        cat = r.get('category', 'Uncategorized')
        item = r.get('waste_item', 'Unknown Item')
        cat_counts[cat] = cat_counts.get(cat, 0) + 1
        item_counts[item] = item_counts.get(item, 0) + 1

    # Sort by count descending
    sorted_cats = sorted(cat_counts.items(), key=lambda x: x[1], reverse=True)
    sorted_items = sorted(item_counts.items(), key=lambda x: x[1], reverse=True)

    top_category = sorted_cats[0][0]
    top_category_count = sorted_cats[0][1]
    top_item = sorted_items[0][0]

    # Category breakdown with percentages
    breakdown = []
    for cat_name, count in sorted_cats:
        pct = round((count / total) * 100, 1)
        breakdown.append({
            'category': cat_name,
            'searches': count,
            'percentage': pct,
        })

    # Human-readable insight
    top_pct = round((top_category_count / total) * 100, 1)
    if total >= 20:
        insight = f"{top_category} dominates search trends with {top_category_count} searches ({top_pct}% of all {total} searches). Most searched item: {top_item}."
    elif total >= 5:
        insight = f"{top_category} leads search activity with {top_category_count} out of {total} total searches ({top_pct}%). Top item: {top_item}."
    else:
        insight = f"Early search data shows {top_category} as the leading category ({top_category_count} of {total} searches). Most searched: {top_item}."

    return {
        'total_searches': total,
        'top_category': top_category,
        'top_item': top_item,
        'category_breakdown': breakdown,
        'insight': insight,
    }


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
    GET displays landing page, POST fetches real WasteWise data from
    Laravel API and performs Python analysis.
    Demonstrates: request.form (POST), request.args (GET), session,
    redirect, url_for, Jinja2 rendering.
    """
    if request.method == 'POST':
        # 5.4.1 request.form — read selected focus from POST form
        focus = request.form.get('focus', 'all').strip()

        # Fetch real data from Laravel API via urllib.request
        api_data = fetch_laravel_search_trends(focus)

        if api_data is None:
            flash(
                "Could not connect to the main WasteWise application. "
                "Please ensure Laravel is running on port 8000 and try again.",
                "error"
            )
            return redirect(url_for('insights'))

        records = api_data.get('records', [])

        # Python-side analysis of real search history data
        analysis = analyze_search_records(records)

        # 5.4.2 Flask Session Storage
        session['last_category'] = analysis['top_category']
        session['last_item'] = analysis['top_item']
        session['last_searches'] = analysis['total_searches']
        session['last_insight'] = analysis['insight']
        session['category_breakdown'] = analysis['category_breakdown']
        session['analyzed_at'] = datetime.now().strftime("%Y-%m-%d %H:%M:%S")
        session['analysis_source'] = 'WasteWise Live Data'
        session['analysis_focus'] = focus

        # Clear CSV metadata when running live analysis
        session.pop('csv_filename', None)
        session.pop('csv_rows', None)
        session.pop('csv_top_category', None)

        # 5.4.4 Redirect with url_for
        return redirect(url_for('insights_result'))

    # GET handling with optional request.args parameters (5.4.1 request.args)
    selected_focus = request.args.get('focus', 'all')

    return render_template(
        'index.html',
        selected_focus=selected_focus,
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
        'item': session.get('last_item', 'N/A'),
        'searches': session.get('last_searches'),
        'insight': session.get('last_insight'),
        'category_breakdown': session.get('category_breakdown', []),
        'analyzed_at': session.get('analyzed_at'),
        'analysis_source': session.get('analysis_source', 'System'),
        'analysis_focus': session.get('analysis_focus', 'all'),
        'csv_filename': session.get('csv_filename'),
        'csv_rows': session.get('csv_rows'),
        'csv_top_category': session.get('csv_top_category'),
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
        session['last_item'] = top_category  # CSV uses category as primary label
        session['last_searches'] = top_searches
        session['last_insight'] = insight_msg
        session['category_breakdown'] = []
        session['analyzed_at'] = datetime.now().strftime("%Y-%m-%d %H:%M:%S")
        session['analysis_source'] = 'CSV Upload'
        session['analysis_focus'] = 'csv'
        session['csv_filename'] = safe_name
        session['csv_rows'] = row_count
        session['csv_top_category'] = top_category

        flash(f"CSV data '{safe_name}' processed successfully ({row_count} data rows). Top trend: {top_category} in {top_city}.", "success")
        return redirect(url_for('insights_result'))

    except Exception as e:
        flash(f"Error processing CSV file: {str(e)}", "error")
        return redirect(url_for('insights'))


def parse_and_validate_collection_centre_csv(filepath):
    """
    Parse and perform initial Python-side validation on uploaded Collection Centre CSV file.
    Validates presence of required headers: name, category, city, address.
    Checks data formats for each row. Returns (valid_rows, error_messages).
    """
    valid_rows = []
    errors = []

    try:
        with open(filepath, mode='r', encoding='utf-8-sig', errors='replace') as f:
            reader = csv.reader(f)
            header_row = next(reader, None)

            if not header_row:
                return None, ["The uploaded CSV file is empty."]

            headers = [h.strip().lower() for h in header_row]

            # Determine column mapping
            col_map = {}
            for idx, h in enumerate(headers):
                if 'name' in h:
                    col_map['name'] = idx
                elif 'category' in h:
                    col_map['category'] = idx
                elif 'city' in h:
                    col_map['city'] = idx
                elif 'address' in h:
                    col_map['address'] = idx
                elif 'lat' in h:
                    col_map['latitude'] = idx
                elif 'long' in h or 'lng' in h:
                    col_map['longitude'] = idx
                elif 'phone' in h:
                    col_map['phone'] = idx

            # Check required headers
            required = ['name', 'category', 'city', 'address']
            missing = [req for req in required if req not in col_map]
            if missing:
                return None, [f"Missing required CSV headers: {', '.join(missing)}. Header row found: {', '.join(header_row)}"]

            row_num = 1
            for row in reader:
                row_num += 1
                if not row or not any(field.strip() for field in row):
                    continue

                def get_val(key):
                    idx = col_map.get(key)
                    if idx is not None and len(row) > idx:
                        return row[idx].strip()
                    return ''

                name = get_val('name')
                category = get_val('category')
                city = get_val('city')
                address = get_val('address')
                phone = get_val('phone')
                lat_raw = get_val('latitude')
                lng_raw = get_val('longitude')

                # Basic row checks
                if not name:
                    errors.append(f"Row {row_num}: Centre Name is required.")
                if not category:
                    errors.append(f"Row {row_num}: Waste Category is required.")
                if not city:
                    errors.append(f"Row {row_num}: City is required.")
                if not address:
                    errors.append(f"Row {row_num}: Address is required.")

                lat_val = None
                if lat_raw:
                    try:
                        lat_val = float(lat_raw)
                        if not (-90 <= lat_val <= 90):
                            errors.append(f"Row {row_num}: Latitude must be between -90 and 90.")
                    except ValueError:
                        errors.append(f"Row {row_num}: Latitude must be a numeric value.")

                lng_val = None
                if lng_raw:
                    try:
                        lng_val = float(lng_raw)
                        if not (-180 <= lng_val <= 180):
                            errors.append(f"Row {row_num}: Longitude must be between -180 and 180.")
                    except ValueError:
                        errors.append(f"Row {row_num}: Longitude must be a numeric value.")

                row_dict = {
                    'name': name,
                    'category': category,
                    'city': city,
                    'address': address,
                    'phone': phone if phone else None,
                    'latitude': lat_val,
                    'longitude': lng_val,
                }
                valid_rows.append(row_dict)

            if not valid_rows and not errors:
                errors.append("CSV file contains no valid data rows.")

            return valid_rows, errors

    except Exception as e:
        return None, [f"Error reading CSV file: {str(e)}"]


@app.route('/import-collection-centres', methods=['POST'])
def import_collection_centres():
    """
    Flask Collection Centre CSV Import Flow:
    1. Accept .csv upload with size/extension checks.
    2. Safely parse and validate rows in Python.
    3. Forward structured JSON payload to protected Laravel API endpoint.
    4. Handle response & row-level validation errors cleanly.
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
    filename = f"centres_{timestamp}{safe_name}"
    save_path = os.path.join(app.config['UPLOAD_FOLDER'], filename)

    try:
        file.save(save_path)

        rows, parse_errors = parse_and_validate_collection_centre_csv(save_path)

        if parse_errors or not rows:
            err_msg = "CSV parsing failed: " + "; ".join(parse_errors[:3])
            flash(err_msg, "error")
            return redirect(url_for('insights'))

        # Prepare JSON payload for protected Laravel API
        payload = json.dumps({'centres': rows}).encode('utf-8')
        api_url = f"{LARAVEL_API_BASE}/api/admin/collection-centres/import"

        secret_key = os.environ.get('SECRET_KEY', 'dev-wastewise-flask-secret-key-2026')
        req = urllib.request.Request(api_url, data=payload, headers={
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-Admin-Api-Key': secret_key,
            'User-Agent': 'WasteWise-Flask-Import/1.0',
        })

        try:
            with urllib.request.urlopen(req, timeout=15) as response:
                resp_raw = response.read().decode('utf-8')
                resp_data = json.loads(resp_raw)

                if resp_data.get('success'):
                    imported = resp_data.get('imported', 0)
                    updated = resp_data.get('updated', 0)
                    flash(
                        f"Collection Centres imported successfully! "
                        f"({imported} new centres created, {updated} existing centres updated).",
                        "success"
                    )
                else:
                    flash(f"Import failed: {resp_data.get('message', 'Validation errors occurred.')}", "error")

        except urllib.error.HTTPError as http_err:
            try:
                err_body = http_err.read().decode('utf-8')
                err_json = json.loads(err_body)
                errors_list = err_json.get('errors', [])
                if errors_list and isinstance(errors_list, list):
                    first_err = errors_list[0]
                    detail = f"Row {first_err.get('row', '?')} ({first_err.get('field', 'field')}): {first_err.get('message', '')}"
                    flash(f"Laravel Validation Error: {detail} (Total failed: {len(errors_list)})", "error")
                else:
                    flash(f"Import failed with HTTP {http_err.code}: {err_json.get('message', http_err.reason)}", "error")
            except Exception:
                flash(f"Laravel API returned error (HTTP {http_err.code}): {http_err.reason}", "error")

        except urllib.error.URLError as url_err:
            flash(f"Could not connect to main WasteWise application: {url_err.reason}", "error")

        return redirect(url_for('insights'))

    except Exception as e:
        flash(f"Error during Collection Centre import: {str(e)}", "error")
        return redirect(url_for('insights'))


@app.route('/download-sample-csv', methods=['GET'])
def download_sample_csv():
    """
    Provide downloadable sample CSV template for Collection Centre bulk import.
    """
    return send_from_directory(
        BASE_DIR,
        'sample_collection_centres.csv',
        as_attachment=True,
        download_name='sample_collection_centres.csv'
    )

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
