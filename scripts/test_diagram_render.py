import subprocess
import os

html_content = """<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<style>
body { background: white; margin: 0; padding: 20px; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
.mermaid-container { background: #ffffff; padding: 20px; border: 1px solid #cbd5e1; border-radius: 8px; display: inline-block; }
</style>
<script src="https://cdn.jsdelivr.net/npm/mermaid@10/dist/mermaid.min.js"></script>
<script>
mermaid.initialize({ startOnLoad: true, theme: 'neutral' });
</script>
</head>
<body>
<div class="mermaid-container">
<div class="mermaid">
graph TD
    User([Citizen User]) -->|1. Search Request| Laravel[Laravel Web Server]
    Laravel -->|2. Query Items| DB[(MySQL Database)]
    Laravel -->|3. Trigger Insights| Python[scripts/waste_insights.py]
    Python -->|4. Return JSON Analytics| Laravel
    Laravel -->|5. Display Guidance & Charts| User
</div>
</div>
</body>
</html>"""

html_file = r'D:\tmp\htdocs\wastewise\test_mermaid.html'
img_file = r'D:\tmp\htdocs\wastewise\test_mermaid.png'

with open(html_file, 'w', encoding='utf-8') as f:
    f.write(html_content)

edge_path = r'C:\Program Files (x86)\Microsoft\Edge\Application\msedge.exe'
subprocess.run([edge_path, '--headless', '--disable-gpu', '--window-size=1000,600', f'--screenshot={img_file}', html_file])

print('Screenshot created:', os.path.exists(img_file), 'Size:', os.path.getsize(img_file) if os.path.exists(img_file) else 0)
