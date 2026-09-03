# WasteWise – Viva Examination Preparation Guide

This document contains comprehensive viva questions and answers tailored for the **Course 504 (Advanced Web Application Development)** final examination. It covers project fundamentals, PHP/Laravel, MySQL, Flask, microservice communication, security, testing, and common examiner questions.

---

## 🎯 Section A: Project Basics

### Q1: What is WasteWise?
- **Simple Answer**: WasteWise is a smart waste disposal assistant web application that helps citizens find proper disposal methods and nearby collection centres for different types of waste.
- **Where Used**: Main project application (`D:\tmp\htdocs\wastewise`).
- **Keywords**: Waste Disposal Assistant, Citizen Portal, Municipal Management.

### Q2: What primary problem does WasteWise solve?
- **Simple Answer**: It solves the lack of public awareness regarding proper waste sorting and disposal by providing instant search, nearest collection centre location finding, and environmental education.
- **Where Used**: Public home page (`/`) and Collection Centre Finder.
- **Keywords**: Waste Sorting, Environmental Awareness, Geolocation Finder.

---

## 🛠️ Section B: WasteWise Functionality

### Q3: How does the Collection Centre Finder determine the nearest centre?
- **Simple Answer**: It takes the user's current latitude and longitude (or selected city coordinates) and computes the distance to all collection centres using the **Haversine formula** in JavaScript/PHP.
- **Where Used**: `welcome.blade.php`, `CollectionCentreController.php`, `CollectionCentreNearestTest.php`.
- **Keywords**: Haversine Formula, Latitude/Longitude, Distance Calculation.

### Q4: What information is provided when a user searches for a waste item?
- **Simple Answer**: The item name, category (e.g. E-Waste, Plastic), hazard level, detailed disposal instructions, and nearest collection centres.
- **Where Used**: `GET /api/waste-search` and `WasteSearchController.php`.
- **Keywords**: Category, Hazard Level, Disposal Instructions.

---

## 🅰️ Section C: Laravel Framework

### Q5: Why did you choose Laravel for the backend framework?
- **Simple Answer**: Laravel provides a robust MVC architecture, elegant Eloquent ORM for database operations, built-in authentication, middleware protection, database migration tools, and comprehensive testing support out of the box.
- **Where Used**: Entire core application (`app/`, `routes/`, `database/`).
- **Keywords**: MVC Architecture, Eloquent ORM, Middleware, Routing, Artisan.

### Q6: What is the role of `artisan` in Laravel?
- **Simple Answer**: Artisan is Laravel's command-line interface used for running database migrations, seeding data, creating controllers/models, starting dev servers (`php artisan serve`), and running automated tests (`php artisan test`).
- **Where Used**: Terminal operations and build scripts.
- **Keywords**: CLI, Migrations, Seeders, Servicing, Testing.

---

## 🐘 Section D: PHP Fundamentals (Unit 1 & 2)

### Q7: How does PHP handle input validation in WasteWise?
- **Simple Answer**: Forms submit to Laravel controller actions where `$request->validate([...])` enforces data types, required fields, maximum lengths, regular expressions, and foreign key existence before processing.
- **Where Used**: All Admin CRUD Controllers and `CollectionCentreImportController.php`.
- **Keywords**: `$request->validate()`, Validation Rules, Input Sanitation.

### Q8: How does PHP communicate with the file system for CSV processing?
- **Simple Answer**: Standard PHP file functions like `fopen()`, `fgetcsv()`, and `fclose()` read uploaded CSV files line by line, parsing comma-separated rows into associative arrays.
- **Where Used**: `CollectionCentreImportController.php` and `parse_and_validate_collection_centre_csv`.
- **Keywords**: `fopen()`, `fgetcsv()`, File Stream, CSV Parsing.

---

## 🗄️ Section E: MySQL & Database Design (Unit 3)

### Q9: What are the primary entities in the WasteWise database?
- **Simple Answer**: `users`, `waste_categories`, `disposal_methods`, `waste_items`, `cities`, `collection_centres`, `search_histories`, `feedback`, `announcements`, `sustainability_tips`, and `knowledge_articles` (11 tables).
- **Where Used**: `database/migrations/` and `app/Models/`.
- **Keywords**: Relational Schema, Foreign Keys, Eloquent Models.

### Q10: How are relationships defined between tables?
- **Simple Answer**: Using Eloquent ORM relationships such as `belongsTo` and `hasMany`. For example, a `WasteItem` `belongsTo` a `WasteCategory`, and a `City` `hasMany` `CollectionCentres`.
- **Where Used**: `app/Models/WasteItem.php`, `app/Models/CollectionCentre.php`, etc.
- **Keywords**: `belongsTo`, `hasMany`, Foreign Keys, Relationships.

---

## 🔄 Section F: CRUD Operations

### Q11: What does CRUD stand for, and where is it implemented?
- **Simple Answer**: CRUD stands for Create, Read, Update, Delete. It is implemented across 10 admin management modules (Categories, Items, Cities, Collection Centres, Announcements, Tips, Knowledge Articles, Users, Feedback).
- **Where Used**: `app/Http/Controllers/Admin/` controllers and Blade views in `resources/views/admin/`.
- **Keywords**: Create, Read, Update, Delete, Admin Management.

---

## ⚡ Section G: AJAX (Asynchronous JavaScript and XML)

### Q12: Why use AJAX for waste searching instead of normal form submission?
- **Simple Answer**: AJAX allows the user to see real-time search suggestions and disposal details instantly as they type without reloading the entire web page, providing a fast and seamless user experience.
- **Where Used**: `resources/views/welcome.blade.php` calling `GET /api/waste-search`.
- **Keywords**: Asynchronous, Fetch API, Real-Time Autocomplete, No Page Reload.

---

## 🔒 Section H: Authentication & Authorization

### Q13: How does admin authorization work in WasteWise?
- **Simple Answer**: Web requests pass through `EnsureIsAdmin` middleware (`middleware('admin')`), which verifies that the user is logged in (`auth()->check()`) and has `is_admin == true`. API requests can also authenticate by passing a valid `X-API-KEY` header matching `ADMIN_API_KEY`.
- **Where Used**: `app/Http/Middleware/EnsureIsAdmin.php` and `routes/web.php` / `routes/api.php`.
- **Keywords**: Middleware, `EnsureIsAdmin`, `is_admin`, `X-API-KEY`, Dual-Mode Auth.

---

## 🔌 Section I: RESTful APIs

### Q14: What API endpoints are exposed by WasteWise?
- **Simple Answer**: 
  1. `GET /api/waste-search` (Public AJAX search)
  2. `GET /api/search-trends` (Public/service analytics data)
  3. `POST /api/admin/collection-centres/import` (Protected admin bulk import)
- **Where Used**: `routes/api.php`.
- **Keywords**: REST API, JSON Response, `X-API-KEY`, Endpoint Specifications.

---

## 🐍 Section J: PHP → Python Integration (Unit 4)

### Q15: How does PHP execute Python scripts in Unit 4?
- **Simple Answer**: `PythonAnalyticsService.php` uses PHP `proc_open()` to launch `python scripts/waste_insights.py` as a CLI subprocess, streaming JSON data via `stdin` and capturing structured analytics results from `stdout`.
- **Where Used**: `app/Services/PythonAnalyticsService.php` and `scripts/waste_insights.py`.
- **Keywords**: `proc_open()`, Subprocess Execution, Standard I/O Pipes, JSON Streaming.

---

## 🌐 Section K: Python Flask Microservice (Unit 5)

### Q16: Why use a separate Flask service alongside Laravel?
- **Simple Answer**: Flask serves as a lightweight, dedicated microservice for Python-based data processing, CSV file analysis, and analytics dashboard rendering. This demonstrates modern service-oriented architecture (SOA) where specialized tasks run on Python while the main application runs on Laravel.
- **Where Used**: `flask_service/app.py`.
- **Keywords**: Microservices, Decoupled Architecture, Specialized Data Processing.

### Q17: Why does Flask NOT connect directly to MySQL?
- **Simple Answer**: Direct database connection from Flask would create tight coupling and duplicate database access logic. Instead, Flask communicates with Laravel via REST API (`/api/search-trends`), ensuring Laravel remains the single source of truth for database operations and security.
- **Where Used**: `flask_service/app.py` calling `http://127.0.0.1:8000/api/search-trends`.
- **Keywords**: Single Source of Truth, Decoupling, API Consumption.

---

## 🎨 Section L: Jinja2 Templating Engine

### Q18: What is Jinja2 and how is it used in the Flask service?
- **Simple Answer**: Jinja2 is the default templating engine for Flask. It allows embedding Python variables (`{{ variable }}`), control structures (`{% for %}`, `{% if %}`), template inheritance (`{% extends %}`), and dynamic URL generation (`{{ url_for() }}`) into HTML files.
- **Where Used**: `flask_service/templates/index.html` and `result.html`.
- **Keywords**: Jinja2, Template Inheritance, Interpolation, `url_for()`.

---

## 🌐 Section M: HTTP Verbs & Methods

### Q19: What is the difference between GET and POST HTTP methods?
- **Simple Answer**: `GET` requests append data to the URL query string and are idempotent (used for retrieving data). `POST` requests send data in the request body, are non-idempotent, and are used for submitting forms or modifying server state.
- **Where Used**: Routes in `routes/web.php`, `routes/api.php`, and `flask_service/app.py`.
- **Keywords**: `GET`, `POST`, Query String, Request Body, Idempotency.

---

## 📂 Section N: CSV Processing & Database Integrity

### Q20: How does Collection Centre CSV import prevent duplicate entries?
- **Simple Answer**: Before inserting a row, the controller queries the `collection_centres` table for an existing record with the same `name` and `city_id`. If found, it updates the existing centre instead of creating a duplicate.
- **Where Used**: `CollectionCentreImportController.php` and `parse_and_validate_collection_centre_csv`.
- **Keywords**: Duplicate Check, `updateOrCreate`, Unique Constraints.

### Q21: How does database transaction rollback work during CSV import?
- **Simple Answer**: The entire import is wrapped in `DB::transaction(function() { ... })`. If an error occurs on any row (e.g. invalid city or bad coordinates), an exception is thrown, causing MySQL to roll back all inserted/updated rows in that batch, ensuring zero partial data corruption.
- **Where Used**: `CollectionCentreImportController.php` and `CollectionCentreImportApiTest.php`.
- **Keywords**: `DB::transaction()`, Rollback, Atomicity, Data Integrity.

---

## 🔒 Section O: Application Security

### Q22: How is Cross-Site Request Forgery (CSRF) prevented?
- **Simple Answer**: Laravel automatically generates a unique CSRF token for each user session. All HTML forms must include `@csrf`, which attaches the token as a hidden field. Laravel verifies this token on POST/PUT/DELETE requests.
- **Where Used**: All Blade form templates in `resources/views/`.
- **Keywords**: CSRF Token, `@csrf`, Session Verification, Form Security.

---

## 🏗️ Section P: Architecture & System Flow

### Q23: Trace the complete flow when an admin uploads a CSV in Flask to import into Laravel.
- **Simple Answer**:
  1. Admin uploads CSV on Flask UI (`POST /import-collection-centres`).
  2. Flask validates file extension and parses CSV rows.
  3. Flask sends JSON payload via HTTP POST to Laravel's `/api/admin/collection-centres/import` with `X-API-KEY`.
  4. Laravel's `EnsureIsAdmin` middleware validates `X-API-KEY`.
  5. Laravel executes `DB::transaction`, validating category/city IDs and updating/inserting rows into MySQL.
  6. Laravel returns JSON success response to Flask, which flashes a success message on the Flask UI.
- **Where Used**: `flask_service/app.py` ↔ `CollectionCentreImportController.php`.
- **Keywords**: End-to-End Flow, Flask Gateway, API Key Auth, DB Transaction.

---

## 🧪 Section Q: Automated Testing

### Q24: How many automated tests exist in WasteWise and what do they verify?
- **Simple Answer**: There are **149 automated tests** (122 Laravel tests + 27 Flask unit tests). They verify authentication, route access, CRUD operations, input validation, Haversine nearest centre calculations, API responses, CSV parsing, transaction rollbacks, and error handling.
- **Where Used**: `tests/Feature/` and `flask_service/test_app.py`.
- **Keywords**: 149 Tests, 122 Laravel Tests, 27 Flask Tests, 100% Pass Rate.
