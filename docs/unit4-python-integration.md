# WasteWise — Course 504 Unit 4 Documentation
## PHP ↔ Python Integration (Smart Waste Analytics & Insights)

### Overview
This document provides a technical walkthrough of the Course 504 Unit 4 syllabus implementation in WasteWise. It covers inter-process communication between PHP (Laravel) and Python, shell execution, subprocess execution, data serialization, and filesystem management.

---

### Architecture & Data Flow

#### 1. PHP → Python Integration (Waste Insights Engine)

```
+------------------+         +--------------------------+         +--------------------------+
|  MySQL Database  | ------> |       Laravel (PHP)      | ------> |   Python Analytics Engine|
| (SearchHistory)  |         | PythonAnalyticsService   |         |  scripts/waste_insights.py|
+------------------+         +--------------------------+         +--------------------------+
                                          |                                    |
                                          | Passes JSON (base64)               | Evaluates category
                                          v                                    v trends & top item
                             +--------------------------+         +--------------------------+
                             | Admin Analytics View     | <------ | Returns JSON Output      |
                             | /admin/analytics         |         | (total, top_cat, share)  |
                             +--------------------------+         +--------------------------+
```

1. **Database Retrieval:** Laravel queries `SearchHistory` with related `WasteItem` and `WasteCategory` models.
2. **Payload Preparation:** Data is serialized into JSON and base64 encoded for safe CLI argument transmission across operating systems.
3. **Execution & Command Safety:** `PythonAnalyticsService` invokes `shell_exec()` using `escapeshellcmd()` and `escapeshellarg()`:
   ```php
   $safeCommand = escapeshellcmd($pythonBin . ' ' . $scriptPath) . ' ' . escapeshellarg($base64Payload);
   $rawOutput = shell_exec($safeCommand);
   ```
4. **Python Processing:** `scripts/waste_insights.py` decodes input, computes total searches, top category, top item, category share percentage, active day, and generates a natural language insight string.
5. **JSON Return:** Python outputs structured JSON to `stdout`, which PHP decodes via `json_decode()`.

---

#### 2. Python → PHP Integration (Subprocess Report Generator)

```
+--------------------------------------+
| Python Script: run_php_report.py    |
+--------------------------------------+
                   |
                   | 1. subprocess.check_output("php -v") & decode()
                   | 2. subprocess.Popen(["php", "artisan", "about"]) & communicate()
                   v
+--------------------------------------+
| PHP / Artisan Runtime Output         |
+--------------------------------------+
                   |
                   | 3. Low-level File Writing: os.open(), os.write(), os.close()
                   | 4. Directory Audit: os.walk(), os.listdir(), os.path.exists()
                   v
+--------------------------------------+
| storage/app/python_reports/          |
| waste_report.json                    |
+--------------------------------------+
```

---

### Unit 4 Syllabus Coverage Checklist

| Syllabus Topic | Function / Module | Location in Codebase |
| :--- | :--- | :--- |
| **4.1.1** Echo / Output Handling | `echo` / `json_encode` / `stdout` | `scripts/waste_insights.py`, `PythonAnalyticsService.php` |
| **4.1.2** Escaping & Shell Exec | `escapeshellcmd()`, `shell_exec()`, `escapeshellarg()` | `app/Services/PythonAnalyticsService.php` |
| **4.2.1.1** Subprocess module | `subprocess.check_call()` | `scripts/run_php_report.py` |
| **4.2.1.1** Subprocess module | `subprocess.check_output()` | `scripts/run_php_report.py` |
| **4.2.1.1** Output decoding | `raw_output.decode('utf-8')` | `scripts/run_php_report.py` |
| **4.2.1.1** Subprocess Popen | `subprocess.Popen()` | `scripts/run_php_report.py` |
| **4.2.1.1** Popen communicate | `process.communicate()` | `scripts/run_php_report.py` |
| **4.2.1.1** Argument splitting | `cmd_str.split()` | `scripts/run_php_report.py` |
| **4.2.2.1** OS Path & Directories | `os.path.join()`, `os.path.exists()`, `os.path.isfile()`, `os.path.isdir()` | `scripts/run_php_report.py` |
| **4.2.2.1** OS Dir Creation | `os.mkdir()`, `os.makedirs()` | `scripts/run_php_report.py` |
| **4.2.2.1** OS File IO | `os.open()`, `os.write()`, `os.close()`, `f.read()` | `scripts/run_php_report.py` |
| **4.2.2.2** OS Traversal & Dir Ops | `os.listdir()`, `os.walk()`, `os.chdir()` | `scripts/run_php_report.py` |

---

### Why Python is Used in WasteWise
- **Advanced Data Processing:** Python excels at aggregations, text processing, statistical summary generation, and future machine learning integration.
- **Interoperability:** Demonstrates standard enterprise integration between web backend frameworks (Laravel) and data analysis scripts (Python).

---

### Security Measures
1. **No Shell Injection:** Shell commands never concatenate raw user input.
2. **Safe Argument Escaping:** Input data is base64 encoded and wrapped with `escapeshellarg()`. Script paths are sanitized with `escapeshellcmd()`.
3. **Controlled Invocation:** Python scripts only execute pre-approved internal paths within `scripts/`.
4. **Error Masking:** Raw shell exception messages are logged privately to Laravel log files and never displayed raw to end-users.
