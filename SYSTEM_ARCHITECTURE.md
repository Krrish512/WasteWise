# WasteWise – System Architecture Documentation

This document describes the high-level system architecture, service integration, request processing flows, data pipelines, and architectural principles implemented in **WasteWise**.

---

## 🏗️ 1. High-Level Architecture Overview

WasteWise uses a **decoupled, service-oriented architecture (SOA)** combining a primary **PHP / Laravel 11** core web application with a secondary **Python / Flask 3.1** microservice.

```mermaid
graph TD
    Client[Browser / End User] -->|HTTP GET/POST| Laravel[Laravel Web Application Port 8000]
    Client -->|HTTP GET/POST| Flask[Python Flask Service Port 5000]

    subgraph Laravel Ecosystem
        Laravel -->|Eloquent ORM| MySQL[(MySQL Database)]
        Laravel -->|Subprocess proc_open - Unit 4| PythonCLI[scripts/waste_insights.py]
    end

    subgraph Flask Ecosystem
        Flask -->|REST API HTTP GET/POST - Unit 5| LaravelAPI[Laravel REST API /api/...]
    end

    LaravelAPI -->|Query / Transaction| MySQL
```

### Key Architectural Principles
1. **Single Source of Truth**: The MySQL database is owned and managed exclusively by the Laravel core application.
2. **No Direct Microservice Database Access**: Flask does **not** connect directly to MySQL; it interacts with data solely through HTTP REST APIs exposed by Laravel.
3. **Decoupled Responsibilities**:
   - **Laravel**: Handles UI rendering, user auth, business logic, CRUD management, API endpoints, and database transactions.
   - **Flask**: Handles specialized data analysis, custom CSV processing, interactive trend reporting, and CSV import staging.

---

## 🔁 2. Component Integration Flows

### Flow A: PHP → Python CLI Integration (Course 504 - Unit 4)
When an administrator triggers a Python analytics report from the Laravel Admin Dashboard:

```mermaid
sequenceDiagram
    autonumber
    actor Admin
    participant Dashboard as Admin Controller
    participant Service as PythonAnalyticsService
    participant Python as scripts/waste_insights.py
    participant DB as MySQL Database

    Admin->>Dashboard: Click "Generate Python Report"
    Dashboard->>DB: Query Search History records
    DB-->>Dashboard: Return search log collection
    Dashboard->>Service: Pass records array to runReport()
    Service->>Python: Launch proc_open("python waste_insights.py")
    Service->>Python: Pipe JSON payload via stdin
    Python->>Python: Compute statistics & category breakdown
    Python-->>Service: Return JSON results via stdout
    Service-->>Dashboard: Parse JSON array
    Dashboard-->>Admin: Render Blade Report View
```

---

### Flow B: Flask → Laravel REST API Integration (Course 504 - Unit 5)
When an administrator accesses the Flask Insights portal to inspect search trends:

```mermaid
sequenceDiagram
    autonumber
    actor Admin
    participant Flask as Flask Service (Port 5000)
    participant API as Laravel REST API (Port 8000)
    participant DB as MySQL Database

    Admin->>Flask: GET /insights?focus=electronic
    Flask->>API: HTTP GET /api/search-trends?focus=electronic
    API->>DB: Query SearchHistory filtering by category
    DB-->>API: Return Eloquent collection
    API-->>Flask: HTTP 200 OK JSON response
    Flask->>Flask: Jinja2 renders index.html template
    Flask-->>Admin: Display trend metrics & category breakdown
```

---

### Flow C: Collection Centre CSV Bulk Import Pipeline
When an administrator uploads a CSV file through the Flask Service interface to import new collection centres into Laravel:

```mermaid
sequenceDiagram
    autonumber
    actor Admin
    participant Flask as Flask Service (Port 5000)
    participant API as Laravel Import API
    participant Middleware as EnsureIsAdmin Middleware
    participant Controller as CollectionCentreImportController
    participant DB as MySQL Database

    Admin->>Flask: POST /import-collection-centres (Upload CSV)
    Flask->>Flask: Parse CSV rows & validate headers
    Flask->>API: POST /api/admin/collection-centres/import<br/>(Header: X-API-KEY, Body: JSON array)
    API->>Middleware: Validate X-API-KEY header
    Middleware->>Controller: Forward request
    Controller->>DB: Begin DB::transaction()
    loop For Each Row in CSV
        Controller->>DB: Validate Category & City exist
        Controller->>DB: Check for duplicate (name + city_id)
        alt Existing Record
            Controller->>DB: UPDATE collection_centre
        else New Record
            Controller->>DB: INSERT INTO collection_centres
        end
    end
    alt All Rows Valid
        Controller->>DB: Commit Transaction
        Controller-->>Flask: HTTP 200 OK {success: true, imported: N}
        Flask-->>Admin: Flash Success Message
    else Any Row Invalid
        Controller->>DB: Rollback Transaction
        Controller-->>Flask: HTTP 422 Unprocessable Entity {errors: [...]}
        Flask-->>Admin: Render Errors on Flask UI
    end
```

---

## 🔍 3. Core Application Workflows

### 1. Citizen Live Waste Search Flow (AJAX)
```mermaid
graph LR
    User((Citizen)) -->|Types query in input| JS[welcome.blade.php JavaScript]
    JS -->|Fetch API GET /api/waste-search?q=item| API[WasteSearchController]
    API -->|Query| DB[(MySQL: waste_items)]
    DB -->|Item + Category + Disposal| API
    API -->|JSON Response| JS
    JS -->|Update DOM| Display[Display Disposal Steps & Distance]
    API -->|Async Record| History[(search_histories Table)]
```

### 2. Geolocation Finder Flow (Haversine Distance)
1. User provides coordinates `(user_lat, user_lng)` or selects a city.
2. Client JavaScript fetches collection centres from backend payload.
3. Client executes Haversine distance formula for each centre:
   $$d = 2r \arcsin\left(\sqrt{\sin^2\left(\frac{\Delta \phi}{2}\right) + \cos(\phi_1)\cos(\phi_2)\sin^2\left(\frac{\Delta \lambda}{2}\right)}\right)$$
4. Sorts centres by distance (km) and updates UI listing nearest facilities.

---

## 🛡️ 4. Security Architecture

1. **Web Authentication**: Session-based auth using encrypted cookies and Laravel `Auth` guard.
2. **API Authentication**: Dual-mode protection in `EnsureIsAdmin.php`:
   - Checks active web admin session (`auth()->check() && auth()->user()->is_admin`).
   - OR verifies request header `X-API-KEY` matches `config('app.admin_api_key')`.
3. **Database Transaction Isolation**: Ensures all bulk imports complete atomically using MySQL InnoDB transactions (`DB::transaction`).
4. **Input Sanitation & Escaping**:
   - Blade templates use double curly braces `{{ $value }}` for automatic HTML entity escaping.
   - SQL queries use PDO parameter bindings to eliminate SQL Injection risks.
