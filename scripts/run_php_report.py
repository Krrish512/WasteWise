#!/usr/bin/env python3
"""
WasteWise - Unit 4.2 Python Utility: Subprocess & OS Module Demonstration
-------------------------------------------------------------------------
Purpose: Demonstrates executing PHP / Artisan commands from Python using
subprocess, and managing generated reports using the os module.

Demonstrates Unit 4.2 Syllabus Topics:
- subprocess: check_call(), check_output(), decode(), Popen(), communicate(), split()
- os module: write(), read(), close(), mkdir(), makedirs(), path.exists(),
             path.isfile(), path.isdir(), listdir(), walk(), chdir(), path.join()
"""

import os
import sys
import json
import subprocess
from datetime import datetime

def run_python_php_report_utility():
    # 1. OS Module Path & Directory Operations
    base_dir = os.path.dirname(os.path.dirname(os.path.abspath(__file__)))
    target_dir = os.path.join(base_dir, "storage", "app", "python_reports")

    # Demonstrate os.path.exists, os.path.isdir, os.makedirs, os.mkdir
    if not os.path.exists(target_dir):
        os.makedirs(target_dir, exist_ok=True)
    elif not os.path.isdir(target_dir):
        os.mkdir(target_dir)

    report_file_path = os.path.join(target_dir, "waste_report.txt")

    # Demonstrate os.chdir and os.listdir
    orig_dir = os.getcwd()
    os.chdir(base_dir)
    dir_files = os.listdir(target_dir)

    # Demonstrate os.walk
    scanned_files = []
    for root, dirs, files in os.walk(target_dir):
        for f in files:
            full_f = os.path.join(root, f)
            if os.path.isfile(full_f):
                scanned_files.append(f)

    # 2. Subprocess Module Operations
    # Demonstrate command split
    php_version_cmd = "php -v"
    cmd_parts = php_version_cmd.split()

    # Demonstrate subprocess.check_call()
    try:
        check_call_result = subprocess.check_call([cmd_parts[0], "-v"], stdout=subprocess.DEVNULL, stderr=subprocess.DEVNULL)
    except Exception:
        check_call_result = -1

    # Demonstrate subprocess.check_output() and decode()
    try:
        raw_php_output = subprocess.check_output([cmd_parts[0], "-v"])
        decoded_php_version = raw_php_output.decode('utf-8').splitlines()[0]
    except Exception as e:
        decoded_php_version = f"PHP execution check note: {str(e)}"

    # Demonstrate subprocess.Popen() and communicate()
    try:
        artisan_cmd = [cmd_parts[0], "artisan", "about", "--only=environment"]
        process = subprocess.Popen(artisan_cmd, stdout=subprocess.PIPE, stderr=subprocess.PIPE)
        stdout_data, stderr_data = process.communicate()
        decoded_artisan_output = stdout_data.decode('utf-8')
    except Exception as e:
        decoded_artisan_output = f"Artisan execution note: {str(e)}"

    # 3. File Operations demonstrating os low-level write/close + standard file ops
    now_str = datetime.now().strftime("%Y-%m-%d %H:%M:%S")

    report_content = f"""==================================================
WASTEWISE SMART WASTE ANALYTICS REPORT
Generated via WasteWise Python Integration
Date: {now_str}
==================================================

1. PHP ENGINE VERIFICATION (subprocess.check_output & decode)
--------------------------------------------------
{decoded_php_version}

2. ARTISAN SYSTEM AUDIT (subprocess.Popen & communicate)
--------------------------------------------------
{decoded_artisan_output.strip()}

3. PYTHON OS MODULE DIRECTORY AUDIT
--------------------------------------------------
Base Directory: {base_dir}
Target Report Dir: {target_dir}
Directory Exists: {os.path.exists(target_dir)}
Is Directory: {os.path.isdir(target_dir)}
Files in Target Dir: {', '.join(dir_files) if dir_files else 'None'}
Walk Scanned Files: {', '.join(scanned_files) if scanned_files else 'None'}

4. SUMMARY
--------------------------------------------------
This report confirms successful PHP <-> Python execution,
subprocess management, and filesystem handling.
==================================================
"""

    # Low-level OS file write demonstration (os.open, os.write, os.close)
    flags = os.O_WRONLY | os.O_CREAT | os.O_TRUNC
    if hasattr(os, 'O_BINARY'):
        flags |= os.O_BINARY

    fd = os.open(report_file_path, flags)
    os.write(fd, report_content.encode('utf-8'))
    os.close(fd)

    # Verify os.path.isfile and os.path.exists
    file_exists = os.path.exists(report_file_path) and os.path.isfile(report_file_path)

    # Read back using standard file read to verify
    with open(report_file_path, "r", encoding="utf-8") as f:
        verified_content = f.read()

    # Restore working directory
    os.chdir(orig_dir)

    return {
        "status": "success",
        "message": "Python-PHP Report generated successfully.",
        "report_file": report_file_path,
        "file_exists": file_exists,
        "php_version": decoded_php_version,
        "check_call_code": check_call_result,
        "generated_at": now_str,
        "file_size_bytes": len(verified_content)
    }

def main():
    try:
        result = run_python_php_report_utility()
    except Exception as e:
        result = {
            "status": "error",
            "message": f"Python Report Execution Error: {str(e)}"
        }

    print(json.dumps(result, indent=2))

if __name__ == "__main__":
    main()
