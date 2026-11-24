import subprocess
import os
import sys
import re
import fitz  # PyMuPDF

sys.stdout.reconfigure(encoding='utf-8')

wastewise_dir = r'D:\tmp\htdocs\wastewise'
md_path = os.path.join(wastewise_dir, 'WASTEWISE_PROJECT_REPORT.md')
html_path = os.path.join(wastewise_dir, 'WASTEWISE_PROJECT_REPORT.html')
pdf_path = os.path.join(wastewise_dir, 'WASTEWISE_PROJECT_REPORT.pdf')
root_pdf_path = r'd:\EventVenueManagement\WASTEWISE_PROJECT_REPORT.pdf'
pdf_pages_dir = os.path.join(wastewise_dir, r'docs\pdf_pages')

os.makedirs(pdf_pages_dir, exist_ok=True)
edge_path = r'C:\Program Files (x86)\Microsoft\Edge\Application\msedge.exe'

# Read source Markdown
with open(md_path, 'r', encoding='utf-8') as f:
    md_text = f.read()

# Convert Markdown to HTML with custom formatting
def parse_markdown_to_academic_html(text):
    # Process pagebreaks
    text = text.replace('<pagebreak />', '<div class="page-break"></div>')
    
    # Process diagrams to img tags pointing to docs/images/
    diagram_map = {
        "System Architecture Diagram": "diag_01_system_architecture.png",
        "Entity-Relationship (ER) Diagram": "diag_02_er_diagram.png",
        "Entity-Relationship Diagram": "diag_02_er_diagram.png",
        "Context Diagram": "diag_03_context_diagram.png",
        "DFD Level 0 Diagram": "diag_04_dfd_level_0.png",
        "DFD Level 1 Diagram": "diag_05_dfd_level_1.png",
        "User Navigation Flow": "diag_06_user_flow.png",
        "Admin Operations Flow": "diag_07_admin_flow.png",
        "Waste Search Flow": "diag_08_waste_search_flow.png",
        "CSV Import Flow": "diag_09_csv_import_flow.png",
        "Flask Analysis Flow": "diag_10_flask_analysis_flow.png"
    }

    for title, img_name in diagram_map.items():
        pattern = rf'!\[{title}\]\((.*?)\)'
        img_url = f"docs/images/{img_name}"
        replacement = f'''
<figure class="diagram-figure">
    <img src="{img_url}" alt="{title}" class="diagram-img" />
    <figcaption>Figure: {title}</figcaption>
</figure>
'''
        text = re.sub(pattern, replacement, text)

    # Process Screenshot Placeholders
    placeholder_pattern = r'\[Insert Screenshot - (.*?)\]'
    def replace_placeholder(m):
        name = m.group(1)
        return f'''
<div class="screenshot-box">
    <div class="screenshot-header">📷 SCREENSHOT PLACEHOLDER</div>
    <div class="screenshot-title">[Insert Screenshot - {name}]</div>
    <div class="screenshot-desc">High-resolution interface screenshot of {name} screen for final submission printing.</div>
</div>
'''
    text = re.sub(placeholder_pattern, replace_placeholder, text)

    # Convert markdown headers
    # Cover Page handling
    lines = text.split('\n')
    html_lines = []
    in_code = False
    code_buffer = []
    code_lang = ''
    in_table = False
    table_buffer = []

    def flush_table(buf):
        if not buf:
            return ''
        html = ['<table class="academic-table">']
        for i, row in enumerate(buf):
            cells = [c.strip() for c in row.strip('|').split('|')]
            tag = 'th' if i == 0 else 'td'
            # Skip separator row like |---|---|
            if i == 1 and all(set(c) <= set('-: ') for c in cells):
                continue
            html.append('<tr>')
            for cell in cells:
                html.append(f'<{tag}>{cell}</{tag}>')
            html.append('</tr>')
        html.append('</table>')
        return '\n'.join(html)

    for line in lines:
        # Code block handling
        if line.startswith('```'):
            if in_code:
                in_code = False
                code_text = '\n'.join(code_buffer)
                escaped_code = code_text.replace('&', '&amp;').replace('<', '&lt;').replace('>', '&gt;')
                html_lines.append(f'<pre class="code-block"><code>{escaped_code}</code></pre>')
                code_buffer = []
            else:
                in_code = True
                code_lang = line.replace('```', '').strip()
            continue

        if in_code:
            code_buffer.append(line)
            continue

        # Table handling
        if line.strip().startswith('|') and line.strip().endswith('|'):
            in_table = True
            table_buffer.append(line)
            continue
        elif in_table:
            in_table = False
            html_lines.append(flush_table(table_buffer))
            table_buffer = []

        # Headings
        if line.startswith('# '):
            html_lines.append(f'<h1 class="chapter-title">{line[2:].strip()}</h1>')
        elif line.startswith('## '):
            html_lines.append(f'<h2 class="sec-title">{line[3:].strip()}</h2>')
        elif line.startswith('### '):
            html_lines.append(f'<h3 class="subsec-title">{line[4:].strip()}</h3>')
        elif line.startswith('#### '):
            html_lines.append(f'<h4 class="subsubsec-title">{line[5:].strip()}</h4>')
        elif line.startswith('- '):
            html_lines.append(f'<ul><li>{line[2:].strip()}</li></ul>')
        elif line.startswith('1. ') or line.startswith('2. ') or line.startswith('3. ') or line.startswith('4. ') or line.startswith('5. '):
            num_text = line.split('. ', 1)[1]
            html_lines.append(f'<ol><li>{num_text.strip()}</li></ol>')
        elif line.strip() == '---':
            html_lines.append('<hr class="academic-hr" />')
        elif line.strip() == '':
            html_lines.append('<p></p>')
        else:
            html_lines.append(f'<p>{line.strip()}</p>')

    if in_table:
        html_lines.append(flush_table(table_buffer))

    body_html = '\n'.join(html_lines)

    # Post processing for formatting bold/italic/links
    body_html = re.sub(r'\*\*(.*?)\*\*', r'<strong>\1</strong>', body_html)
    body_html = re.sub(r'\*(.*?)\*', r'<em>\1</em>', body_html)
    body_html = re.sub(r'`(.*?)`', r'<code class="inline-code">\1</code>', body_html)

    return body_html

body_content = parse_markdown_to_academic_html(md_text)

full_html = f"""<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>WasteWise Academic Project Report</title>
<style>
@page {{
    size: A4 portrait;
    margin: 20mm 15mm 20mm 15mm;
    @top-right {{
        content: "WasteWise Project Report";
        font-family: Arial, sans-serif;
        font-size: 8pt;
        color: #64748b;
    }}
    @bottom-right {{
        content: "Page " counter(page);
        font-family: Arial, sans-serif;
        font-size: 9pt;
        color: #475569;
        font-weight: bold;
    }}
    @bottom-left {{
        content: "BCA Academic Submission";
        font-family: Arial, sans-serif;
        font-size: 8pt;
        color: #64748b;
    }}
}}

body {{
    font-family: 'Segoe UI', Arial, Helvetica, sans-serif;
    font-size: 10.5pt;
    line-height: 1.5;
    color: #1e293b;
    background: #ffffff;
    margin: 0;
    padding: 0;
}}

/* Cover Page Styling */
.cover-page {{
    height: 90vh;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    align-items: center;
    text-align: center;
    border: 3px double #1e3a8a;
    padding: 40px 30px;
    box-sizing: border-box;
    page-break-after: always;
}}

.cover-header h1 {{
    font-size: 32pt;
    font-weight: 800;
    color: #1e3a8a;
    margin: 10px 0 5px 0;
    letter-spacing: 2px;
}}

.cover-header h2 {{
    font-size: 16pt;
    font-weight: 600;
    color: #0d9488;
    margin: 0 0 20px 0;
}}

.cover-badge {{
    background: #1e3a8a;
    color: #ffffff;
    padding: 6px 20px;
    font-size: 12pt;
    font-weight: 700;
    letter-spacing: 1px;
    border-radius: 20px;
    display: inline-block;
    margin: 15px 0;
}}

.cover-details {{
    width: 100%;
    margin: 30px 0;
    text-align: left;
    display: flex;
    justify-content: space-between;
}}

.cover-col {{
    width: 48%;
    font-size: 10.5pt;
}}

.cover-col p {{
    margin: 6px 0;
}}

.cover-col strong {{
    color: #0f172a;
}}

.cover-footer {{
    border-top: 2px solid #cbd5e1;
    padding-top: 15px;
    width: 100%;
    text-align: center;
    font-size: 10pt;
    color: #334155;
}}

/* Headings */
h1.chapter-title {{
    font-size: 20pt;
    font-weight: 800;
    color: #1e3a8a;
    border-bottom: 2px solid #1e3a8a;
    padding-bottom: 6px;
    margin-top: 30px;
    margin-bottom: 18px;
    page-break-before: always;
}}

h2.sec-title {{
    font-size: 14pt;
    font-weight: 700;
    color: #0f172a;
    margin-top: 22px;
    margin-bottom: 12px;
    border-left: 4px solid #0d9488;
    padding-left: 10px;
}}

h3.subsec-title {{
    font-size: 12pt;
    font-weight: 700;
    color: #1e293b;
    margin-top: 16px;
    margin-bottom: 8px;
}}

h4.subsubsec-title {{
    font-size: 11pt;
    font-weight: 600;
    color: #334155;
    margin-top: 12px;
    margin-bottom: 6px;
}}

p {{
    margin-top: 4px;
    margin-bottom: 8px;
    text-align: justify;
}}

ul, ol {{
    margin-top: 4px;
    margin-bottom: 8px;
    padding-left: 24px;
}}

li {{
    margin-bottom: 4px;
}}

.page-break {{
    page-break-before: always;
}}

/* Code Blocks */
pre.code-block {{
    background: #f8fafc;
    border: 1px solid #cbd5e1;
    border-left: 4px solid #1e3a8a;
    border-radius: 4px;
    padding: 12px 15px;
    font-family: 'Consolas', 'Courier New', monospace;
    font-size: 9pt;
    line-height: 1.4;
    overflow-x: auto;
    white-space: pre-wrap;
    word-wrap: break-word;
    margin: 14px 0;
    page-break-inside: avoid;
}}

code.inline-code {{
    background: #f1f5f9;
    border: 1px solid #e2e8f0;
    padding: 2px 5px;
    border-radius: 3px;
    font-family: 'Consolas', monospace;
    font-size: 9pt;
    color: #0f172a;
}}

/* Academic Tables */
table.academic-table {{
    width: 100%;
    border-collapse: collapse;
    margin: 16px 0;
    font-size: 9.5pt;
    page-break-inside: avoid;
    table-layout: auto;
}}

table.academic-table th {{
    background-color: #1e293b;
    color: #ffffff;
    font-weight: 700;
    text-align: left;
    padding: 8px 10px;
    border: 1px solid #0f172a;
}}

table.academic-table td {{
    padding: 7px 10px;
    border: 1px solid #cbd5e1;
    vertical-align: top;
    word-wrap: break-word;
}}

table.academic-table tr:nth-child(even) {{
    background-color: #f8fafc;
}}

/* Diagrams & Figures */
.diagram-figure {{
    text-align: center;
    margin: 20px 0;
    page-break-inside: avoid;
}}

.diagram-img {{
    max-width: 95%;
    max-height: 480px;
    object-fit: contain;
    border: 1px solid #cbd5e1;
    border-radius: 6px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.08);
}}

figcaption {{
    font-size: 9pt;
    font-style: italic;
    color: #475569;
    margin-top: 6px;
    font-weight: 600;
}}

/* Screenshot Callouts */
.screenshot-box {{
    background: #eff6ff;
    border: 1px solid #bfdbfe;
    border-left: 5px solid #3b82f6;
    border-radius: 6px;
    padding: 14px 18px;
    margin: 16px 0;
    page-break-inside: avoid;
}}

.screenshot-header {{
    font-size: 8.5pt;
    font-weight: 800;
    color: #1d4ed8;
    letter-spacing: 1px;
    margin-bottom: 4px;
}}

.screenshot-title {{
    font-size: 11pt;
    font-weight: 700;
    color: #1e3a8a;
    margin-bottom: 4px;
}}

.screenshot-desc {{
    font-size: 9pt;
    color: #334155;
    font-style: italic;
}}

hr.academic-hr {{
    border: 0;
    height: 1px;
    background: #cbd5e1;
    margin: 20px 0;
}}
</style>
</head>
<body>
{body_content}
</body>
</html>"""

# Write rendered HTML
with open(html_path, 'w', encoding='utf-8') as f:
    f.write(full_html)

print(f"✅ HTML report rendered to: {html_path}")

# Generate PDF via Edge Headless
print("Generating PDF via Headless Edge...")
cmd = [
    edge_path,
    '--headless',
    '--disable-gpu',
    '--no-pdf-header-footer',
    f'--print-to-pdf={pdf_path}',
    html_path
]

res = subprocess.run(cmd, stdout=subprocess.DEVNULL, stderr=subprocess.DEVNULL)

if os.path.exists(pdf_path) and os.path.getsize(pdf_path) > 0:
    print(f"✅ PDF created successfully at: {pdf_path} ({os.path.getsize(pdf_path)} bytes)")
    # Copy to workspace root
    import shutil
    shutil.copyfile(pdf_path, root_pdf_path)
    print(f"✅ Copied PDF to workspace root: {root_pdf_path}")
else:
    print(f"❌ PDF generation failed!")
    sys.exit(1)

# Quality Check with PyMuPDF
print("\n--- PDF QUALITY AUDIT ---")
doc = fitz.open(pdf_path)
total_pages = len(doc)
print(f"📄 Total PDF Pages: {total_pages}")

issue_count = 0
for i, page in enumerate(doc):
    pix = page.get_pixmap(dpi=150)
    img_out = os.path.join(pdf_pages_dir, f"page_{i+1:02d}.png")
    pix.save(img_out)
    
    # Simple sanity checks on text extraction
    text = page.get_text()
    if len(text.strip()) == 0:
        print(f"⚠️ Page {i+1} appears to be BLANK!")
        issue_count += 1

print(f"✅ Extracted all {total_pages} page images to: {pdf_pages_dir}")
if issue_count == 0:
    print("✅ Quality Check Passed: Zero blank pages detected!")
else:
    print(f"⚠️ Quality Check Warning: {issue_count} issues flagged.")
