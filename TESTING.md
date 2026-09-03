# WasteWise – Automated & Manual Testing Documentation

This document provides complete testing documentation for the **WasteWise** application, including test architecture, automated test suites (Laravel & Flask), execution commands, test coverage breakdowns, and manual verification checklists.

---

## 📊 Summary of Test Execution Results

| Test Suite | Framework | Total Tests | Status | Assertions / Coverage |
| :--- | :--- | :---: | :---: | :---: |
| **Laravel Application** | PHPUnit / Laravel Testing | **122** | **PASS** | 380 Assertions |
| **Flask Microservice** | Python `unittest` | **27** | **PASS** | 100% Suite Pass |
| **TOTAL SYSTEM TESTS** | **Combined** | **149** | **PASS** | **0 Failures** |

---

## 🛠️ Test Execution Commands

### 1. Execute Laravel Test Suite (112 Tests)
Run from the project root directory (`D:\tmp\htdocs\wastewise`):
```bash
php artisan test
```

### 2. Execute Flask Test Suite (27 Tests)
Run from the project root directory (`D:\tmp\htdocs\wastewise`):
```bash
.\flask_service\.venv\Scripts\python.exe -m unittest flask_service.test_app -v
```

---

## 🧪 Automated Test Suite Breakdown

### A. Laravel Test Suite Breakdown (112 Tests)

#### 1. `CollectionCentreImportApiTest.php` (8 Tests)
- `unauthenticated request blocked`: Verifies guest access to bulk import API returns `401 Unauthorized`.
- `non admin user blocked`: Verifies non-admin authenticated users receive `403 Forbidden`.
- `admin successful bulk import`: Verifies admin can upload a valid CSV payload and records are created in MySQL.
- `api key header authentication succeeds`: Verifies external clients presenting a valid `X-API-KEY` header pass authentication.
- `import fails on invalid category`: Verifies CSV row with unknown category triggers validation error and rolls back.
- `import fails on invalid city`: Verifies CSV row with unknown city triggers validation error and rolls back.
- `duplicate centre updates existing record`: Verifies uploading a centre matching existing `(name, city_id)` updates the record instead of duplicating.
- `transaction rollback when one row fails`: Verifies atomic `DB::transaction` rolls back all rows if a single row fails validation.

#### 2. `SearchTrendsApiTest.php` (6 Tests)
- `search trends returns json structure`: Verifies `/api/search-trends` returns correct JSON schema keys (`success`, `total_searches`, `focus`, `records`).
- `search trends empty database`: Verifies API handles empty database tables gracefully returning `total_searches = 0`.
- `search trends returns real records`: Verifies API formats search histories correctly.
- `search trends focus filters correctly`: Verifies filtering by category focus (`electronic`, `plastic`, etc.).
- `search trends unknown focus returns all`: Verifies unrecognized focus parameter falls back to returning all records.
- `existing waste search api not broken`: Regression check for public search API `/api/waste-search`.

#### 3. `CollectionCentreNearestTest.php` (3 Tests)
- `public home page loads and passes collection centres with coordinates`: Verifies home page passes collection centres array to Blade view.
- `centre with null coordinates loads safely without errors`: Verifies centres missing lat/lng coordinates render safely without JavaScript errors.
- `admin created collection centre immediately appears in public data`: Verifies new collection centres are immediately returned in finder.

#### 4. `AdminAnalyticsTest.php` (7 Tests)
- Verifies admin dashboard statistics calculation, report view rendering, and Unit 4 PHP → Python CLI analytics execution.

#### 5. `AdminAuthenticationTest.php` (9 Tests)
- Verifies admin login form, credential validation, admin session initialization, and middleware access checks.

#### 6. `AdminManagementTest.php` (28 Tests)
- Comprehensive CRUD test coverage for Categories, Disposal Methods, Waste Items, Cities, Collection Centres, Users, and Feedback.

#### 7. `AnnouncementTest.php` (12 Tests)
- Verifies public announcement listing, detail view, draft filtering, admin CRUD operations, and authorization rules.

#### 8. `AuthenticationAndSearchHistoryTest.php` (12 Tests)
- Verifies user registration, login, logout, password validation, authenticated search logging, and guest search execution.

#### 9. `KnowledgeArticleTest.php` (18 Tests)
- Verifies Knowledge Hub public views, slug generation, draft status handling, admin CRUD, category relationships, and navbar links.

#### 10. `SustainabilityTipTest.php` (15 Tests)
- Verifies public tips listing, tip detail view, draft protection, admin CRUD, reading time calculations, and difficulty filters.

#### 11. `ExampleTest.php` (2 Tests)
- Base application response checks.

---

### B. Flask Test Suite Breakdown (`test_app.py` - 27 Tests)

- `test_01_get_home_redirects_to_insights`: GET `/` redirects to `/insights`.
- `test_02_health_check_json`: GET `/health` returns status `200 OK` and JSON response.
- `test_03_get_insights_page`: GET `/insights` renders Jinja2 template successfully.
- `test_04_get_insights_args_prefill`: GET `/insights?prefill=electronic` pre-populates search focus form.
- `test_05_post_insights_real_data_electronic`: POST `/insights` with `focus=electronic` fetches Laravel API and computes analytics.
- `test_06_post_insights_real_data_all`: POST `/insights` with `focus=all` processes full search history dataset.
- `test_07_post_insights_empty_data`: Displays user-friendly "no data" notice when search history is empty.
- `test_08_post_insights_laravel_unavailable`: Graceful fallback notice when Laravel API is offline.
- `test_09_session_stores_real_analysis`: Flask session keys (`last_category`, `last_item`, `analysis_source`) correctly updated.
- `test_10_category_breakdown_computed`: Category breakdown percentages sum to 100%.
- `test_11_analyze_search_records_empty`: Python helper handles empty input lists without exceptions.
- `test_12_analyze_search_records_correct_top`: Python helper accurately identifies highest frequency category & item.
- `test_13_analyze_search_records_single_record`: Python helper processes single-element search logs.
- `test_14_csv_upload_valid`: POST `/insights` with valid search log CSV calculates correct metrics.
- `test_15_csv_upload_invalid_extension`: Rejects `.pdf` or `.txt` file uploads with standard error message.
- `test_16_csv_highest_search_count_surat_bugfix`: Verifies CSV parsing bugfix for multi-city search frequency metrics.
- `test_17_post_api_analyze_json_success`: POST `/api/analyze` returns JSON analytics output.
- `test_18_post_api_analyze_json_invalid`: Rejects malformed JSON requests with HTTP `400 Bad Request`.
- `test_19_demo_http_methods`: Verifies HTTP `GET`, `POST`, `HEAD`, `PUT`, `DELETE` support on `/demo-methods` route (Unit 5 requirement).
- `test_20_result_page_no_session_redirects`: Direct access to `/insights/result` without active session redirects to `/insights`.
- `test_21_parse_collection_centre_csv_helper_valid`: Helper parses valid collection centre CSV correctly.
- `test_22_parse_collection_centre_csv_helper_missing_headers`: Helper catches missing CSV headers.
- `test_23_parse_collection_centre_csv_helper_invalid_lat_lng`: Helper rejects invalid latitude/longitude values (e.g. `latitude = 999`).
- `test_24_import_collection_centres_route_success`: POST `/import-collection-centres` relays valid CSV payload to Laravel REST API with `X-API-KEY`.
- `test_25_import_collection_centres_route_laravel_validation_error`: Displays Laravel API validation errors (HTTP `422`) on Flask UI.
- `test_26_import_collection_centres_invalid_extension`: Blocks non-CSV uploads on collection centre import route.
- `test_27_download_sample_csv_route`: GET `/download-sample-csv` serves the sample CSV file.

---

## 📋 Manual Browser Verification Checklist

The following manual test cases were executed and verified in modern desktop browsers (Chrome / Edge):

| Test Scenario | Action / Steps | Expected Result | Status |
| :--- | :--- | :--- | :---: |
| **Public Waste Search** | Type "Plastic Bottle" in search bar on `/`. | Instant dropdown shows category "Plastic Waste", disposal steps, and nearest centre distance. | **PASSED** |
| **Collection Centre Finder** | Click "Use My Location" or select "Surat". | Calculates distance in kilometers using Haversine formula and lists closest centres. | **PASSED** |
| **User Registration & Login** | Fill form at `/register`, then login at `/login`. | User session created, navbar shows user name, search history saved on profile. | **PASSED** |
| **Admin Middleware Protection** | Attempt to visit `/admin` as guest or normal user. | Redirected to admin login page or shown `403 Forbidden`. | **PASSED** |
| **Admin CRUD Operation** | Log in as admin, create a new Waste Item in `/admin/waste-items`. | Item created successfully, flash message shown, immediately searchable on public portal. | **PASSED** |
| **CSV Bulk Import (Flask UI)** | Upload `sample_collection_centres.csv` at `http://127.0.0.1:5000/import-collection-centres`. | CSV validated by Flask, posted to Laravel API with `X-API-KEY`, success message displayed, centres added to MySQL. | **PASSED** |
| **CSV Bulk Import Error Rollback** | Upload CSV containing an invalid city name ("UnknownCity"). | Laravel rejects request with `422 Unprocessable Entity`, transaction rolls back, 0 records added. | **PASSED** |
| **Unit 4 Python CLI Analytics** | Click "Generate Python Report" on `/admin/analytics`. | PHP executes `scripts/waste_insights.py` via `proc_open`, JSON response rendered into Blade report table. | **PASSED** |
| **Unit 5 Flask Insights** | Navigate to `http://127.0.0.1:5000/insights`, select "Electronic Waste". | Fetches `/api/search-trends?focus=electronic` from Laravel, calculates top item and metrics in Flask UI. | **PASSED** |
