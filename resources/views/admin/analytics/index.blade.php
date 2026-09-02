@extends('layouts.app')

@section('title', '📊 Smart Waste Insights – Admin Analytics')

@section('content')
<div class="admin-dash-header" style="background:linear-gradient(135deg, #065f46 0%, #047857 60%, #10b981 100%);color:white;padding:2.5rem 0;margin-bottom:2.5rem;">
    <div class="container" style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:1rem;">
        <div>
            <div style="display:inline-flex;align-items:center;gap:.4rem;background:rgba(255,255,255,.15);backdrop-filter:blur(8px);padding:.3rem .8rem;border-radius:var(--radius-full);font-size:.8rem;font-weight:700;margin-bottom:.6rem;color:#dcfce7;">
                🐍 Course 504 Unit 4 • PHP ↔ Python Integration
            </div>
            <h1 class="admin-dash-title" style="font-family:'Outfit',sans-serif;font-size:2.2rem;font-weight:900;margin:0;">
                📊 Smart Waste Insights & Analytics
            </h1>
            <p class="admin-dash-sub" style="color:#a7f3d0;font-size:1rem;margin-top:.4rem;max-width:650px;">
                Real-time MySQL search data analyzed dynamically using Python scripts via PHP <code>shell_exec()</code>, <code>subprocess</code>, and the <code>os</code> module.
            </p>
        </div>
        <div style="display:flex;gap:.75rem;flex-wrap:wrap;">
            <a href="{{ route('admin.dashboard') }}" style="background:rgba(255,255,255,.2);color:#fff;padding:.6rem 1.2rem;border-radius:var(--radius-full);font-weight:700;font-size:.9rem;text-decoration:none;border:1px solid rgba(255,255,255,.3);">
                ← Admin Dashboard
            </a>
        </div>
    </div>
</div>

<div class="container" style="padding-bottom:5rem;">

    <!-- Top Metric Cards (4 Cards) -->
    <div style="display:grid;grid-template-columns:repeat(auto-fit, minmax(220px, 1fr));gap:1.5rem;margin-bottom:2.5rem;">
        
        <!-- Total Searches -->
        <div style="background:#fff;border:1px solid var(--border-light);border-radius:var(--radius-xl);padding:1.5rem;box-shadow:var(--shadow-sm);display:flex;align-items:center;gap:1.25rem;">
            <div style="width:52px;height:52px;border-radius:var(--radius-md);background:#ecfdf5;color:var(--primary);display:flex;align-items:center;justify-content:center;font-size:1.6rem;flex-shrink:0;">
                🔍
            </div>
            <div>
                <div style="font-size:.85rem;color:var(--text-muted);font-weight:600;">Total Searches</div>
                <div style="font-family:'Outfit',sans-serif;font-size:1.8rem;font-weight:900;color:var(--text-dark);">
                    {{ $insights['total_searches'] ?? 0 }}
                </div>
            </div>
        </div>

        <!-- Most Searched Category -->
        <div style="background:#fff;border:1px solid var(--border-light);border-radius:var(--radius-xl);padding:1.5rem;box-shadow:var(--shadow-sm);display:flex;align-items:center;gap:1.25rem;">
            <div style="width:52px;height:52px;border-radius:var(--radius-md);background:#eff6ff;color:#1d4ed8;display:flex;align-items:center;justify-content:center;font-size:1.6rem;flex-shrink:0;">
                ♻️
            </div>
            <div>
                <div style="font-size:.85rem;color:var(--text-muted);font-weight:600;">Top Waste Category</div>
                <div style="font-family:'Outfit',sans-serif;font-size:1.25rem;font-weight:800;color:var(--text-dark);">
                    {{ $insights['top_category'] ?? 'N/A' }}
                </div>
            </div>
        </div>

        <!-- Most Searched Waste Item -->
        <div style="background:#fff;border:1px solid var(--border-light);border-radius:var(--radius-xl);padding:1.5rem;box-shadow:var(--shadow-sm);display:flex;align-items:center;gap:1.25rem;">
            <div style="width:52px;height:52px;border-radius:var(--radius-md);background:#fef3c7;color:#b45309;display:flex;align-items:center;justify-content:center;font-size:1.6rem;flex-shrink:0;">
                🗑️
            </div>
            <div>
                <div style="font-size:.85rem;color:var(--text-muted);font-weight:600;">Top Waste Item</div>
                <div style="font-family:'Outfit',sans-serif;font-size:1.25rem;font-weight:800;color:var(--text-dark);">
                    {{ $insights['top_item'] ?? 'N/A' }}
                </div>
            </div>
        </div>

        <!-- Top Category Percentage -->
        <div style="background:#fff;border:1px solid var(--border-light);border-radius:var(--radius-xl);padding:1.5rem;box-shadow:var(--shadow-sm);display:flex;align-items:center;gap:1.25rem;">
            <div style="width:52px;height:52px;border-radius:var(--radius-md);background:#f3e8ff;color:#6b21a8;display:flex;align-items:center;justify-content:center;font-size:1.6rem;flex-shrink:0;">
                📈
            </div>
            <div>
                <div style="font-size:.85rem;color:var(--text-muted);font-weight:600;">Top Category Share</div>
                <div style="font-family:'Outfit',sans-serif;font-size:1.8rem;font-weight:900;color:var(--text-dark);">
                    {{ $insights['percentage'] ?? 0 }}%
                </div>
            </div>
        </div>

    </div>

    <!-- Python Smart Insight Banner -->
    <div style="background:linear-gradient(135deg, #ecfdf5 0%, #d1fae5 100%);border:1.5px solid #a7f3d0;border-radius:var(--radius-xl);padding:1.75rem 2rem;margin-bottom:2.5rem;box-shadow:var(--shadow-sm);display:flex;align-items:center;gap:1.5rem;flex-wrap:wrap;">
        <div style="width:56px;height:56px;border-radius:50%;background:#047857;color:#fff;display:flex;align-items:center;justify-content:center;font-size:1.8rem;flex-shrink:0;box-shadow:0 4px 12px rgba(4,120,87,.3);">
            💡
        </div>
        <div style="flex:1;">
            <div style="font-size:.8rem;font-weight:800;text-transform:uppercase;letter-spacing:.05em;color:#065f46;margin-bottom:.3rem;">
                🐍 Python Generated Smart Insight (scripts/waste_insights.py)
            </div>
            <div style="font-family:'Outfit',sans-serif;font-size:1.25rem;font-weight:800;color:#064e3b;line-height:1.4;">
                "{{ $insights['insight'] ?? 'No search history recorded yet.' }}"
            </div>
        </div>
        <div>
            <span style="background:#fff;color:#047857;border:1px solid #a7f3d0;padding:.4rem metric;border-radius:var(--radius-full);font-size:.82rem;font-weight:700;padding:.4rem .9rem;">
                Most Active Day: {{ $insights['most_active_day'] ?? 'N/A' }}
            </span>
        </div>
    </div>

    <!-- Main Two Column Grid: Category Distribution & Python Subprocess Report -->
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:2rem;margin-bottom:3rem;">
        
        <!-- Left: Category Distribution Table -->
        <div style="background:#fff;border:1px solid var(--border-light);border-radius:var(--radius-xl);padding:2rem;box-shadow:var(--shadow-sm);">
            <h3 style="font-family:'Outfit',sans-serif;font-size:1.3rem;font-weight:800;color:var(--text-dark);margin-bottom:1.25rem;display:flex;align-items:center;gap:.5rem;">
                📊 Category Search Breakdown
            </h3>

            @if(empty($insights['category_distribution']))
                <div style="text-align:center;padding:2.5rem;background:#f8fafc;border-radius:var(--radius-lg);color:var(--text-muted);">
                    <div style="font-size:2.5rem;margin-bottom:.5rem;">🔍</div>
                    <p style="margin:0;font-size:.92rem;">No category search data available yet.</p>
                </div>
            @else
                <table style="width:100%;border-collapse:collapse;font-size:.95rem;">
                    <thead>
                        <tr style="border-bottom:2px solid var(--border-light);text-align:left;color:var(--text-muted);font-size:.82rem;text-transform:uppercase;letter-spacing:.04em;">
                            <th style="padding:.75rem 0;">Category Name</th>
                            <th style="padding:.75rem 0;text-align:center;">Searches</th>
                            <th style="padding:.75rem 0;text-align:right;">Distribution Share</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($insights['category_distribution'] as $item)
                            <tr style="border-bottom:1px solid var(--border-light);">
                                <td style="padding:1rem 0;font-weight:700;color:var(--text-dark);">
                                    ♻️ {{ $item['category'] }}
                                </td>
                                <td style="padding:1rem 0;text-align:center;font-weight:800;color:var(--primary-dark);">
                                    {{ $item['searches'] }}
                                </td>
                                <td style="padding:1rem 0;text-align:right;">
                                    <div style="display:inline-flex;align-items:center;gap:.75rem;justify-content:flex-end;">
                                        <div style="width:80px;height:8px;background:#e2e8f0;border-radius:var(--radius-full);overflow:hidden;">
                                            <div style="width:{{ min(100, $item['percentage']) }}%;height:100%;background:var(--primary);border-radius:var(--radius-full);"></div>
                                        </div>
                                        <span style="font-weight:700;font-size:.88rem;color:var(--text-mid);min-width:45px;">
                                            {{ $item['percentage'] }}%
                                        </span>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>

        <!-- Right: Python 4.2 Subprocess & OS Module Report Generator -->
        <div style="background:#fff;border:1px solid var(--border-light);border-radius:var(--radius-xl);padding:2rem;box-shadow:var(--shadow-sm);display:flex;flex-direction:column;">
            <h3 style="font-family:'Outfit',sans-serif;font-size:1.3rem;font-weight:800;color:var(--text-dark);margin-bottom:.5rem;display:flex;align-items:center;gap:.5rem;">
                ⚙️ Unit 4.2 Subprocess & OS Module Report
            </h3>
            <p style="color:var(--text-muted);font-size:.92rem;margin-bottom:1.5rem;line-height:1.5;">
                Demonstrates Python executing PHP commands via <code>subprocess.check_output()</code> &amp; <code>Popen()</code>, and writing files using <code>os.makedirs()</code>, <code>os.walk()</code>, and <code>os.write()</code>.
            </p>

            <div style="background:#f8fafc;border:1px solid var(--border-light);border-radius:var(--radius-lg);padding:1.25rem;margin-bottom:1.5rem;flex:1;">
                <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:.75rem;">
                    <span style="font-size:.85rem;font-weight:700;color:var(--text-mid);">
                        📄 Report File Status:
                    </span>
                    @if($reportExists)
                        <span style="background:#dcfce7;color:#166534;border:1px solid #86efac;padding:.2rem .75rem;border-radius:var(--radius-full);font-size:.78rem;font-weight:800;">
                            ✅ Ready (waste_report.txt)
                        </span>
                    @else
                        <span style="background:#fef2f2;color:#991b1b;border:1px solid #fca5a5;padding:.2rem .75rem;border-radius:var(--radius-full);font-size:.78rem;font-weight:800;">
                            ⚠️ Not Generated Yet
                        </span>
                    @endif
                </div>

                @if($reportExists)
                    <div style="font-size:.82rem;color:var(--text-muted);margin-bottom:1rem;">
                        📅 Last Updated: <strong>{{ $reportLastModified }}</strong><br>
                        📁 Location: <code>storage/app/python_reports/waste_report.txt</code>
                    </div>
                @endif

                <div style="display:flex;gap:1rem;flex-wrap:wrap;margin-top:auto;">
                    <!-- Action Form: Generate Report -->
                    <form action="{{ route('admin.analytics.generate-report') }}" method="POST" style="margin:0;">
                        @csrf
                        <button type="submit" style="background:var(--primary);color:#fff;border:none;padding:.75rem 1.4rem;border-radius:var(--radius-full);font-weight:700;font-size:.9rem;cursor:pointer;display:inline-flex;align-items:center;gap:.4rem;box-shadow:var(--shadow-sm);">
                            ⚙️ Generate Python Report
                        </button>
                    </form>

                    @if($reportExists)
                        <a href="{{ route('admin.analytics.view-report') }}" target="_blank" style="background:#fff;color:var(--primary-dark);border:1.5px solid var(--primary);padding:.75rem 1.4rem;border-radius:var(--radius-full);font-weight:700;font-size:.9rem;text-decoration:none;display:inline-flex;align-items:center;gap:.4rem;">
                            📄 View Report Output
                        </a>
                    @endif
                </div>
            </div>

            <div style="background:#fff7ed;border:1px solid #fed7aa;border-radius:var(--radius-md);padding:1rem 1.25rem;font-size:.85rem;color:#9a3412;line-height:1.5;">
                📌 <strong>Security Guarantee:</strong> All Python &amp; subprocess calls execute hardcoded, validated scripts with controlled parameters. No untrusted input is passed to shell commands.
            </div>
        </div>

    </div>

    <!-- Unit 4 Syllabus Viva Documentation Card -->
    <div style="background:#fff;border:1.5px solid var(--border);border-radius:var(--radius-xl);padding:2.5rem;box-shadow:var(--shadow-sm);">
        <div style="display:flex;align-items:center;gap:.6rem;margin-bottom:1.5rem;">
            <div style="font-size:1.8rem;">🎓</div>
            <div>
                <h3 style="font-family:'Outfit',sans-serif;font-size:1.4rem;font-weight:900;color:var(--text-dark);margin:0;">
                    Course 504 Unit 4 Syllabus Technical Integration Audit
                </h3>
                <div style="font-size:.88rem;color:var(--text-muted);font-weight:500;">
                    Complete coverage of Unit 4 PHP ↔ Python inter-process execution requirements for Viva verification.
                </div>
            </div>
        </div>

        <div style="display:grid;grid-template-columns:repeat(auto-fit, minmax(300px, 1fr));gap:1.5rem;">
            
            <!-- 4.1 PHP -> Python -->
            <div style="background:#f8fafc;border:1px solid var(--border-light);border-radius:var(--radius-lg);padding:1.5rem;">
                <h4 style="font-family:'Outfit',sans-serif;font-size:1.1rem;font-weight:800;color:var(--primary-dark);margin-bottom:.75rem;">
                    🔹 4.1 Executing Python using PHP
                </h4>
                <ul style="font-size:.88rem;color:var(--text-mid);line-height:1.7;list-style:disc;padding-left:1.25rem;">
                    <li><strong>4.1.1 Output Capture &amp; Echo:</strong> PHP captures Python <code>stdout</code> via <code>shell_exec()</code> and decodes JSON output.</li>
                    <li><strong>4.1.2 Command Security:</strong> Uses <code>escapeshellcmd()</code> for script path and <code>escapeshellarg()</code> for base64 payload.</li>
                    <li><strong>Script Path:</strong> <code>scripts/waste_insights.py</code></li>
                    <li><strong>Command Executed:</strong><br>
                        <code style="font-size:.78rem;background:#e2e8f0;padding:.2rem .4rem;border-radius:4px;word-break:break-all;">
                            {{ $academicDemo['prepared_command'] ?? 'N/A' }}
                        </code>
                    </li>
                </ul>
            </div>

            <!-- 4.2 Python -> PHP Subprocess & OS -->
            <div style="background:#f8fafc;border:1px solid var(--border-light);border-radius:var(--radius-lg);padding:1.5rem;">
                <h4 style="font-family:'Outfit',sans-serif;font-size:1.1rem;font-weight:800;color:#1d4ed8;margin-bottom:.75rem;">
                    🔹 4.2 Python subprocess &amp; os Modules
                </h4>
                <ul style="font-size:.88rem;color:var(--text-mid);line-height:1.7;list-style:disc;padding-left:1.25rem;">
                    <li><strong>4.2.1 subprocess:</strong> <code>check_call()</code>, <code>check_output()</code>, <code>decode()</code>, <code>Popen()</code>, <code>communicate()</code>, <code>split()</code> demonstrated in <code>scripts/run_php_report.py</code>.</li>
                    <li><strong>4.2.2 os module:</strong> <code>os.path.join()</code>, <code>os.path.exists()</code>, <code>os.path.isfile()</code>, <code>os.path.isdir()</code>, <code>os.makedirs()</code>, <code>os.mkdir()</code>, <code>os.listdir()</code>, <code>os.walk()</code>, <code>os.chdir()</code>, <code>os.write()</code>, <code>os.close()</code>.</li>
                    <li><strong>Report Path:</strong> <code>storage/app/python_reports/waste_report.txt</code></li>
                </ul>
            </div>

        </div>
    </div>

</div>
@endsection
