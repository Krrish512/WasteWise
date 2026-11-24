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
            'percentage': 0.0,
            'most_active_day': 'N/A',
            'category_breakdown': [],
            'category_distribution': [],
            'insight': 'No search history data available yet. Use the main WasteWise app to search for waste items.',
            'status': 'success',
            'source': 'flask',
        }

    # Count categories and items
    cat_counts = {}
    item_counts = {}
    for r in records:
        cat = r.get('category') or r.get('category_name') or 'Uncategorized'
        item = r.get('waste_item') or r.get('waste_item_name') or 'Unknown Item'
        cat_counts[cat] = cat_counts.get(cat, 0) + 1
        item_counts[item] = item_counts.get(item, 0) + 1

    sorted_cats = sorted(cat_counts.items(), key=lambda x: x[1], reverse=True)
    sorted_items = sorted(item_counts.items(), key=lambda x: x[1], reverse=True)

    top_category = sorted_cats[0][0]
    top_category_count = sorted_cats[0][1]
    top_item = sorted_items[0][0]

    top_pct = round((top_category_count / total) * 100, 1)

    breakdown = []
    for cat_name, count in sorted_cats:
        pct = round((count / total) * 100, 1)
        breakdown.append({
            'category': cat_name,
            'searches': count,
            'percentage': pct,
        })

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
        'percentage': top_pct,
        'most_active_day': 'Today',
        'category_breakdown': breakdown,
        'category_distribution': breakdown,
        'insight': insight,
        'status': 'success',
        'source': 'flask',
    }


def read_csv_with_autodetection(filepath):
    """
    Automatic delimiter detection (sep=None, engine='python') and UTF-8-SIG BOM handling
    so Excel and government CSV exports never fail.
    Tries pandas first if available, falls back to Python csv.Sniffer with utf-8-sig.
    """
    try:
        import pandas as pd
        # Automatic delimiter detection (sep=None, engine='python') and UTF-8-SIG BOM handling
        df = pd.read_csv(filepath, sep=None, engine='python', encoding='utf-8-sig', dtype=str)
        df = df.fillna('')
        headers = [str(col).strip() for col in df.columns]
        rows = []
        for _, row in df.iterrows():
            rows.append([str(v).strip() for v in row.values])
        return headers, rows
    except Exception:
        # Fallback to Python standard library csv module with automatic sniffer
        with open(filepath, mode='r', encoding='utf-8-sig', errors='replace') as f:
            content = f.read()

        lines = [line for line in content.splitlines() if line.strip()]
        if not lines:
            return None, []

        delimiter = ','
        try:
            sample = '\n'.join(lines[:10])
            sniffer = csv.Sniffer()
            dialect = sniffer.sniff(sample, delimiters=',;\t|')
            delimiter = dialect.delimiter
        except Exception:
            first_line = lines[0]
            if ';' in first_line and first_line.count(';') > first_line.count(','):
                delimiter = ';'
            elif '\t' in first_line:
                delimiter = '\t'

        reader = csv.reader(lines, delimiter=delimiter)
        header_row = next(reader, None)
        if not header_row:
            return None, []

        rows = [row for row in reader if row and any(f.strip() for f in row)]
        return [h.strip() for h in header_row], rows


def parse_and_validate_collection_centre_csv(filepath):
    """
    Parse and validate uploaded Collection Centre CSV file.
    Supports:
    - Extended 18-column research schema (centre_name/name, district, city, category,
      address, phone, latitude, longitude, facility_type, public_access,
      authorization_status, authorization_id, authorization_valid_until,
      website, source_url, verification_date, confidence, notes).
    - Backward compatibility with legacy 8-column format.
    - Optional latitude and longitude (rows are never dropped if coords missing).
    - Global bounds validation (-90..90, -180..180) as ERRORS.
    - Gujarat bounding-box validation (lat 20.0–24.8, lng 68.0–74.6) as WARNINGS.
    Returns (valid_rows, errors, warnings).
    """
    valid_rows = []
    errors = []
    warnings = []

    try:
        headers, rows = read_csv_with_autodetection(filepath)
        if headers is None or not headers:
            return None, ["The uploaded CSV file is empty."], []

        headers_lower = [h.strip().lower() for h in headers]

        # Determine column mappings supporting both 18-column and legacy 8-column formats
        col_map = {}
        for idx, h in enumerate(headers_lower):
            if h in ('name', 'centre_name', 'center_name', 'facility_name') or 'centre_name' in h:
                col_map['name'] = idx
            elif 'category' in h or h in ('waste_category', 'categories', 'waste_type'):
                col_map['category'] = idx
            elif h in ('city', 'town', 'municipality'):
                col_map['city'] = idx
            elif 'district' in h or h == 'dist':
                col_map['district'] = idx
            elif 'address' in h or h == 'location':
                col_map['address'] = idx
            elif 'lat' in h:
                col_map['latitude'] = idx
            elif 'long' in h or 'lng' in h:
                col_map['longitude'] = idx
            elif 'phone' in h or 'contact' in h or 'mobile' in h:
                col_map['phone'] = idx
            elif 'facility_type' in h or h == 'type':
                col_map['facility_type'] = idx
            elif 'public_access' in h or h == 'access':
                col_map['public_access'] = idx
            elif 'authorization_status' in h or h == 'auth_status':
                col_map['authorization_status'] = idx
            elif 'authorization_id' in h or h in ('gpcb_id', 'auth_id', 'reg_no'):
                col_map['authorization_id'] = idx
            elif 'authorization_valid_until' in h or 'valid_until' in h or 'expiry' in h:
                col_map['authorization_valid_until'] = idx
            elif h in ('website', 'web', 'site_url'):
                col_map['website'] = idx
            elif 'source_url' in h or h == 'source':
                col_map['source_url'] = idx
            elif 'verification_date' in h or 'verified_on' in h:
                col_map['verification_date'] = idx
            elif 'confidence' in h:
                col_map['confidence'] = idx
            elif 'notes' in h or 'remarks' in h or 'comments' in h:
                col_map['notes'] = idx

        # Fallback for name if col_map missed it but 'name' was in header
        if 'name' not in col_map:
            for idx, h in enumerate(headers_lower):
                if 'name' in h:
                    col_map['name'] = idx
                    break

        required = ['name', 'category', 'city', 'address']
        missing = [req for req in required if req not in col_map]
        if missing:
            return None, [f"Missing required CSV headers: {', '.join(missing)}. Header row found: {', '.join(headers)}"], []

        row_num = 1
        for row in rows:
            row_num += 1
            if not row or not any(field.strip() for field in row):
                continue

            def get_val(key):
                idx = col_map.get(key)
                if idx is not None and len(row) > idx:
                    return str(row[idx]).strip()
                return ''

            name = get_val('name')
            category = get_val('category')
            city = get_val('city')
            district = get_val('district')
            address = get_val('address')
            phone = get_val('phone')
            lat_raw = get_val('latitude')
            lng_raw = get_val('longitude')
            facility_type = get_val('facility_type') or 'public_dropoff'
            public_access = get_val('public_access') or 'public_dropoff'
            auth_status = get_val('authorization_status') or 'verified'
            auth_id = get_val('authorization_id')
            auth_valid_until = get_val('authorization_valid_until')
            website = get_val('website')
            source_url = get_val('source_url')
            verification_date = get_val('verification_date')
            confidence = get_val('confidence') or 'medium'
            notes = get_val('notes')

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
            lng_val = None

            # Coordinates are optional instead of dropping rows
            if lat_raw:
                try:
                    lat_val = float(lat_raw)
                    if not (-90 <= lat_val <= 90):
                        errors.append(f"Row {row_num}: Latitude must be between -90 and 90.")
                except ValueError:
                    errors.append(f"Row {row_num}: Latitude must be a numeric value.")

            if lng_raw:
                try:
                    lng_val = float(lng_raw)
                    if not (-180 <= lng_val <= 180):
                        errors.append(f"Row {row_num}: Longitude must be between -180 and 180.")
                except ValueError:
                    errors.append(f"Row {row_num}: Longitude must be a numeric value.")

            # Gujarat bounding box validation: lat 20.0–24.8, lng 68.0–74.6 (WARNING, not error)
            if lat_val is not None and lng_val is not None:
                in_gujarat = (20.0 <= lat_val <= 24.8) and (68.0 <= lng_val <= 74.6)
                if not in_gujarat:
                    warnings.append(
                        f"Row {row_num} ('{name}'): Coordinate ({lat_val}, {lng_val}) is outside the Gujarat bounding box (lat 20.0–24.8, lng 68.0–74.6)."
                    )

            row_dict = {
                'name': name,
                'category': category,
                'city': city,
                'district': district if district else None,
                'address': address,
                'phone': phone if phone else None,
                'latitude': lat_val,
                'longitude': lng_val,
                'facility_type': facility_type,
                'public_access': public_access,
                'authorization_status': auth_status,
                'authorization_id': auth_id if auth_id else None,
                'authorization_valid_until': auth_valid_until if auth_valid_until else None,
                'website': website if website else None,
                'source_url': source_url if source_url else None,
                'verification_date': verification_date if verification_date else None,
                'confidence': confidence,
                'notes': notes if notes else None,
            }
            valid_rows.append(row_dict)

        if not valid_rows and not errors:
            errors.append("CSV file contains no valid data rows.")

        return valid_rows, errors, warnings

    except Exception as e:
        return None, [f"Error reading CSV file: {str(e)}"], []


@app.route('/')
def home():
    """Redirect home route to insights analysis page."""
    return redirect(url_for('insights'))


@app.route('/insights', methods=['GET', 'POST'])
def insights():
    """
    Main WasteWise Python Insights analysis page.
    """
    if request.method == 'GET':
        prefill_focus = request.args.get('focus', 'all').lower()
        if prefill_focus not in ('all', 'electronic', 'plastic', 'organic'):
            prefill_focus = 'all'
        return render_template('index.html', prefill_focus=prefill_focus)

    focus = request.form.get('focus', 'all').strip().lower()
    if focus not in ('all', 'electronic', 'plastic', 'organic'):
        focus = 'all'

    api_result = fetch_laravel_search_trends(focus)

    if api_result and 'records' in api_result:
        records = api_result['records']
        if records:
            analysis = analyze_search_records(records)
            source = 'WasteWise Live Data'
        else:
            analysis = {
                'total_searches': 0,
                'top_category': 'N/A',
                'top_item': 'N/A',
                'category_breakdown': [],
                'insight': 'No search history data available yet. Use the main WasteWise app to search for waste items.',
            }
            source = 'WasteWise Live Data (Empty)'
    else:
        analysis = {
            'total_searches': 0,
            'top_category': 'N/A',
            'top_item': 'N/A',
            'category_breakdown': [],
            'insight': f'Could not connect to the main WasteWise application. Ensure Laravel is running at {LARAVEL_API_BASE}.',
        }
        source = 'Offline Notice'

    session['last_category'] = analysis['top_category']
    session['last_item'] = analysis['top_item']
    session['last_searches'] = analysis['total_searches']
    session['last_insight'] = analysis['insight']
    session['category_breakdown'] = analysis['category_breakdown']
    session['analyzed_at'] = datetime.now().strftime("%Y-%m-%d %H:%M:%S")
    session['analysis_source'] = source
    session['analysis_focus'] = focus
    session.pop('csv_filename', None)
    session.pop('csv_rows', None)
    session.pop('csv_top_category', None)

    return redirect(url_for('insights_result'))


@app.route('/insights/result', methods=['GET'])
def insights_result():
    """
    Render analysis results from session.
    """
    if 'last_category' not in session:
        flash("No analysis has been performed yet. Please submit an analysis first.", "info")
        return redirect(url_for('insights'))

    result_data = {
        'category': session.get('last_category', 'N/A'),
        'item': session.get('last_item', 'N/A'),
        'searches': session.get('last_searches', 0),
        'insight': session.get('last_insight', ''),
        'category_breakdown': session.get('category_breakdown', []),
        'analyzed_at': session.get('analyzed_at', ''),
        'analysis_source': session.get('analysis_source', 'Direct Form'),
        'analysis_focus': session.get('analysis_focus', 'all'),
        'csv_filename': session.get('csv_filename'),
        'csv_rows': session.get('csv_rows'),
        'csv_top_category': session.get('csv_top_category'),
        'import_warnings': session.get('last_import_warnings', []),
        'import_errors': session.get('last_import_errors', []),
        'import_summary': session.get('last_import_summary'),
    }

    return render_template('result.html', result=result_data)


@app.route('/upload-csv', methods=['POST'])
def upload_csv():
    """
    Controlled CSV file upload handling with automatic delimiter and BOM handling.
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
        file.save(save_path)
        headers, rows = read_csv_with_autodetection(save_path)

        if not rows:
            flash("The uploaded CSV file contains no valid data rows.", "error")
            return redirect(url_for('insights'))

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

        parsed_rows = []
        for row in rows:
            cat = row[cat_idx].strip() if len(row) > cat_idx and row[cat_idx].strip() else "Uncategorized"
            city = row[city_idx].strip() if len(row) > city_idx and row[city_idx].strip() else "Unknown City"
            search_cnt = 1
            if len(row) > search_idx:
                try:
                    search_cnt = int(row[search_idx].strip())
                except (ValueError, TypeError):
                    search_cnt = 1
            parsed_rows.append({'category': cat, 'city': city, 'searches': search_cnt})

        top_row = max(parsed_rows, key=lambda r: r['searches'])
        top_category = top_row['category']
        top_city = top_row['city']
        top_searches = top_row['searches']

        if top_searches >= 50:
            insight_msg = f"{top_category} has extremely high search volume in {top_city} ({top_searches} searches)."
        elif top_searches >= 15:
            insight_msg = f"{top_category} is currently highly searched in {top_city} ({top_searches} searches)."
        else:
            insight_msg = f"{top_category} shows steady search activity in {top_city} ({top_searches} searches)."

        session['last_category'] = top_category
        session['last_item'] = top_category
        session['last_searches'] = top_searches
        session['last_insight'] = insight_msg
        session['category_breakdown'] = []
        session['analyzed_at'] = datetime.now().strftime("%Y-%m-%d %H:%M:%S")
        session['analysis_source'] = 'CSV Upload'
        session['analysis_focus'] = 'csv'
        session['csv_filename'] = safe_name
        session['csv_rows'] = len(parsed_rows)
        session['csv_top_category'] = top_category

        flash(f"CSV data '{safe_name}' processed successfully ({len(parsed_rows)} data rows). Top trend: {top_category} in {top_city}.", "success")
        return redirect(url_for('insights_result'))

    except Exception as e:
        flash(f"Error processing CSV file: {str(e)}", "error")
        return redirect(url_for('insights'))


@app.route('/import-collection-centres', methods=['POST'])
def import_collection_centres():
    """
    Flask Collection Centre CSV Import Flow:
    1. Delimiter autodetection & BOM handling.
    2. 18-column research schema + 8-column legacy support.
    3. Optional coordinates + Gujarat bounding-box warnings.
    4. Forwards to Laravel backend API.
    5. Displays granular row-by-row warnings and error messages in result.html.
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
        file.save(save_path)
        rows, parse_errors, warnings = parse_and_validate_collection_centre_csv(save_path)

        session['last_import_warnings'] = warnings
        session['last_import_errors'] = parse_errors or []

        if parse_errors or not rows:
            err_msg = "CSV parsing failed: " + "; ".join(parse_errors[:3])
            flash(err_msg, "error")
            session['last_category'] = 'Import Error'
            session['last_item'] = 'N/A'
            session['last_searches'] = 0
            session['last_insight'] = f"Failed to parse CSV due to {len(parse_errors)} validation errors."
            session['analyzed_at'] = datetime.now().strftime("%Y-%m-%d %H:%M:%S")
            session['analysis_source'] = 'Collection Centre CSV Import'
            session['analysis_focus'] = 'import'
            return redirect(url_for('insights_result'))

        payload = json.dumps({'centres': rows}).encode('utf-8')
        api_url = f"{LARAVEL_API_BASE}/api/admin/collection-centres/import"

        secret_key = os.environ.get('SECRET_KEY', 'dev-wastewise-flask-secret-key-2026')
        req = urllib.request.Request(api_url, data=payload, headers={
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-Admin-Api-Key': secret_key,
            'User-Agent': 'WasteWise-Flask-Import/1.0',
        })

        import_summary = {}

        try:
            with urllib.request.urlopen(req, timeout=15) as response:
                resp_raw = response.read().decode('utf-8')
                resp_data = json.loads(resp_raw)

                if resp_data.get('success'):
                    inserted = resp_data.get('inserted', resp_data.get('imported', 0))
                    skipped = resp_data.get('skipped_duplicates', 0)
                    conflicts = resp_data.get('flagged_conflicts', 0)
                    import_summary = {
                        'inserted': inserted,
                        'skipped_duplicates': skipped,
                        'flagged_conflicts': conflicts,
                        'total_processed': len(rows),
                    }
                    session['last_import_summary'] = import_summary
                    flash(
                        f"Collection Centres imported successfully! "
                        f"({inserted} new centres created / inserted, {skipped} duplicates skipped, {conflicts} conflicts flagged).",
                        "success"
                    )
                else:
                    flash(f"Import failed: {resp_data.get('message', 'Validation errors occurred.')}", "error")

        except urllib.error.HTTPError as http_err:
            try:
                err_body = http_err.read().decode('utf-8')
                err_json = json.loads(err_body)
                errors_list = err_json.get('errors', [])
                backend_errs = [f"Row {e.get('row', '?')} ({e.get('field', 'field')}): {e.get('message', '')}" for e in errors_list] if isinstance(errors_list, list) else [err_json.get('message', 'Error')]
                session['last_import_errors'] = (session.get('last_import_errors') or []) + backend_errs
                flash(f"Laravel Validation Error: Total {len(backend_errs)} errors occurred.", "error")
            except Exception:
                flash(f"Laravel API returned error (HTTP {http_err.code}): {http_err.reason}", "error")

        except urllib.error.URLError as url_err:
            flash(f"Could not connect to main WasteWise application: {url_err.reason}", "error")

        # Set session details and show result.html
        session['last_category'] = 'Collection Centre Import'
        session['last_item'] = f"{len(rows)} Centres Processed"
        session['last_searches'] = len(rows)
        session['last_insight'] = f"Import completed for {safe_name}. {len(warnings)} regional warnings flagged."
        session['analyzed_at'] = datetime.now().strftime("%Y-%m-%d %H:%M:%S")
        session['analysis_source'] = 'Collection Centre CSV Import'
        session['analysis_focus'] = 'import'
        session['csv_filename'] = safe_name
        session['csv_rows'] = len(rows)
        session['csv_top_category'] = 'Collection Centres'

        return redirect(url_for('insights_result'))

    except Exception as e:
        flash(f"Error during Collection Centre import: {str(e)}", "error")
        return redirect(url_for('insights'))


@app.route('/download-sample-csv', methods=['GET'])
def download_sample_csv():
    """
    Serves sample Collection Centre CSV template with the 18-column research schema.
    """
    sample_path = os.path.join(BASE_DIR, 'sample_collection_centres.csv')
    if not os.path.exists(sample_path):
        flash("Sample CSV file not found on server.", "error")
        return redirect(url_for('insights'))

    return send_from_directory(
        BASE_DIR,
        'sample_collection_centres.csv',
        as_attachment=True,
        download_name='sample_collection_centres.csv',
        mimetype='text/csv'
    )


@app.route('/api/insights', methods=['GET', 'POST'])
def api_insights():
    """
    JSON API for Laravel's PythonAnalyticsService.
    Accepts records in POST body or query parameter and computes insights.
    """
    if request.method == 'POST':
        data = request.get_json(silent=True) or {}
        records = data.get('records', [])
    else:
        focus = request.args.get('focus', 'all')
        res = fetch_laravel_search_trends(focus=focus)
        records = res.get('records', []) if res else []

    analysis = analyze_search_records(records)
    return jsonify(analysis), 200


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

if __name__ == '__main__':
    port = int(os.environ.get('FLASK_PORT', 5000))
    app.run(host='127.0.0.1', port=port, debug=True)
