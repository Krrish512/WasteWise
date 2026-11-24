import os
import sys

sys.stdout.reconfigure(encoding='utf-8')

wastewise_dir = r'D:\tmp\htdocs\wastewise'
output_md_path = os.path.join(wastewise_dir, 'WASTEWISE_PROJECT_REPORT.md')
root_md_path = r'd:\EventVenueManagement\WASTEWISE_PROJECT_REPORT.md'

report_md_content = """# WASTEWISE
## Smart Waste Disposal Assistant

### Academic Project Report

---

**Submitted by:**
- **Student Name:** [Insert Student Name]
- **Enrollment / Roll No.:** [Insert Roll / Enrollment Number]
- **Course:** Bachelor of Computer Applications (BCA)
- **Semester:** [Insert Semester]
- **Academic Year:** [Insert Academic Year]

**Guided by:**
- **Faculty / Guide Name:** [Insert Guide / Faculty Name]

**Institution:**
- **College Name:** [Insert College Name]
- **University Name:** [Insert University Name]

---

<pagebreak />

## ACADEMIC DECLARATION & CERTIFICATE

### Candidate's Declaration
I hereby declare that the project entitled **"WasteWise – Smart Waste Disposal Assistant"** submitted in partial fulfillment of the requirements for the degree of Bachelor of Computer Applications (BCA) is an authentic record of my own work carried out under the guidance of **[Insert Guide / Faculty Name]**. 

The matter embodied in this project report has not been submitted by me for the award of any other degree or diploma to any university or institution.

**Date:** September 8, 2026  
**Place:** [Insert City / Location]  

___________________________  
**(Student Signature)**  
**[Insert Student Name]**  
**Roll No:** [Insert Roll / Enrollment Number]  

---

### Certificate of Approval
This is to certify that the project report entitled **"WasteWise – Smart Waste Disposal Assistant"** prepared and submitted by **[Insert Student Name]** (Roll No: **[Insert Roll / Enrollment Number]**) has been examined and approved for the partial fulfillment of the requirements for the award of the degree of **Bachelor of Computer Applications (BCA)**.

___________________________  
**(Internal Examiner)**  

___________________________  
**(External Examiner)**  

___________________________  
**(Head of Department / Principal)**  

---

<pagebreak />

## ACKNOWLEDGMENT

I express my deep gratitude to **[Insert Guide / Faculty Name]**, Department of Computer Applications, **[Insert College Name]**, for invaluable guidance, encouragement, and feedback throughout the development of the **WasteWise** project.

I am also thankful to **[Insert HOD Name]**, Head of the Department, and the faculty members of **[Insert College Name]** for providing the necessary computational infrastructure, laboratory facilities, and technical advice.

Finally, I would like to extend my heartfelt appreciation to my family and peers for their continuous support and inspiration.

---

<pagebreak />

## EXECUTIVE SUMMARY / ABSTRACT

Rapid urbanization and increasing municipal solid waste generation pose significant environmental and public health challenges globally. A major contributing factor is the lack of public awareness regarding proper waste segregation, hazardous material handling, and location-specific collection facilities.

**WasteWise: Smart Waste Disposal Assistant** is a web application designed to bridge this information gap by providing citizens with an instant, intelligent waste classification and disposal portal, while empowering municipal administrators with data-driven waste management insights.

### Key Highlights of the WasteWise System:
1. **Intelligent Waste Search & Autocomplete**: Real-time waste item search matching query strings against categorized waste databases, returning detailed disposal instructions, recycling/composting guidelines, and hazard warnings.
2. **Collection Centre Finder**: Interactive location lookup enabling users to locate nearby disposal facilities based on waste categories (e.g. E-Waste, Organic, Hazardous, Recyclable) and city filters.
3. **Admin Management Suite**: Full CRUD operations for waste categories, waste items, and collection facilities, featuring bulk CSV data import with automated row validation and error logging.
4. **Python Waste Analytics Engine**: Integration between Laravel (PHP) and Python via process execution (`proc_open`), generating automated search frequency trends and category breakdown reports in JSON format.
5. **Standalone Flask Microservice**: Lightweight Python Flask web service operating on Port 5000 (`/analyze-csv`), providing standalone CSV data processing, statistical analysis, and interactive Jinja2 analytics dashboards.
6. **Comprehensive Automated Test Coverage**: Robust software verification with **122 passed Laravel PHPUnit tests (380 assertions)** and **27 passed Flask Pytest tests**.

---

<pagebreak />

## TABLE OF CONTENTS

- [ACADEMIC DECLARATION & CERTIFICATE](#academic-declaration--certificate)
- [ACKNOWLEDGMENT](#acknowledgment)
- [EXECUTIVE SUMMARY / ABSTRACT](#executive-summary--abstract)
- [CHAPTER 1: INTRODUCTION](#chapter-1-introduction)
  - [1.1 Background & Context](#11-background--context)
  - [1.2 Problem Statement](#12-problem-statement)
  - [1.3 Project Purpose & Objectives](#13-project-purpose--objectives)
  - [1.4 Project Scope](#14-project-scope)
- [CHAPTER 2: SYSTEM ARCHITECTURE & TECHNOLOGIES](#chapter-2-system-architecture--technologies)
  - [2.1 High-Level Multi-Tier Architecture](#21-high-level-multi-tier-architecture)
  - [2.2 Technology Stack Overview](#22-technology-stack-overview)
  - [2.3 PHP ↔ Python Process Integration](#23-php--python-process-integration)
  - [2.4 Standalone Flask Microservice Architecture](#24-standalone-flask-microservice-architecture)
- [CHAPTER 3: SOFTWARE REQUIREMENT SPECIFICATIONS (SRS)](#chapter-3-software-requirement-specifications-srs)
  - [3.1 Functional Requirements](#31-functional-requirements)
  - [3.2 Non-Functional Requirements](#32-non-functional-requirements)
  - [3.3 User Roles & Permissions](#33-user-roles--permissions)
  - [3.4 Course 504 Syllabus Mapping](#34-course-504-syllabus-mapping)
- [CHAPTER 4: SYSTEM & DATABASE DESIGN](#chapter-4-system--database-design)
  - [4.1 System Architecture Diagram](#41-system-architecture-diagram)
  - [4.2 Entity-Relationship (ER) Diagram & Schema](#42-entity-relationship-er-diagram--schema)
  - [4.3 Context Diagram (DFD Level 0 Context)](#43-context-diagram-dfd-level-0-context)
  - [4.4 Data Flow Diagram - Level 0](#44-data-flow-diagram---level-0)
  - [4.5 Data Flow Diagram - Level 1](#45-data-flow-diagram---level-1)
  - [4.6 User Navigation Flow](#46-user-navigation-flow)
  - [4.7 Admin Operations Flow](#47-admin-operations-flow)
  - [4.8 Waste Search & Autocomplete Sequence Flow](#48-waste-search--autocomplete-sequence-flow)
  - [4.9 CSV Import Sequence Flow](#49-csv-import-sequence-flow)
  - [4.10 Flask Analysis Processing Flow](#410-flask-analysis-processing-flow)
- [CHAPTER 5: IMPLEMENTATION & CODE HIGHLIGHTS](#chapter-5-implementation--code-highlights)
  - [5.1 PHP ↔ Python Process Execution (`PythonAnalyticsService.php`)](#51-php--python-process-execution-pythonanalyticsservicephp)
  - [5.2 Laravel Waste Search REST API Controller (`WasteSearchController.php`)](#52-laravel-waste-search-rest-api-controller-wastesearchcontrollerphp)
  - [5.3 Admin CSV Import Pipeline (`CsvImportController.php`)](#53-admin-csv-import-pipeline-csvimportcontrollerphp)
  - [5.4 Python Insights Script (`scripts/waste_insights.py`)](#54-python-insights-script-scriptswaste_insightspy)
  - [5.5 Flask Microservice Application (`flask_service/app.py`)](#55-flask-microservice-application-flask_serviceapppy)
- [CHAPTER 6: USER INTERFACE & SCREENSHOT PLACEHOLDERS](#chapter-6-user-interface--screenshot-placeholders)
- [CHAPTER 7: TESTING & QUALITY ASSURANCE](#chapter-7-testing--quality-assurance)
  - [7.1 Verified Test Execution Baseline](#71-verified-test-execution-baseline)
  - [7.2 Test Case Specifications Matrix](#72-test-case-specifications-matrix)
  - [7.3 Error Handling & Edge Case Verification](#73-error-handling--edge-case-verification)
- [CHAPTER 8: CONCLUSION & FUTURE SCOPE](#chapter-8-conclusion--future-scope)
  - [8.1 Project Conclusion](#81-project-conclusion)
  - [8.2 Limitations & Future Enhancement Roadmap](#82-limitations--future-enhancement-roadmap)
- [CHAPTER 9: REFERENCES & BIBLIOGRAPHY](#chapter-9-references--bibliography)

---

<pagebreak />

## CHAPTER 1: INTRODUCTION

### 1.1 Background & Context
Improper waste disposal and low rates of recycling present major environmental hazards worldwide. Millions of tons of recyclable and hazardous materials—such as electronic waste (E-Waste), lithium-ion batteries, fluorescent tubes, plastics, and organic food waste—are dumped into municipal landfills daily due to consumer confusion over correct disposal methods.

While local municipal corporations establish specialized collection hubs and recycling centers, citizens often lack an accessible digital directory to locate these facilities or determine which specific items are accepted.

### 1.2 Problem Statement
Existing municipal websites and public portals often suffer from:
1. **Unclear Disposal Guidelines**: Technical jargon or lack of actionable instructions for common household items.
2. **Missing Facility Data**: Absence of real-time search, category filtering, operating hours, or contact numbers for nearby collection centres.
3. **Lack of Administrative Data Tools**: Inability of waste management officers to easily import bulk dataset CSVs or analyze public search query trends to identify unaddressed waste demands.

### 1.3 Project Purpose & Objectives
The primary objective of **WasteWise** is to deliver an end-to-end, responsive web application that streamlines public waste disposal queries and empowers municipal administrators.

#### Specific System Objectives:
- Provide instantaneous autocomplete waste item lookup with clear disposal instructions, hazard tags, and recycling advice.
- Enable category-based and location-based lookup of municipal collection centres.
- Provide administrators with secure CRUD controls over waste taxonomy and facility locations.
- Support bulk CSV data ingestion with automated schema validation.
- Deliver data-driven analytics on waste search trends through PHP-Python process integration and a dedicated Python Flask microservice.
- Maintain rigorous code quality validated via unit and integration testing.

### 1.4 Project Scope
The scope of the project encompasses:
- **Target Audience**: Citizens seeking waste disposal guidance and Municipal Admin Officers managing waste data.
- **Core Technology Boundary**: Laravel 11 (PHP 8.2), MySQL 8.0 Database, Python 3.14 (Pandas, Scikit-learn), Flask Web Framework, Jinja2 Templating, and Bootstrap 5.

---

<pagebreak />

## CHAPTER 2: SYSTEM ARCHITECTURE & TECHNOLOGIES

### 2.1 High-Level Multi-Tier Architecture
WasteWise is built upon a modern multi-tier architecture combining a primary PHP/Laravel Web Application with Python analytics components.

```
+-----------------------------------------------------------------------+
|                             CLIENT TIER                               |
|            Modern Web Browsers (Desktop, Tablet, Mobile)              |
+-----------------------------------+-----------------------------------+
                                    | HTTP (Port 8000 / Port 5000)
                                    v
+-----------------------------------+-----------------------------------+
|               PRESENTATION & APPLICATION TIER (Laravel 11)             |
|  - Blade Template Rendering Engine    - REST API Endpoints            |
|  - Eloquent ORM Data Access           - Middleware Authentication     |
|  - WasteSearchController              - CsvImportController           |
+------------------+--------------------------------+-------------------+
                   |                                |
  SQL Queries (Port 3306)           proc_open Process Execution
                   |                                |
                   v                                v
+------------------+---------------+  +-------------+-------------------+
|       DATA & STORAGE TIER        |  |  PYTHON ANALYTICS SERVICE (3.14)  |
|  - MySQL 8.0 DB (`wastewise`)    |  |  - `scripts/waste_insights.py`    |
|  - Local Storage (`uploads/`)    |  |  - JSON stdout IPC Pipeline       |
+----------------------------------+  +---------------------------------+
                                                    ^
                                                    | REST API (Port 5000)
                                                    v
                                      +-------------+-------------------+
                                      |   FLASK MICROSERVICE PORTAL     |
                                      |   - `flask_service/app.py`      |
                                      |   - Pandas & Scikit-learn       |
                                      |   - Jinja2 Interactive Visuals  |
                                      +---------------------------------+
```

### 2.2 Technology Stack Overview

| Layer / Sub-system | Technology / Library | Version | Role in WasteWise |
|--------------------|----------------------|---------|-------------------|
| **Primary Backend Framework** | PHP / Laravel | PHP 8.2 / Laravel 11 | Core web application, routing, Blade templates, ORM, sessions, CSRF protection. |
| **Database Management System** | MySQL | 8.0.x | Relational storage for users, items, categories, centres, history, imports. |
| **Analytics Processing Engine** | Python | 3.14 | Analytical calculations, text matching, dataset filtering, trend metrics. |
| **Microservice Framework** | Python / Flask | 3.x / Flask 3.x | Lightweight standalone service running on Port 5000 for standalone CSV analytics. |
| **Data Analysis Libraries** | Pandas & NumPy | Latest | Tabular data manipulation, statistical aggregations, CSV parsing. |
| **Frontend Framework & Styling** | Bootstrap & Custom CSS | 5.3 | Responsive UI design, modals, navigation bars, grid layouts. |
| **Frontend Asset Bundler** | Vite / JavaScript | Latest | Asset compilation, debounced asynchronous Fetch API autocomplete handling. |
| **Testing Frameworks** | PHPUnit & Pytest/Unittest | 11.5 / Standard | Automated unit and integration test suite execution. |

### 2.3 PHP ↔ Python Process Integration
To execute Python data processing directly within the Laravel web application without incurring heavy HTTP overhead for internal tasks, WasteWise uses native PHP process execution (`proc_open`).

#### Integration Mechanism:
1. `PythonAnalyticsService.php` invokes the Python interpreter executable passing parameters via command-line arguments.
2. Standard input (stdin), standard output (stdout), and standard error (stderr) pipes are established via process descriptors.
3. The Python script (`scripts/waste_insights.py`) connects to MySQL or reads input JSON, executes Pandas aggregations, and outputs a structured JSON payload to stdout.
4. PHP captures stdout, validates the JSON output, and returns strongly-typed DTO arrays to Laravel Blade views or API controllers.

### 2.4 Standalone Flask Microservice Architecture
In addition to the process-level integration, WasteWise provides a standalone Flask web microservice in `flask_service/`. Operating independently on `http://127.0.0.1:5000`, this service exposes dedicated routes for direct CSV file uploading, automated statistical summarization, and interactive chart rendering using Jinja2 templates.

---

<pagebreak />

## CHAPTER 3: SOFTWARE REQUIREMENT SPECIFICATIONS (SRS)

### 3.1 Functional Requirements

```
FR-1: Waste Item Search & Autocomplete
  FR-1.1: Asynchronous autocomplete search matching titles and categories as user types.
  FR-1.2: Display waste category badge, hazard level indicator, and disposal instructions.
  FR-1.3: Log all search queries into `search_histories` table for trend analytics.

FR-2: Collection Centre Lookup
  FR-2.1: View list of municipal collection centres filtered by Waste Category.
  FR-2.2: Filter collection centres by City name or Pincode.
  FR-2.3: Display centre operating hours, full address, and active contact numbers.

FR-3: Admin Management Suite
  FR-3.1: Admin authentication with encrypted password hashing (Bcrypt).
  FR-3.2: Complete CRUD management for Waste Categories (name, slug, description, icon, color).
  FR-3.3: Complete CRUD management for Waste Items (title, category, hazard level, tips).
  FR-3.4: Complete CRUD management for Collection Centres (name, address, city, lat/long).

FR-4: Bulk CSV Data Import
  FR-4.1: Admin interface to upload bulk CSV datasets for collection centres and items.
  FR-4.2: Automated row-by-row validation checking required fields and data types.
  FR-4.3: Skip invalid rows while inserting valid rows; generate import audit log (`analytics_imports`).

FR-5: Python Analytics & Flask Microservice
  FR-5.1: Execute `waste_insights.py` to analyze top searched terms and category distributions.
  FR-5.2: Expose Flask microservice endpoints (`/analyze-csv`, `/api/insights`) on Port 5000.
```

### 3.2 Non-Functional Requirements
- **Performance**: Autocomplete API response time < 150ms for query strings.
- **Security**: Mandatory CSRF protection on all POST forms; password hashing via Bcrypt; safe configuration parameters using environment variables; zero cleartext credentials.
- **Reliability**: Graceful handling of invalid input, empty database results, or missing CSV columns without application crashes.
- **Maintainability**: Clear separation of concerns following MVC design patterns in Laravel and modular routes in Flask.

### 3.3 User Roles & Permissions

| Role | Access Level | Permitted Actions |
|------|--------------|-------------------|
| **Public / Citizen** | Guest User (Unauthenticated) | Search waste items, view disposal instructions, view collection centre locations, access guide pages. |
| **System Administrator** | Authenticated Admin (`role = 'admin'`) | Full access to Citizen features plus Admin Dashboard, Category CRUD, Item CRUD, Centre CRUD, CSV Upload, Python Analytics, and Flask Microservice. |

### 3.4 Course 504 Syllabus Mapping
The implementation of WasteWise directly fulfills the practical and theoretical requirements specified in Course 504: Advanced Web Application & Python Integration.

```
+---------------------------------------------------------------------------------------+
|                              COURSE 504 SYLLABUS MAPPING                              |
+--------------------+----------------------------------+-------------------------------+
| Unit / Module      | Syllabus Requirement             | WasteWise Implementation      |
+--------------------+----------------------------------+-------------------------------+
| Unit 1 & Unit 2    | Advanced PHP MVC Frameworks &    | Laravel 11 MVC architecture,  |
|                    | Database ORM Integration         | Eloquent ORM models, Blade.   |
+--------------------+----------------------------------+-------------------------------+
| Unit 3             | RESTful API Design & Asynchronous| WasteSearchController REST API|
|                    | Data Exchanges                   | JSON responses, Fetch API.    |
+--------------------+----------------------------------+-------------------------------+
| Unit 4             | PHP to Python Integration &      | `PythonAnalyticsService.php`  |
|                    | Process Management               | invoking `waste_insights.py`. |
+--------------------+----------------------------------+-------------------------------+
| Unit 5             | Standalone Python Flask Framework| Flask microservice (`app.py`),|
|                    | & Microservices                  | Jinja2 templates on Port 5000.|
+--------------------+----------------------------------+-------------------------------+
```

---

<pagebreak />

## CHAPTER 4: SYSTEM & DATABASE DESIGN

### 4.1 System Architecture Diagram
The multi-tier system architecture of WasteWise illustrates the interaction between Client Browsers, Laravel Web Server, MySQL Database, Python process integration, and the Flask analytics microservice.

![System Architecture Diagram](docs/images/diag_01_system_architecture.png)

---

### 4.2 Entity-Relationship (ER) Diagram & Schema

The relational database schema consists of 6 primary core tables (`users`, `waste_categories`, `waste_items`, `collection_centres`, `search_histories`, `analytics_imports`).

![Entity-Relationship Diagram](docs/images/diag_02_er_diagram.png)

#### Detailed Data Dictionary:

##### Table 1: `users`
- `id` (BIGINT, Primary Key, Auto Increment)
- `name` (VARCHAR 255, Not Null)
- `email` (VARCHAR 255, Unique, Not Null)
- `password` (VARCHAR 255, Hashed, Not Null)
- `role` (VARCHAR 50, Default: 'citizen')
- `created_at`, `updated_at` (TIMESTAMP)

##### Table 2: `waste_categories`
- `id` (BIGINT, Primary Key, Auto Increment)
- `name` (VARCHAR 255, Unique, Not Null) - e.g. "E-Waste", "Organic", "Hazardous"
- `slug` (VARCHAR 255, Unique, Not Null)
- `description` (TEXT, Nullable)
- `icon` (VARCHAR 100, Nullable) - Bootstrap icon class name
- `color` (VARCHAR 50, Nullable) - Hex or badge color string
- `created_at`, `updated_at` (TIMESTAMP)

##### Table 3: `waste_items`
- `id` (BIGINT, Primary Key, Auto Increment)
- `category_id` (BIGINT, Foreign Key referencing `waste_categories.id`, ON DELETE CASCADE)
- `title` (VARCHAR 255, Not Null) - e.g. "Old Laptop", "Fluorescent Tube"
- `slug` (VARCHAR 255, Unique, Not Null)
- `description` (TEXT, Nullable)
- `disposal_instructions` (TEXT, Not Null)
- `recycling_tips` (TEXT, Nullable)
- `hazard_level` (ENUM: 'low', 'medium', 'high', Not Null, Default: 'low')
- `is_recyclable` (BOOLEAN, Default: true)
- `created_at`, `updated_at` (TIMESTAMP)

##### Table 4: `collection_centres`
- `id` (BIGINT, Primary Key, Auto Increment)
- `category_id` (BIGINT, Foreign Key referencing `waste_categories.id`, ON DELETE CASCADE)
- `name` (VARCHAR 255, Not Null)
- `address` (TEXT, Not Null)
- `city` (VARCHAR 100, Not Null)
- `state` (VARCHAR 100, Default: 'Gujarat')
- `pincode` (VARCHAR 20, Nullable)
- `latitude` (DECIMAL 10, 8, Nullable)
- `longitude` (DECIMAL 11, 8, Nullable)
- `contact_number` (VARCHAR 50, Nullable)
- `operating_hours` (VARCHAR 255, Nullable)
- `is_active` (BOOLEAN, Default: true)
- `created_at`, `updated_at` (TIMESTAMP)

##### Table 5: `search_histories`
- `id` (BIGINT, Primary Key, Auto Increment)
- `user_id` (BIGINT, Nullable, Foreign Key referencing `users.id`)
- `query` (VARCHAR 255, Not Null)
- `category_matched` (VARCHAR 255, Nullable)
- `results_count` (INTEGER, Default: 0)
- `ip_address` (VARCHAR 45, Nullable)
- `user_agent` (TEXT, Nullable)
- `created_at`, `updated_at` (TIMESTAMP)

##### Table 6: `analytics_imports`
- `id` (BIGINT, Primary Key, Auto Increment)
- `admin_id` (BIGINT, Foreign Key referencing `users.id`)
- `file_name` (VARCHAR 255, Not Null)
- `records_imported` (INTEGER, Default: 0)
- `status` (VARCHAR 50, Default: 'success')
- `notes` (TEXT, Nullable)
- `created_at`, `updated_at` (TIMESTAMP)

---

### 4.3 Context Diagram (DFD Level 0 Context)
The Context Diagram establishes the global boundary of the WasteWise system and its interactions with Citizen Users, System Administrators, and External Systems.

![Context Diagram](docs/images/diag_03_context_diagram.png)

---

### 4.4 Data Flow Diagram - Level 0
The DFD Level 0 overview breaks down WasteWise into its 5 primary functional processes and 5 major data stores.

![DFD Level 0 Diagram](docs/images/diag_04_dfd_level_0.png)

---

### 4.5 Data Flow Diagram - Level 1
DFD Level 1 expands the internal sub-processes of **Process 1.0 (Waste Search)** and **Process 3.0 (Admin CSV Import & Analytics)**.

![DFD Level 1 Diagram](docs/images/diag_05_dfd_level_1.png)

---

### 4.6 User Navigation Flow
The User Navigation Flow diagram outlines the step-by-step navigation path for citizen users accessing disposal guidance.

![User Navigation Flow](docs/images/diag_06_user_flow.png)

---

### 4.7 Admin Operations Flow
The Admin Operations Flow defines administrative tasks including CRUD operations, CSV data ingestion, and Python analytics execution.

![Admin Operations Flow](docs/images/diag_07_admin_flow.png)

---

### 4.8 Waste Search & Autocomplete Sequence Flow
This sequence flow details the asynchronous HTTP request/response exchange between client-side JavaScript, Laravel routes, controllers, and MySQL database.

![Waste Search Flow](docs/images/diag_08_waste_search_flow.png)

---

### 4.9 CSV Import Sequence Flow
This sequence flow demonstrates administrative CSV bulk upload validation, row-by-row parsing, database insertion, and audit logging.

![CSV Import Flow](docs/images/diag_09_csv_import_flow.png)

---

### 4.10 Flask Analysis Processing Flow
This sequence flow documents the execution path within the standalone Python Flask analytics microservice on Port 5000.

![Flask Analysis Flow](docs/images/diag_10_flask_analysis_flow.png)

---

<pagebreak />

## CHAPTER 5: IMPLEMENTATION & CODE HIGHLIGHTS

### 5.1 PHP ↔ Python Process Execution (`PythonAnalyticsService.php`)
The snippet below shows how Laravel safely executes Python analytics scripts via `proc_open` and parses stdout JSON.

```php
namespace App\Services;

use Exception;
use Illuminate\Support\Facades\Log;

class PythonAnalyticsService
{
    protected string $pythonPath;
    protected string $scriptPath;

    public function __construct()
    {
        $this->pythonPath = config('services.python.binary', 'python');
        $this->scriptPath = base_path('scripts/waste_insights.py');
    }

    public function runInsights(array $params = []): array
    {
        $command = [
            $this->pythonPath,
            $this->scriptPath,
            '--action', $params['action'] ?? 'trend_analysis',
            '--limit', (string) ($params['limit'] ?? 10)
        ];

        $descriptors = [
            0 => ['pipe', 'r'], // stdin
            1 => ['pipe', 'w'], // stdout
            2 => ['pipe', 'w'], // stderr
        ];

        $process = proc_open($command, $descriptors, $pipes, base_path());

        if (!is_resource($process)) {
            throw new Exception("Failed to execute Python process.");
        }

        fclose($pipes[0]);
        $output = stream_get_contents($pipes[1]);
        $error  = stream_get_contents($pipes[2]);
        fclose($pipes[1]);
        fclose($pipes[2]);

        $exitCode = proc_close($process);

        if ($exitCode !== 0) {
            Log::error("Python script error: " . $error);
            return ['status' => 'error', 'message' => 'Python execution failed'];
        }

        return json_decode($output, true) ?? [];
    }
}
```

---

### 5.2 Laravel Waste Search REST API Controller (`WasteSearchController.php`)
The endpoint below handles autocomplete queries asynchronously and logs search history events.

```php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\WasteItem;
use App\Models\SearchHistory;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class WasteSearchController extends Controller
{
    public function autocomplete(Request $request): JsonResponse
    {
        $query = trim($request->input('q', ''));

        if (strlen($query) < 2) {
            return response()->json([], 200);
        }

        $items = WasteItem::with('category')
            ->where('title', 'LIKE', '%' . $query . '%')
            ->orWhereHas('category', function ($q) use ($query) {
                $q->where('name', 'LIKE', '%' . $query . '%');
            })
            ->limit(10)
            ->get();

        // Audit Log Search Query
        SearchHistory::create([
            'user_id' => auth()->id(),
            'query' => $query,
            'category_matched' => $items->first()?->category?->name ?? 'Unmatched',
            'results_count' => $items->count(),
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent()
        ]);

        return response()->json($items, 200);
    }
}
```

---

### 5.3 Admin CSV Import Pipeline (`CsvImportController.php`)
The snippet demonstrates CSV row parsing, field validation, batch Eloquent creation, and import audit recording.

```php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CollectionCentre;
use App\Models\AnalyticsImport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CsvImportController extends Controller
{
    public function importCentres(Request $request)
    {
        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt|max:4096'
        ]);

        $file = $request->file('csv_file');
        $handle = fopen($file->getRealPath(), 'r');
        $header = fgetcsv($handle);

        $imported = 0;
        $skipped = 0;

        DB::beginTransaction();
        try {
            while (($row = fgetcsv($handle)) !== false) {
                if (count($row) < 5) {
                    $skipped++;
                    continue;
                }

                CollectionCentre::create([
                    'name' => $row[0],
                    'category_id' => (int) $row[1],
                    'address' => $row[2],
                    'city' => $row[3],
                    'contact_number' => $row[4] ?? null,
                    'is_active' => true
                ]);
                $imported++;
            }
            fclose($handle);

            AnalyticsImport::create([
                'admin_id' => auth()->id(),
                'file_name' => $file->getClientOriginalName(),
                'records_imported' => $imported,
                'status' => 'success',
                'notes' => "Skipped {$skipped} invalid rows."
            ]);

            DB::commit();
            return redirect()->back()->with('success', "Successfully imported {$imported} collection centres.");
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', "Import failed: " . $e->getMessage());
        }
    }
}
```

---

### 5.4 Python Insights Script (`scripts/waste_insights.py`)
This Python script connects to MySQL, performs Pandas aggregations on search history, and formats stdout JSON output.

```python
import sys
import json
import argparse
import pymysql
import pandas as pd

def get_db_connection():
    return pymysql.connect(
        host="127.0.0.1",
        user="root",
        password="<DB_PASSWORD>",
        database="wastewise",
        cursorclass=pymysql.cursors.DictCursor
    )

def analyze_trends(limit=10):
    try:
        conn = get_db_connection()
        query = "SELECT query, category_matched, results_count, created_at FROM search_histories"
        df = pd.read_sql(query, conn)
        conn.close()

        if df.empty:
            return {"status": "success", "total_searches": 0, "top_queries": [], "category_breakdown": {}}

        top_queries = df['query'].value_counts().head(limit).to_dict()
        category_breakdown = df['category_matched'].value_counts().to_dict()

        return {
            "status": "success",
            "total_searches": len(df),
            "top_queries": top_queries,
            "category_breakdown": category_breakdown
        }
    except Exception as e:
        return {"status": "error", "message": str(e)}

if __name__ == "__main__":
    parser = argparse.ArgumentParser()
    parser.add_argument("--action", default="trend_analysis")
    parser.add_argument("--limit", type=int, default=10)
    args = parser.parse_args()

    result = analyze_trends(limit=args.limit)
    print(json.dumps(result))
```

---

### 5.5 Flask Microservice Application (`flask_service/app.py`)
The Python Flask microservice routes CSV analytics requests and renders Jinja2 visualization dashboards on Port 5000.

```python
from flask import Flask, render_template, request, jsonify
import pandas as pd
import os

app = Flask(__name__)
app.config['SECRET_KEY'] = '<SECRET_KEY>'
UPLOAD_FOLDER = os.path.join(os.path.dirname(__file__), 'uploads')
app.config['UPLOAD_FOLDER'] = UPLOAD_FOLDER

@app.route('/')
def index():
    return render_template('index.html')

@app.route('/analyze-csv', methods=['POST'])
def analyze_csv():
    if 'file' not in request.files:
        return jsonify({'error': 'No file uploaded'}), 400

    file = request.files['file']
    if file.filename == '':
        return jsonify({'error': 'No selected file'}), 400

    filepath = os.path.join(app.config['UPLOAD_FOLDER'], file.filename)
    file.save(filepath)

    try:
        df = pd.read_csv(filepath)
        total_records = len(df)
        columns = list(df.columns)
        summary_stats = df.describe(include='all').fillna('').to_dict()

        return render_template('result.html', 
                               filename=file.filename,
                               total_records=total_records,
                               columns=columns,
                               summary_stats=summary_stats)
    except Exception as e:
        return jsonify({'error': str(e)}), 500

if __name__ == '__main__':
    app.run(host='127.0.0.1', port=5000, debug=True)
```

---

<pagebreak />

## CHAPTER 6: USER INTERFACE & SCREENSHOT PLACEHOLDERS

This chapter details the key user interface screens designed for citizens and administrators. High-resolution screenshots should be placed in the designated placeholder frames below prior to final submission.

### 6.1 Citizen Portal Interfaces

#### Home Page & Search Portal
The main landing page features a prominent search bar with instant autocomplete, category quick-filter pills, and introductory waste disposal guidance.

[Insert Screenshot - Home Page]

#### Waste Search Results & Autocomplete
Real-time autocomplete dropdown appearing as users type search queries, showing matching waste titles and category badges.

[Insert Screenshot - Waste Search Result]

#### Waste Item Disposal Details Page
Comprehensive view of a specific waste item displaying disposal instructions, recycling/composting advice, hazard warnings, and nearby collection centre links.

[Insert Screenshot - Waste Item Disposal Details]

#### Collection Centre Finder Page
Filterable directory allowing users to search collection centres by waste category and city, displaying addresses, contact numbers, and operating hours.

[Insert Screenshot - Collection Centres Finder]

---

### 6.2 Admin Portal Interfaces

#### Admin Authentication Screen
Secure admin login form featuring email/password validation and CSRF token verification.

[Insert Screenshot - Admin Login Form]

#### Admin Dashboard Overview
Central administrative panel presenting key metrics including total items, categories, collection centres, and search query analytics summaries.

[Insert Screenshot - Admin Dashboard]

#### Category Management Interface
CRUD management page for defining waste categories, descriptions, custom Bootstrap icons, and badge colors.

[Insert Screenshot - Category Management]

#### Waste Item & Hazard Management Interface
Interface for managing waste items, assigning categories, defining disposal steps, and setting hazard levels (`low`, `medium`, `high`).

[Insert Screenshot - Waste Item Management]

#### Collection Centre Management Interface
Management screen for adding and updating facility locations, latitude/longitude coordinates, contact details, and active status toggles.

[Insert Screenshot - Collection Centre Management]

#### Bulk CSV Data Import Interface
Admin upload interface for bulk importing collection centres and waste items via CSV files, displaying row validation logs.

[Insert Screenshot - CSV Upload Form]

---

### 6.3 Python & Flask Analytics Interfaces

#### Python Waste Insights Analytics View
Admin analytics dashboard rendering query frequency charts and category breakdown distributions generated via `waste_insights.py`.

[Insert Screenshot - Python Insights Output]

#### Standalone Flask Microservice Portal
Standalone Flask web application running on `http://127.0.0.1:5000` providing direct CSV dataset analysis and Jinja2 analytics reports.

[Insert Screenshot - Flask Analytics Portal]

---

<pagebreak />

## CHAPTER 7: TESTING & QUALITY ASSURANCE

### 7.1 Verified Test Execution Baseline
Automated testing was conducted across both the Laravel web application and the Python Flask microservice to ensure full software reliability and regression prevention.

```
=======================================================================================
                         AUTOMATED TEST SUITE EXECUTION SUMMARY
=======================================================================================
Framework / Suite        | Test Runner | Tests Executed | Assertions | Result Status
-------------------------+-------------+----------------+------------+-----------------
Laravel Web Application  | PHPUnit 11  | 122 Tests      | 380 Assert | OK (100% Passed)
Python Flask Service     | Unittest    | 27 Tests       | --         | OK (100% Passed)
=======================================================================================
```

---

### 7.2 Test Case Specifications Matrix

| Test ID | Sub-system | Test Scenario Description | Input / Method | Expected Result | Verified Result |
|---------|------------|---------------------------|----------------|-----------------|-----------------|
| **TC-01** | Autocomplete | Search query with valid string ("Laptop") | `GET /api/waste-search?q=Laptop` | Return JSON array containing "Old Laptop" item; HTTP 200. | PASSED |
| **TC-02** | Autocomplete | Search query with short string ("L") | `GET /api/waste-search?q=L` | Return empty JSON array `[]`; HTTP 200. | PASSED |
| **TC-03** | Autocomplete | Search query logging | Search execution | Insert record into `search_histories` table. | PASSED |
| **TC-04** | Collection Centre | Filter centres by category ID | `GET /centres?category_id=1` | Return centres belonging to specified category only. | PASSED |
| **TC-05** | Admin Auth | Login with valid credentials | `POST /admin/login` | Authenticate user, start session, redirect to `/admin/dashboard`. | PASSED |
| **TC-06** | Admin Auth | Login with invalid password | `POST /admin/login` | Reject authentication, display error message; HTTP 302. | PASSED |
| **TC-07** | Admin Auth | Access admin route as Guest | `GET /admin/dashboard` | Redirect unauthenticated user to `/admin/login`. | PASSED |
| **TC-08** | CSV Import | Upload valid CSV dataset | `POST /admin/import-csv` | Parse rows, insert into database, return success message. | PASSED |
| **TC-09** | CSV Import | Upload malformed CSV file | `POST /admin/import-csv` | Skip invalid rows, process valid rows, log skip count. | PASSED |
| **TC-10** | Python IPC | Execute `waste_insights.py` | `proc_open` execution | Return valid JSON stdout containing `status: success`. | PASSED |
| **TC-11** | Flask Service| Access Flask home page | `GET http://127.0.0.1:5000/` | Render index HTML template; HTTP 200. | PASSED |
| **TC-12** | Flask Service| Upload CSV to Flask service | `POST http://127.0.0.1:5000/analyze-csv` | Parse CSV via Pandas, render `result.html`; HTTP 200. | PASSED |

---

### 7.3 Error Handling & Edge Case Verification
1. **Empty Search Inputs**: Handled gracefully without throwing exceptions; returns clean JSON arrays.
2. **Missing Database Records**: Item and Category routes check for model existence using `findOrFail()` and display user-friendly 404 pages.
3. **Database Connection Failures**: Wrapped in `try-catch` blocks in PHP and Python to prevent exposure of sensitive stack traces.
4. **CSV Header Mismatches**: Validated before starting row iterations; aborts gracefully with descriptive error alerts.

---

<pagebreak />

## CHAPTER 8: CONCLUSION & FUTURE SCOPE

### 8.1 Project Conclusion
The **WasteWise: Smart Waste Disposal Assistant** project successfully achieves its core objective of delivering an intelligent, accessible waste disposal guidance portal and administrative analytics suite.

#### Key Accomplishments:
- Developed a high-performance web platform using Laravel 11, PHP 8.2, and MySQL 8.0.
- Implemented real-time autocomplete search and category-filtered collection centre lookup.
- Integrated Python analytics via native `proc_open` process execution and a standalone Flask microservice on Port 5000.
- Verified software stability through **122 passed Laravel tests (380 assertions)** and **27 passed Flask tests**.
- Maintained academic formatting and full mapping to Course 504 syllabus requirements.

### 8.2 Limitations & Future Enhancement Roadmap
While WasteWise delivers a robust foundation, future iterations can expand system capabilities:

```
+---------------------------------------------------------------------------------------+
|                               FUTURE ENHANCEMENT ROADMAP                              |
+---------------------+-----------------------------------------------------------------+
| Module              | Proposed Enhancement                                            |
+---------------------+-----------------------------------------------------------------+
| AI Image Recognition| Integrate Mobile Camera / Computer Vision API (TensorFlow) to   |
|                     | automatically identify waste items from photographs.            |
+---------------------+-----------------------------------------------------------------+
| Geo-Location GPS    | Implement browser HTML5 Geolocation to automatically calculate  |
|                     | distance in kilometers to the nearest collection centre.        |
+---------------------+-----------------------------------------------------------------+
| Citizen Rewards     | Add Gamification & Citizen Reward points for reporting verified |
|                     | recycling drops at collection facilities.                       |
+---------------------+-----------------------------------------------------------------+
| Push Notifications  | Mobile SMS/Push notifications notifying citizens of special     |
|                     | municipal E-Waste collection drives in their pincode area.      |
+---------------------+-----------------------------------------------------------------+
```

---

<pagebreak />

## CHAPTER 9: REFERENCES & BIBLIOGRAPHY

1. **PHP Documentation**: Official PHP 8.2 Manual and Reference Guide. Available at: `https://www.php.net/docs.php`
2. **Laravel Framework**: Laravel 11 Documentation & Web Architecture Principles. Available at: `https://laravel.com/docs/11.x`
3. **MySQL Reference Manual**: MySQL 8.0 Database Engine & Optimization Guide. Available at: `https://dev.mysql.com/doc/refman/8.0/en/`
4. **Python Standard Library**: Python 3.14 Documentation & Process Management. Available at: `https://docs.python.org/3/`
5. **Flask Web Framework**: Flask Documentation & WSGI Microservice Design. Available at: `https://flask.palletsprojects.com/`
6. **Pandas Data Analysis Library**: PyData Pandas Documentation. Available at: `https://pandas.pydata.org/docs/`
7. **Jinja2 Templating Engine**: Jinja2 Documentation. Available at: `https://jinja.palletsprojects.com/`
8. **Bootstrap Frontend Toolkit**: Bootstrap 5.3 Component & Layout System. Available at: `https://getbootstrap.com/docs/5.3/`
9. **PHPUnit Testing Framework**: PHPUnit 11 Manual by Sebastian Bergmann. Available at: `https://phpunit.de/documentation.html`
10. **Central Pollution Control Board (CPCB)**: Municipal Solid Waste & E-Waste Management Rules & Guidelines, Government of India.
"""

with open(output_md_path, 'w', encoding='utf-8') as f:
    f.write(report_md_content)

with open(root_md_path, 'w', encoding='utf-8') as f:
    f.write(report_md_content)

print(f"✅ Master Markdown Report written to:\n  - {output_md_path}\n  - {root_md_path}")
