# WasteWise Python Insights — Standalone Flask Service

A standalone lightweight Python Flask service providing search trend analytics and JSON API endpoints for WasteWise.

## Requirements
- Python 3.10+
- Flask 3.1.2
- Werkzeug 3.1.0+

## Local Execution Instructions

1. Navigate to the service directory:
   ```bash
   cd flask_service
   ```

2. (Optional) Create and activate a Python virtual environment:
   ```bash
   python -m venv venv
   # On Windows PowerShell:
   .\venv\Scripts\Activate.ps1
   ```

3. Install dependencies:
   ```bash
   pip install -r requirements.txt
   ```

4. Run the Flask development server:
   ```bash
   python app.py
   ```

5. Access the service in your web browser at:
   `http://127.0.0.1:5000`

## Service Endpoints
- `GET /`: Home route (redirects to `/insights`)
- `GET /insights`: Interactive Waste Insight Analyzer UI
- `POST /insights`: Process insight analysis form
- `GET /insights/result`: Render result view with session data
- `POST /upload-csv`: Controlled CSV file upload
- `GET /health`: JSON health check (registered via `add_url_rule`)
- `POST /api/analyze`: JSON analysis API endpoint
- `GET|HEAD|PUT|DELETE /api/demo-methods`: HTTP method demonstration
