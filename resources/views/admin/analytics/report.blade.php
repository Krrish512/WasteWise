@extends('layouts.app')

@section('title', '🧾 JSON Analytics Report – WasteWise Admin')

@section('content')
<div class="admin-dash-header" style="background:linear-gradient(135deg, #065f46 0%, #047857 60%, #10b981 100%);color:white;padding:2.5rem 0;margin-bottom:2.5rem;">
    <div class="container" style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:1rem;">
        <div>
            <div style="display:inline-flex;align-items:center;gap:.4rem;background:rgba(255,255,255,.15);backdrop-filter:blur(8px);padding:.3rem .8rem;border-radius:var(--radius-full);font-size:.8rem;font-weight:700;margin-bottom:.6rem;color:#dcfce7;">
                🐍 Python Generated JSON Report
            </div>
            <h1 class="admin-dash-title" style="font-family:'Outfit',sans-serif;font-size:2.2rem;font-weight:900;margin:0;">
                🧾 Smart Waste JSON Analytics Report
            </h1>
            <p class="admin-dash-sub" style="color:#a7f3d0;font-size:1rem;margin-top:.4rem;max-width:650px;">
                Raw structured JSON analytics data processed dynamically by WasteWise Python integration.
            </p>
        </div>
        <div style="display:flex;gap:.75rem;flex-wrap:wrap;align-items:center;">
            <a href="{{ route('admin.analytics.index') }}" style="background:rgba(255,255,255,.2);color:#fff;padding:.6rem 1.2rem;border-radius:var(--radius-full);font-weight:700;font-size:.9rem;text-decoration:none;border:1px solid rgba(255,255,255,.3);">
                ← Back to Analytics
            </a>
            @if($reportExists)
                <a href="{{ route('admin.analytics.download-report') }}" style="background:#fff;color:var(--primary-dark);padding:.6rem 1.2rem;border-radius:var(--radius-full);font-weight:800;font-size:.9rem;text-decoration:none;box-shadow:var(--shadow-sm);display:inline-flex;align-items:center;gap:.4rem;">
                    📥 Download JSON Report
                </a>
            @endif
        </div>
    </div>
</div>

<div class="container" style="padding-bottom:5rem;">

    @if(!$reportExists)
        <!-- Missing Report State -->
        <div style="background:#fff;border:1.5px solid var(--border);border-radius:var(--radius-xl);padding:4rem 2rem;text-align:center;max-width:650px;margin:2rem auto;box-shadow:var(--shadow-sm);">
            <div style="font-size:3.5rem;margin-bottom:1rem;color:var(--text-muted);">📄</div>
            <h2 style="font-family:'Outfit',sans-serif;font-size:1.6rem;font-weight:800;color:var(--text-dark);margin-bottom:.5rem;">
                Report Not Available Yet
            </h2>
            <p style="color:var(--text-muted);font-size:1rem;margin-bottom:2rem;">
                Report not available yet. Please generate a report first.
            </p>
            <form action="{{ route('admin.analytics.generate-report') }}" method="POST" style="margin:0;">
                @csrf
                <button type="submit" style="background:var(--primary);color:#fff;border:none;padding:.85rem 1.8rem;border-radius:var(--radius-full);font-weight:700;font-size:1rem;cursor:pointer;display:inline-flex;align-items:center;gap:.5rem;box-shadow:var(--shadow-md);">
                    ⚙️ Generate Report Now
                </button>
            </form>
        </div>
    @else
        <!-- Available Report Content -->

        <!-- Metadata & Status Bar -->
        <div style="background:#fff;border:1px solid var(--border-light);border-radius:var(--radius-xl);padding:1.25rem 1.75rem;margin-bottom:1.5rem;box-shadow:var(--shadow-sm);display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:1rem;">
            <div style="display:flex;align-items:center;gap:1rem;">
                <span style="background:#dcfce7;color:#166534;border:1px solid #86efac;padding:.3rem .85rem;border-radius:var(--radius-full);font-size:.82rem;font-weight:800;">
                    ✅ waste_report.json
                </span>
                <span style="color:var(--text-muted);font-size:.88rem;">
                    📅 Generated: <strong style="color:var(--text-dark);">{{ $reportLastModified }}</strong>
                </span>
                <span style="color:var(--text-muted);font-size:.88rem;">
                    📦 Size: <strong style="color:var(--text-dark);">{{ number_format($reportSize / 1024, 2) }} KB</strong>
                </span>
            </div>
            <div style="display:flex;gap:1rem;">
                <form action="{{ route('admin.analytics.generate-report') }}" method="POST" style="margin:0;">
                    @csrf
                    <button type="submit" style="background:var(--primary);color:#fff;border:none;padding:.5rem 1.1rem;border-radius:var(--radius-full);font-weight:700;font-size:.85rem;cursor:pointer;display:inline-flex;align-items:center;gap:.4rem;">
                        ⚙️ Re-generate Report
                    </button>
                </form>
                <a href="{{ route('admin.analytics.download-report') }}" style="background:var(--primary-dark);color:#fff;padding:.5rem 1.1rem;border-radius:var(--radius-full);font-weight:700;font-size:.85rem;text-decoration:none;display:inline-flex;align-items:center;gap:.4rem;">
                    📥 Download JSON Report
                </a>
            </div>
        </div>

        <!-- Formatted JSON Code Display Panel -->
        <div style="background:#0f172a;border:1px solid #1e293b;border-radius:var(--radius-xl);padding:2rem;box-shadow:var(--shadow-md);overflow-x:auto;">
            <div style="display:flex;align-items:center;justify-content:space-between;border-bottom:1px solid #334155;padding-bottom:1rem;margin-bottom:1.25rem;">
                <span style="font-family:'Outfit',sans-serif;color:#38bdf8;font-weight:700;font-size:.95rem;display:flex;align-items:center;gap:.5rem;">
                    📄 waste_report.json
                </span>
                <span style="color:#94a3b8;font-size:.8rem;font-family:monospace;">
                    application/json
                </span>
            </div>
            <pre style="margin:0;font-family:'Consolas', 'Monaco', 'Courier New', monospace;font-size:.92rem;color:#f8fafc;line-height:1.6;white-space:pre-wrap;word-break:break-all;"><code>{{ $reportJsonString }}</code></pre>
        </div>

        <!-- Footer Back Link -->
        <div style="margin-top:2rem;display:flex;justify-content:space-between;align-items:center;">
            <a href="{{ route('admin.analytics.index') }}" style="background:#fff;border:1px solid var(--border);color:var(--text-dark);padding:.75rem 1.4rem;border-radius:var(--radius-full);font-weight:700;font-size:.9rem;text-decoration:none;box-shadow:var(--shadow-sm);">
                ← Back to Analytics
            </a>
            <a href="{{ route('admin.analytics.download-report') }}" style="color:var(--primary-dark);font-weight:700;font-size:.9rem;text-decoration:none;">
                📥 Download JSON Report (waste_report.json) →
            </a>
        </div>

    @endif

</div>
@endsection
