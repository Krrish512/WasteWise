#!/usr/bin/env python3
"""
WasteWise - Unit 4 Integration Python Script
-------------------------------------------
Uses subprocess and os module to generate storage/app/python_reports/waste_report.json.

Unit 4 syllabus coverage:
- subprocess: check_call(), check_output(), decode(), Popen(), communicate(), split()
- os module: path.join(), path.exists(), path.isdir(), path.isfile(), makedirs(), mkdir(),
             chdir(), listdir(), walk(), open(), write(), close()
"""

import os
import sys
import json
import base64
import subprocess
from datetime import datetime
from collections import Counter

def run_python_php_report_utility(payload_b64=None):
    # 1. OS Module Path & Directory Operations
    base_dir = os.path.dirname(os.path.dirname(os.path.abspath(__file__)))
    target_dir = os.path.join(base_dir, "storage", "app", "python_reports")

    # Demonstrate os.path.exists, os.path.isdir, os.makedirs, os.mkdir
    if not os.path.exists(target_dir):
        os.makedirs(target_dir, exist_ok=True)
    elif not os.path.isdir(target_dir):
        os.mkdir(target_dir)

    json_report_path = os.path.join(target_dir, "waste_report.json")

    # Demonstrate os.chdir, os.listdir, os.walk
    orig_dir = os.getcwd()
    os.chdir(base_dir)
    dir_files = os.listdir(target_dir)

    scanned_files = []
    for root, dirs, files in os.walk(target_dir):
        for f in files:
            full_f = os.path.join(root, f)
            if os.path.isfile(full_f):
                scanned_files.append(f)

    # 2. Subprocess Module Operations (Internal Execution Check)
    php_cmd = "php -v".split()
    try:
        check_call_code = subprocess.check_call([php_cmd[0], "-v"], stdout=subprocess.DEVNULL, stderr=subprocess.DEVNULL)
    except Exception:
        check_call_code = -1

    try:
        raw_php_out = subprocess.check_output([php_cmd[0], "-v"])
        php_ver = raw_php_out.decode('utf-8').splitlines()[0]
    except Exception as e:
        php_ver = str(e)

    try:
        artisan_proc = subprocess.Popen([php_cmd[0], "artisan", "about", "--only=environment"], stdout=subprocess.PIPE, stderr=subprocess.PIPE)
        stdout_bytes, stderr_bytes = artisan_proc.communicate()
        artisan_out = stdout_bytes.decode('utf-8')
    except Exception as e:
        artisan_out = str(e)

    # 3. Parse Search History Data
    records = []
    if payload_b64:
        try:
            raw_json = base64.b64decode(payload_b64).decode('utf-8')
            records = json.loads(raw_json)
        except Exception:
            records = []

    total_searches = len(records)
    now_str = datetime.now().strftime("%Y-%m-%d %H:%M:%S")

    if total_searches == 0:
        top_category = "N/A"
        top_item = "N/A"
        top_category_share = 0
        most_active_day = "N/A"
        category_breakdown = []
        insight = "No search history recorded yet."
    else:
        cat_counts = Counter(r.get('category_name', 'Uncategorized') for r in records)
        top_category, top_cat_cnt = cat_counts.most_common(1)[0]
        top_category_share = round((top_cat_cnt / total_searches) * 100)

        item_counts = Counter(r.get('waste_item_name', 'General Item') for r in records)
        top_item, _ = item_counts.most_common(1)[0]

        days = []
        for r in records:
            dt_str = r.get('searched_at')
            if dt_str:
                try:
                    dt = datetime.strptime(dt_str[:19], "%Y-%m-%d %H:%M:%S")
                    days.append(dt.strftime("%A"))
                except Exception:
                    pass
        if days:
            most_active_day, _ = Counter(days).most_common(1)[0]
        else:
            most_active_day = datetime.now().strftime("%A")

        category_breakdown = []
        for cat, cnt in cat_counts.items():
            category_breakdown.append({
                "category": cat,
                "searches": cnt,
                "share": round((cnt / total_searches) * 100)
            })

        insight = f"{top_category} is currently the most searched category."

    # Final Clean JSON Structure
    json_data = {
        "generated_at": now_str,
        "total_searches": total_searches,
        "top_category": top_category,
        "top_item": top_item,
        "top_category_share": top_category_share,
        "most_active_day": most_active_day,
        "category_breakdown": category_breakdown,
        "insight": insight
    }

    formatted_json_str = json.dumps(json_data, indent=2)

    # 4. Low-level OS file write demonstration (os.open, os.write, os.close)
    flags = os.O_WRONLY | os.O_CREAT | os.O_TRUNC
    if hasattr(os, 'O_BINARY'):
        flags |= os.O_BINARY

    fd = os.open(json_report_path, flags)
    os.write(fd, formatted_json_str.encode('utf-8'))
    os.close(fd)

    # Clean up old txt file if exists
    txt_report_path = os.path.join(target_dir, "waste_report.txt")
    if os.path.exists(txt_report_path):
        try:
            os.remove(txt_report_path)
        except Exception:
            pass

    # Restore working directory
    os.chdir(orig_dir)

    return {
        "status": "success",
        "message": "WasteWise JSON Report generated successfully.",
        "report_file": "storage/app/python_reports/waste_report.json",
        "file_exists": os.path.exists(json_report_path),
        "data": json_data
    }

def main():
    payload = sys.argv[1] if len(sys.argv) > 1 else None
    try:
        res = run_python_php_report_utility(payload)
    except Exception as e:
        res = {"status": "error", "message": str(e)}
    print(json.dumps(res))

if __name__ == "__main__":
    main()
