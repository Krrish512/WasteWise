#!/usr/bin/env python3
"""
WasteWise - Unit 4 Python Script: Waste Analytics & Insights
------------------------------------------------------------
Purpose: Receives JSON search history data from Laravel (PHP),
processes category & item trends, and outputs structured JSON insights.

Demonstrates:
- Command-line argument parsing
- Base64 & raw JSON decoding
- Statistical calculations (percentages, top categories, top items)
"""

import sys
import json
import base64
from collections import Counter
from datetime import datetime

def analyze_waste_data(raw_data):
    if not raw_data or not isinstance(raw_data, list):
        return {
            "status": "success",
            "total_searches": 0,
            "top_category": "N/A",
            "top_item": "N/A",
            "percentage": 0.0,
            "most_active_day": "N/A",
            "insight": "No search history recorded yet. Perform waste searches to generate smart insights.",
            "category_distribution": []
        }

    total_searches = len(raw_data)
    categories = []
    items = []
    days = []

    for entry in raw_data:
        cat_name = entry.get("category_name") or "Uncategorized"
        item_name = entry.get("waste_item_name") or "General Waste"
        searched_at = entry.get("searched_at") or ""

        categories.append(cat_name)
        items.append(item_name)

        if searched_at:
            try:
                dt = datetime.strptime(str(searched_at)[:10], "%Y-%m-%d")
                days.append(dt.strftime("%A"))
            except ValueError:
                pass

    cat_counts = Counter(categories)
    item_counts = Counter(items)
    day_counts = Counter(days)

    top_category, top_cat_count = cat_counts.most_common(1)[0] if cat_counts else ("N/A", 0)
    top_item, _ = item_counts.most_common(1)[0] if item_counts else ("N/A", 0)
    most_active_day, _ = day_counts.most_common(1)[0] if day_counts else ("N/A", 0)

    percentage = round((top_cat_count / total_searches) * 100, 2) if total_searches > 0 else 0.0

    distribution = []
    for cat, count in cat_counts.most_common():
        pct = round((count / total_searches) * 100, 1) if total_searches > 0 else 0.0
        distribution.append({
            "category": cat,
            "searches": count,
            "percentage": pct
        })

    insight = f"{top_category} is currently the most searched category, representing {percentage}% of total waste searches."

    return {
        "status": "success",
        "total_searches": total_searches,
        "top_category": top_category,
        "top_item": top_item,
        "percentage": percentage,
        "most_active_day": most_active_day,
        "insight": insight,
        "category_distribution": distribution
    }

def main():
    if len(sys.argv) < 2:
        result = analyze_waste_data([])
        print(json.dumps(result))
        return

    arg = sys.argv[1]
    data = None

    # Try raw JSON decode first
    try:
        data = json.loads(arg)
    except Exception:
        # Try base64 decode if raw string parse fails
        try:
            decoded_bytes = base64.b64decode(arg)
            data = json.loads(decoded_bytes.decode('utf-8'))
        except Exception as e:
            result = {
                "status": "error",
                "message": f"Python Payload Parse Error: {str(e)}",
                "total_searches": 0,
                "top_category": "Error",
                "top_item": "Error",
                "percentage": 0.0,
                "most_active_day": "N/A",
                "insight": "Failed to decode input search history payload in Python.",
                "category_distribution": []
            }
            print(json.dumps(result))
            return

    result = analyze_waste_data(data)
    print(json.dumps(result, indent=2))

if __name__ == "__main__":
    main()
