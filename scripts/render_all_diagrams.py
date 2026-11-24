import subprocess
import os
import sys

sys.stdout.reconfigure(encoding='utf-8')

wastewise_dir = r'D:\tmp\htdocs\wastewise'
img_dir = os.path.join(wastewise_dir, r'docs\images')
os.makedirs(img_dir, exist_ok=True)

edge_path = r'C:\Program Files (x86)\Microsoft\Edge\Application\msedge.exe'

diagrams = {
    "diag_01_system_architecture": {
        "title": "System Architecture Diagram",
        "code": """graph TD
    subgraph Client Tier
        Browser[Web Browser / Mobile Client]
    end

    subgraph Presentation & Application Tier - Laravel 11
        Router[Laravel Routing & Middleware]
        Controllers[Blade Views / Controllers]
        SearchCtrl[WasteSearchController]
        AdminCtrl[Admin Dashboard Controller]
        ImportCtrl[CsvImportController]
        PythonSvc[PythonAnalyticsService - Process Exec]
    end

    subgraph Data & Storage Tier
        MySQL[(MySQL 8.0 Database\\nwastewise)]
        Storage[Local File Storage / uploads]
    end

    subgraph Analytics Microservice Tier - Python 3.14
        InsightsScript[scripts/waste_insights.py]
        FlaskService[Flask Web Service - Port 5000]
        Pandas[Pandas & Analytics Pipeline]
    end

    Browser -->|HTTP GET/POST Port 8000| Router
    Router --> Controllers
    Controllers --> SearchCtrl
    Controllers --> AdminCtrl
    Controllers --> ImportCtrl
    SearchCtrl -->|Eloquent ORM Queries| MySQL
    AdminCtrl -->|Eloquent ORM Queries| MySQL
    ImportCtrl -->|Batch Insert CSV| MySQL
    ImportCtrl -->|Store Uploads| Storage
    AdminCtrl -->|proc_open JSON Execution| PythonSvc
    PythonSvc --> InsightsScript
    InsightsScript -->|Read Search History| MySQL
    Browser -->|HTTP REST API Port 5000| FlaskService
    FlaskService --> Pandas
    Pandas -->|Direct MySQL Connection| MySQL"""
    },
    "diag_02_er_diagram": {
        "title": "Entity-Relationship (ER) Diagram",
        "code": """erDiagram
    users {
        bigint id PK
        string name
        string email UK
        timestamp email_verified_at
        string password
        string role
        string remember_token
        timestamps created_at_updated_at
    }

    waste_categories {
        bigint id PK
        string name UK
        string slug UK
        text description
        string icon
        string color
        timestamps created_at_updated_at
    }

    waste_items {
        bigint id PK
        bigint category_id FK
        string title
        string slug UK
        text description
        text disposal_instructions
        text recycling_tips
        string hazard_level
        boolean is_recyclable
        timestamps created_at_updated_at
    }

    collection_centres {
        bigint id PK
        bigint category_id FK
        string name
        string address
        string city
        string state
        string pincode
        decimal latitude
        decimal longitude
        string contact_number
        string operating_hours
        boolean is_active
        timestamps created_at_updated_at
    }

    search_histories {
        bigint id PK
        bigint user_id FK
        string query
        string category_matched
        integer results_count
        string ip_address
        string user_agent
        timestamps created_at_updated_at
    }

    analytics_imports {
        bigint id PK
        bigint admin_id FK
        string file_name
        integer records_imported
        string status
        text notes
        timestamps created_at_updated_at
    }

    waste_categories ||--o{ waste_items : "contains"
    waste_categories ||--o{ collection_centres : "handles"
    users ||--o{ search_histories : "initiates"
    users ||--o{ analytics_imports : "uploads" """
    },
    "diag_03_context_diagram": {
        "title": "Context Diagram (DFD Level 0 Context)",
        "code": """graph TD
    Citizen[Citizen / Public User]
    Admin[System Administrator]
    System((0.0 WasteWise System))
    ExternalGov[Municipal Waste Database / External APIs]

    Citizen -->|1. Waste Search Queries| System
    Citizen -->|2. Location / Pincode Filters| System
    System -->|3. Disposal Instructions & Guidelines| Citizen
    System -->|4. Nearby Collection Centres & Maps| Citizen

    Admin -->|5. Category & Item Management| System
    Admin -->|6. Centre Details & Operating Hours| System
    Admin -->|7. CSV Datasets for Analytics| System
    System -->|8. System Audit Logs & Usage Reports| Admin
    System -->|9. Waste Insights & Trend Analytics| Admin

    System <-->|10. Data Sync & Backup Exports| ExternalGov"""
    },
    "diag_04_dfd_level_0": {
        "title": "DFD Level 0 Overview Diagram",
        "code": """graph TD
    User([Citizen User])
    Admin([System Admin])

    P1((1.0 Waste Search & Guidance))
    P2((2.0 Collection Centre Lookup))
    P3((3.0 Admin Management))
    P4((4.0 Python Analytics Engine))
    P5((5.0 Flask Microservice))

    DS1[(D1: Waste Items)]
    DS2[(D2: Waste Categories)]
    DS3[(D3: Collection Centres)]
    DS4[(D4: Search History)]
    DS5[(D5: Analytics Imports)]

    User -->|Search Query| P1
    P1 -->|Fetch Matching Items| DS1
    P1 -->|Fetch Category Info| DS2
    P1 -->|Log Query Event| DS4
    P1 -->|Return Guidance| User

    User -->|Category / Location Request| P2
    P2 -->|Query Centres by Category/City| DS3
    P2 -->|Return Centre List| User

    Admin -->|Category/Item CRUD| P3
    P3 -->|Update Items & Categories| DS1
    P3 -->|Update Categories| DS2
    Admin -->|Centre CRUD| P3
    P3 -->|Update Centres| DS3
    Admin -->|Upload Analytics CSV| P3
    P3 -->|Write Import Record| DS5

    P3 -->|Execute Analytics Script| P4
    P4 -->|Read Query & Category Trends| DS4
    P4 -->|Return JSON Chart Data| P3

    Admin -->|Access REST Analytics API| P5
    P5 -->|Query Analytics Tables| DS4
    P5 -->|Query Import History| DS5
    P5 -->|Return HTML Dashboard / JSON| Admin"""
    },
    "diag_05_dfd_level_1": {
        "title": "DFD Level 1 Detailed Sub-processes Diagram",
        "code": """graph TD
    subgraph DFD Level 1.0 - Waste Search Sub-system
        User([Citizen User])
        P1_1((1.1 Autocomplete Query Input))
        P1_2((1.2 Database Matching Engine))
        P1_3((1.3 Result Formatting & Hazard Tagging))
        P1_4((1.4 Search History Logger))
        DS_Items[(D1: Waste Items)]
        DS_Cats[(D2: Waste Categories)]
        DS_Hist[(D4: Search History)]

        User -->|Keystroke Query String| P1_1
        P1_1 -->|Sanitized Search Term| P1_2
        P1_2 <-->|SQL LIKE / Full-text Lookup| DS_Items
        P1_2 <-->|Category Relationship| DS_Cats
        P1_2 -->|Raw Item Records| P1_3
        P1_3 -->|JSON Autocomplete & Item Details| User
        P1_3 -->|Query Metadata| P1_4
        P1_4 -->|Insert Audit Record| DS_Hist
    end

    subgraph DFD Level 3.0 - Admin CSV Import & Analytics Sub-system
        Admin([System Admin])
        P3_1((3.1 CSV Validation Engine))
        P3_2((3.2 Batch DB Inserter))
        P3_3((3.3 Analytics Execution Trigger))
        P3_4((3.4 Python Insights Processor))
        DS_Imp[(D5: Analytics Imports)]

        Admin -->|Upload CSV File| P3_1
        P3_1 -->|Valid Rows| P3_2
        P3_2 -->|Insert Centre/Item Records| DS_Items
        P3_2 -->|Write Audit Log| DS_Imp
        P3_2 -->|Signal Import Complete| P3_3
        P3_3 -->|Execute python waste_insights.py| P3_4
        P3_4 <-->|Process Search Trends| DS_Hist
        P3_4 -->|Render Chart Summaries| Admin
    end"""
    },
    "diag_06_user_flow": {
        "title": "User Navigation & Interaction Flow",
        "code": """graph TD
    Start([User Visits WasteWise Portal]) --> Home[Landing Page / Search Interface]
    Home --> Choice{User Action}

    Choice -->|Type Item Name| Autocomplete[Autocomplete Dropdown Appears]
    Autocomplete --> SelectItem[Select Waste Item]
    SelectItem --> ViewDetails[View Item Disposal Page]
    ViewDetails --> CheckHazard{Is Item Hazardous?}
    CheckHazard -->|Yes| ViewHazardTips[View Special Handling & Safety Warnings]
    CheckHazard -->|No| ViewRecycleTips[View Recycling & Composting Tips]
    ViewHazardTips --> ViewCentre[Click 'Find Collection Centre']
    ViewRecycleTips --> ViewCentre

    Choice -->|Click Collection Centres| CentreFilter[Filter Centres by Category / City]
    CentreFilter --> ViewCentreList[View Centre Address, Hours & Contact]
    ViewCentreList --> Directions[Get Location Details]

    Choice -->|Click How It Works| ViewGuide[Read Waste Classification Guide]
    Choice -->|Click About| ViewAbout[Read Project Mission & Technology Stack]"""
    },
    "diag_07_admin_flow": {
        "title": "Admin Dashboard & Operations Flow",
        "code": """graph TD
    Start([Admin Accesses /admin/login]) --> AuthCheck{Credentials Valid?}
    AuthCheck -->|No| Error[Show Invalid Credentials Error] --> Start
    AuthCheck -->|Yes| Dashboard[Admin Dashboard Overview]

    Dashboard --> Option1[Manage Waste Categories]
    Option1 --> CatCRUD[Create / Edit / Delete Category]

    Dashboard --> Option2[Manage Waste Items]
    Option2 --> ItemCRUD[Create / Edit / Delete Item & Hazard Level]

    Dashboard --> Option3[Manage Collection Centres]
    Option3 --> CentreCRUD[Create / Edit / Toggle Active Status]

    Dashboard --> Option4[Analytics & Reports]
    Option4 --> RunPython[Trigger Python Waste Insights Engine]
    RunPython --> ViewCharts[View Search Frequency & Category Visualizations]

    Dashboard --> Option5[CSV Data Import]
    Option5 --> UploadCSV[Upload Bulk Waste/Centre CSV File]
    UploadCSV --> ImportSummary[View Record Success / Failure Log]

    Dashboard --> Option6[Flask Service Portal]
    Option6 --> OpenFlask[Redirect to Flask Service Port 5000]"""
    },
    "diag_08_waste_search_flow": {
        "title": "Waste Search & Autocomplete Sequence Flow",
        "code": """sequenceDiagram
    autonumber
    actor User as Citizen (Browser)
    participant JS as welcome.blade.php (JS)
    participant Route as routes/web.php
    participant Ctrl as WasteSearchController
    participant Model as WasteItem Model
    participant DB as MySQL Database

    User->>JS: Types item name (e.g. 'Laptop')
    JS->>Route: GET /api/waste-search?q=Laptop
    Route->>Ctrl: invoke autocomplete(Request $request)
    Ctrl->>Model: query() where('title', 'LIKE', '%Laptop%')
    Model->>DB: SELECT * FROM waste_items WHERE title LIKE '%Laptop%' LIMIT 10
    DB-->>Model: Return matching rows
    Model-->>Ctrl: Eloquent Collection
    Ctrl->>DB: INSERT INTO search_histories (query, results_count, ip)
    Ctrl-->>JS: JSON Response [{id, title, category, hazard_level}]
    JS-->>User: Render Autocomplete Dropdown List
    User->>JS: Click on 'Old Laptop'
    JS->>User: Navigate to /waste-items/old-laptop"""
    },
    "diag_09_csv_import_flow": {
        "title": "CSV Bulk Data Import Sequence Flow",
        "code": """sequenceDiagram
    autonumber
    actor Admin as System Administrator
    participant View as Admin Import View
    participant Ctrl as CsvImportController
    participant Validator as Request Validation
    participant Model as CollectionCentre Model
    participant DB as MySQL Database

    Admin->>View: Select CSV file & click 'Upload Data'
    View->>Ctrl: POST /admin/import-csv (Multipart Form)
    Ctrl->>Validator: validate(['csv_file' => 'required|mimes:csv,txt'])
    Validator-->>Ctrl: Validation Passed
    Ctrl->>Ctrl: Open file & parse header row
    loop For each row in CSV
        Ctrl->>Ctrl: Validate row fields (name, category_id, city, coordinates)
        alt Valid Row
            Ctrl->>Model: create(row_data)
            Model->>DB: INSERT INTO collection_centres (...)
        else Invalid Row
            Ctrl->>Ctrl: Log error to skip_list
        end
    end
    Ctrl->>DB: INSERT INTO analytics_imports (records_imported, status)
    Ctrl-->>View: Redirect with Success Alert ("Imported X records")
    View-->>Admin: Display Import Summary & Error Audit Table"""
    },
    "diag_10_flask_analysis_flow": {
        "title": "Flask Analytics Service Processing Flow",
        "code": """sequenceDiagram
    autonumber
    actor Admin as System Administrator
    participant Flask as Flask Microservice (Port 5000)
    participant Engine as Analytics Engine (app.py)
    participant Pandas as Pandas & Scikit-learn
    participant DB as MySQL Database (wastewise)

    Admin->>Flask: GET / or POST /analyze-csv
    Flask->>Engine: Route Request to analyze_data()
    alt CSV Upload Mode
        Engine->>Pandas: read_csv(uploaded_file)
    else Database Direct Mode
        Engine->>DB: SELECT category_matched, COUNT(*) FROM search_histories GROUP BY category_matched
        DB-->>Engine: Raw Search Analytics Dataset
        Engine->>Pandas: DataFrame(query_results)
    end
    Engine->>Pandas: Calculate Total Queries, Category Distribution & Hazard Ratios
    Pandas-->>Engine: Processed Metrics & Chart Arrays
    Engine->>Flask: render_template('result.html', data=metrics)
    Flask-->>Admin: Rendered HTML Dashboard with Charts & Category Breakdowns"""
    }
}

print(f"Generating {len(diagrams)} diagram PNG images...")

for name, dinfo in diagrams.items():
    html_content = f"""<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<style>
body {{
    background: #ffffff;
    margin: 0;
    padding: 30px;
    font-family: 'Segoe UI', Arial, sans-serif;
    display: flex;
    justify-content: center;
    align-items: center;
}}
.diagram-box {{
    background: #ffffff;
    border: 2px solid #e2e8f0;
    border-radius: 12px;
    padding: 30px;
    box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1);
    display: inline-block;
    max-width: 100%;
}}
.diagram-title {{
    font-size: 18px;
    font-weight: 700;
    color: #1e293b;
    margin-bottom: 20px;
    text-align: center;
    font-family: Arial, sans-serif;
}}
</style>
<script src="https://cdn.jsdelivr.net/npm/mermaid@10/dist/mermaid.min.js"></script>
<script>
mermaid.initialize({{
    startOnLoad: true,
    theme: 'neutral',
    securityLevel: 'loose',
    flowchart: {{ useMaxWidth: false, htmlLabels: true }},
    er: {{ useMaxWidth: false }},
    sequence: {{ useMaxWidth: false }}
}});
</script>
</head>
<body>
<div class="diagram-box">
    <div class="diagram-title">{dinfo['title']}</div>
    <div class="mermaid">
{dinfo['code']}
    </div>
</div>
</body>
</html>"""

    tmp_html = os.path.join(img_dir, f"{name}.html")
    out_png = os.path.join(img_dir, f"{name}.png")

    with open(tmp_html, 'w', encoding='utf-8') as f:
        f.write(html_content)

    cmd = [
        edge_path,
        '--headless',
        '--disable-gpu',
        '--window-size=1200,900',
        f'--screenshot={out_png}',
        tmp_html
    ]
    subprocess.run(cmd, stdout=subprocess.DEVNULL, stderr=subprocess.DEVNULL)
    
    if os.path.exists(out_png) and os.path.getsize(out_png) > 0:
        print(f"✅ {name}.png generated ({os.path.getsize(out_png)} bytes)")
    else:
        print(f"❌ Failed to generate {name}.png")

print("Diagram generation completed!")
