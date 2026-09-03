# WasteWise – Smart Waste Disposal Assistant

[![Laravel](https://img.shields.io/badge/Laravel-11.x-FF2D20?style=for-the-badge&logo=laravel)](https://laravel.com)
[![Python](https://img.shields.io/badge/Python-3.x-3776AB?style=for-the-badge&logo=python)](https://python.org)
[![Flask](https://img.shields.io/badge/Flask-3.1-000000?style=for-the-badge&logo=flask)](https://flask.palletsprojects.com)
[![MySQL](https://img.shields.io/badge/MySQL-8.0-4479A1?style=for-the-badge&logo=mysql)](https://mysql.com)
[![Bootstrap](https://img.shields.io/badge/Bootstrap-5.3-7952B3?style=for-the-badge&logo=bootstrap)](https://getbootstrap.com)

**WasteWise** is a full-stack web application designed to help citizens dispose of household and municipal waste responsibly. It provides instant waste item lookup, nearest collection centre geolocation recommendations, an educational Knowledge Hub, daily sustainability tips, public announcements, and a comprehensive administration dashboard. 

The project features a **hybrid PHP (Laravel) + Python (Flask)** microservices architecture created for **Course 504 (Advanced Web Application Development)**, demonstrating full-stack web capabilities, RESTful API design, database management, and cross-technology integration.

---

## 📋 Table of Contents
- [Problem Statement & Objectives](#-problem-statement--objectives)
- [Key Features](#-key-features)
- [System Architecture & Integration](#-system-architecture--integration)
- [Technology Stack](#-technology-stack)
- [Course 504 Syllabus Integration](#-course-504-syllabus-integration)
- [Directory Structure](#-directory-structure)
- [Installation & Local Setup](#-installation--local-setup)
- [Running the Services](#-running-the-services)
- [Running Test Suites](#-running-test-suites)
- [API Overview](#-api-overview)
- [Security Features](#-security-features)
- [Future Scope](#-future-scope)

---

## 🎯 Problem Statement & Objectives

### Problem Statement
Improper household waste disposal leads to severe environmental contamination, land pollution, and health hazards. Citizens often lack clear guidance on how specific waste items (e.g. e-waste, hazardous chemicals, recyclables, organic waste) should be sorted, treated, and disposed of locally.

### Objectives
1. Provide an **instant search engine** mapping waste items to appropriate disposal methods and nearby collection facilities.
2. Provide **geolocation-based routing** to calculate distances to the closest collection centres.
3. Educate the public through a curated **Knowledge Hub**, **Sustainability Tips**, and **Community Announcements**.
4. Enable municipal administrators to manage items, categories, cities, centres, and users effortlessly.
5. Demonstrate **interoperability between PHP (Laravel) and Python (Flask)** using CLI subprocesses and RESTful microservices.

---

## 🚀 Key Features

### 👤 Citizen / Public User Experience
- **Instant Waste Lookup (AJAX)**: Search waste items dynamically with real-time suggestions, category tags, and detailed disposal instructions.
- **Collection Centre Finder**: Enter coordinates or city to find the nearest collection centre calculated via the Haversine formula.
- **Knowledge Hub**: Browse categorized articles detailing recycling practices and hazardous waste management.
- **Sustainability Tips**: Discover daily actionable eco-friendly tips categorized by difficulty level and reading time.
- **Public Announcements**: Stay updated on local e-waste drives, recycling campaigns, and collection notices.
- **User Authentication & Search History**: Optional registration allowing users to track their search history over time.
- **Public Feedback System**: Submit queries, reports, or feedback directly to municipal administrators.

### 🛡️ Admin Dashboard (`/admin`)
- **Complete CRUD Modules**: Manage Waste Categories, Disposal Methods, Waste Items, Cities, Collection Centres, Announcements, Sustainability Tips, Knowledge Articles, Users, and Feedback.
- **Waste Search Analytics**: Monitor live search trends, total searches, top-searched categories, and top-searched items.
- **Collection Centre Bulk CSV Import**: Upload collection centre records in CSV format with automatic field validation, category/city existence verification, coordinate bounds checks, duplicate updates, and atomic database transaction rollbacks.
- **Python CLI Analytics Execution**: Trigger Python background analysis scripts directly from the Laravel admin interface.

### 🐍 Standalone Flask Insights Service (`flask_service/`)
- **RESTful Analytics Integration**: Consumes Laravel API endpoints (`GET /api/search-trends`) to compute search pattern metrics and breakdown percentages.
- **Interactive Web Interface**: Jinja2-rendered portal allowing administrators to analyze search histories or upload custom CSV datasets.
- **Bulk Import API Gateway**: Process collection centre CSV files via Flask UI and securely post validated payloads to Laravel's protected REST API using `X-API-KEY` authentication.

---

## 🏗️ System Architecture & Integration

WasteWise follows a decoupled, service-oriented architecture:

```
+-------------------------------------------------------------------------+
|                              CLIENT BROWSER                             |
+------------------------------------+------------------------------------+
                                     |
           +-------------------------+-------------------------+
           | (HTTP Requests)                                   | (HTTP Requests)
           v                                                   v
+-------------------------------+               +-------------------------------+
|     Laravel Application       |               |     Python Flask Service      |
|        (Port 8000)            |               |        (Port 5000)            |
|  - Web UI & Admin Dashboard   |               |  - Search Analytics Portal    |
|  - REST APIs                  |               |  - CSV File Analyzer          |
|  - Business Logic & Auth      |               |  - CSV Import Interface       |
+---------------+---------------+               +---------------+---------------+
                |                                               |
                | (Subprocess CLI - Unit 4)                     | (REST API Requests - Unit 5)
                v                                               v
+-------------------------------+               +-------------------------------+
|   scripts/waste_insights.py   |               |   GET  /api/search-trends     |
|   (Python Executable Script)  |               |   POST /api/admin/.../import  |
+---------------+---------------+               +---------------+---------------+
                |                                               |
                +-----------------------+-----------------------+
                                        |
                                        v
                        +-------------------------------+
                        |        MySQL Database         |
                        |      (Database Storage)       |
                        +-------------------------------+
```

### Integration Mechanisms
1. **PHP → Python Integration (Unit 4)**: `PythonAnalyticsService.php` uses PHP `proc_open` to execute `scripts/waste_insights.py`, passing JSON arguments via standard input and reading analytical outputs via standard output.
2. **Flask → Laravel Integration (Unit 5)**: Flask service (`app.py`) communicates with Laravel exclusively over HTTP REST APIs (`http://127.0.0.1:8000/api/...`). Flask does **not** connect directly to MySQL, keeping backend responsibilities separated.

---

## 💻 Technology Stack

| Domain | Technologies |
| :--- | :--- |
| **Backend Framework** | Laravel 11.x (PHP 8.2+) |
| **Microservice Framework** | Flask 3.1.x (Python 3.10+) |
| **Database** | MySQL 8.0 / MariaDB |
| **Frontend** | Blade Templates, Bootstrap 5.3, Vanilla JavaScript, CSS3 |
| **Asynchronous Requests** | Fetch API / Modern AJAX |
| **CLI & Scripting** | Python 3 (CLI Process Execution) |
| **Testing Frameworks** | PHPUnit / Laravel Test Suite, Python `unittest` |

---

## 📚 Course 504 Syllabus Integration

WasteWise demonstrates complete coverage of the Course 504 curriculum:

- **Unit 1 (PHP Fundamentals)**: Variables, data types, conditional branching, associative arrays, loops, and custom sorting algorithms (`usort`, `array_multisort`).
- **Unit 2 (PHP Advanced)**: Form handling (`GET`/`POST`), file I/O (`fopen`, `fgetcsv`), cookie/session management, input validation, JSON encoding, OOP controllers/services, regular expressions (`preg_match`), and exception handling with `try-catch`.
- **Unit 3 (Database & AJAX)**: MySQL relational schema, Eloquent ORM, complex `SELECT`/`WHERE`/`ORDER BY` queries, atomic database transactions (`DB::transaction`), and real-time backend AJAX search.
- **Unit 4 (PHP-Python Integration)**: Subprocess execution via `proc_open` in `PythonAnalyticsService.php`, command argument escaping, standard I/O streaming, and Python `os`/`csv` operations.
- **Unit 5 (Flask Service)**: Standalone WSGI application setup, Jinja2 template inheritance, `@app.route` mapping, full HTTP method support (`GET`, `POST`, `HEAD`, `PUT`, `DELETE`), `url_for`, `request.form`, `request.args`, `request.files`, session management, secure file uploads, and API consumption.

---

## 📁 Directory Structure

```text
wastewise/
├── app/
│   ├── Http/
│   │   ├── Controllers/       # Laravel Web & API Controllers
│   │   │   ├── Admin/         # Admin CRUD & Analytics Controllers
│   │   │   └── Api/           # REST API Controllers
│   │   └── Middleware/        # Admin authorization middleware (EnsureIsAdmin)
│   ├── Models/                # Eloquent ORM Models (11 models)
│   └── Services/              # PythonAnalyticsService (PHP -> Python CLI)
├── config/                    # Application & security configuration
├── database/
│   ├── migrations/            # Database schema migration files
│   └── seeders/               # Database seeders (Admin, Cities, Categories, etc.)
├── docs/                      # Technical documentation files
├── flask_service/             # Standalone Python Flask Microservice (Unit 5)
│   ├── static/                # Flask CSS & assets
│   ├── templates/             # Jinja2 templates (index.html, result.html)
│   ├── uploads/               # Temporary CSV upload directory
│   ├── app.py                 # Core Flask application
│   ├── test_app.py            # Flask unittest suite (27 tests)
│   └── requirements.txt       # Flask dependencies
├── public/                    # Web root (index.php, favicons)
├── resources/
│   └── views/                 # Blade HTML templates (Public & Admin views)
├── routes/
│   ├── api.php                # REST API routes
│   └── web.php                # Web application routes
├── scripts/                   # Standalone Python CLI analytics scripts (Unit 4)
│   └── waste_insights.py      # Python analytics runner
├── tests/                     # Laravel automated test suite (112 tests)
│   └── Feature/               # Feature & integration test cases
├── README.md                  # Main GitHub documentation
└── phpunit.xml                # PHPUnit configuration
```

---

## ⚙️ Installation & Local Setup

### Prerequisites
- PHP 8.2 or higher
- Composer
- Python 3.10 or higher
- MySQL / MariaDB Server (e.g. via XAMPP)

### 1. Clone & Configure Laravel
```bash
cd D:\tmp\htdocs\wastewise

# Install PHP dependencies
composer install

# Copy environment template
copy .env.example .env

# Generate application key
php artisan key:generate
```

### 2. Configure Environment `.env`
Update `.env` with your database credentials and admin API key:
```ini
APP_NAME=WasteWise
APP_ENV=local
APP_URL=http://127.0.0.1:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=wastewise
DB_USERNAME=root
DB_PASSWORD=

ADMIN_API_KEY=dev-wastewise-admin-key-2026
```

### 3. Run Database Migrations & Seeders
```bash
php artisan migrate:fresh --seed
```

### 4. Configure Flask Microservice Environment
```bash
cd flask_service

# Create Python virtual environment
python -m venv .venv

# Activate virtual environment (Windows PowerShell)
.\.venv\Scripts\Activate.ps1

# Install Flask dependencies
pip install -r requirements.txt
```

---

## 🏃 Running the Services

To run WasteWise locally, start both the Laravel server and the Flask service:

### Terminal 1: Laravel Web Application
```bash
cd D:\tmp\htdocs\wastewise
php artisan serve
```
*Access Laravel Web Portal at:* `http://127.0.0.1:8000`

### Terminal 2: Flask Insights Service
```bash
cd D:\tmp\htdocs\wastewise\flask_service
.\.venv\Scripts\python.exe app.py
```
*Access Flask Service Portal at:* `http://127.0.0.1:5000`

---

## 🧪 Running Test Suites

WasteWise includes **149 automated tests** verifying feature functionality, authorization, validation, and cross-service communication.

### Run Laravel Tests (122 Tests)
```bash
cd D:\tmp\htdocs\wastewise
php artisan test
```

### Run Flask Tests (27 Tests)
```bash
cd D:\tmp\htdocs\wastewise
.\flask_service\.venv\Scripts\python.exe -m unittest flask_service.test_app -v
```

---

## 🔌 API Overview

| Endpoint | Method | Auth | Description |
| :--- | :---: | :---: | :--- |
| `/api/waste-search` | `GET` | Public | Live AJAX waste item search returning categories, items, and disposal steps. |
| `/api/search-trends` | `GET` | Public | Search history analytics data consumed by Flask and admin dashboards. |
| `/api/admin/collection-centres/import` | `POST` | `X-API-KEY` / Admin | Protected bulk collection centre CSV import with validation & transaction rollback. |

---

## 🔒 Security Features

1. **CSRF Protection**: All Blade forms implement `@csrf` token validation.
2. **Password Hashing**: Passwords stored using standard `bcrypt` via Laravel `Hash::make()`.
3. **Admin Middleware**: Web routes (`/admin/*`) and API routes (`/api/admin/*`) require administrator privileges or valid `X-API-KEY` headers.
4. **Input & CSV Sanitation**: Uploaded CSV files undergo file extension checks, mime type verification, header validation, coordinate bounds checking, and HTML entity escaping.
5. **Database Transaction Safety**: CSV bulk imports are wrapped in `DB::transaction()` blocks to prevent partial/corrupted imports.

---

## 🔮 Future Scope

- **Mobile Application**: Native mobile application (Flutter / React Native) consuming Laravel APIs.
- **GIS Map Visualization**: Interactive OpenStreetMap / Google Maps integration for collection centre navigation.
- **IoT Smart Bin Integration**: Real-time capacity monitoring for collection centres.
- **AI Image Classification**: Computer vision model to identify waste items directly from smartphone camera photos.

---

## 📄 License & Credits
Developed as part of **Course 504: Advanced Web Application Development**. Built with Laravel, Python, Flask, and Bootstrap.
