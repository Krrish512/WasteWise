@extends('layouts.app')

@section('title', '🧾 Smart Waste Analytics Report – WasteWise Admin')

@section('content')
<div class="admin-dash-header" style="background:linear-gradient(135deg, #065f46 0%, #047857 60%, #10b981 100%);color:white;padding:2.5rem 0;margin-bottom:2.5rem;">
    <div class="container" style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:1rem;">
        <div>
            <div style="display:inline-flex;align-items:center;gap:.4rem;background:rgba(255,255,255,.15);backdrop-filter:blur(8px);padding:.3rem .8rem;border-radius:var(--radius-full);font-size:.8rem;font-weight:700;margin-bottom:.6rem;color:#dcfce7;">
                🐍 Smart Waste Analytics Report
            </div>
            <h1 class="admin-dash-title" style="font-family:'Outfit',sans-serif;font-size:2.2rem;font-weight:900;margin:0;">
                🧾 Executive Analytics Report
            </h1>
            <p class="admin-dash-sub" style="color:#a7f3d0;font-size:1rem;margin-top:.4rem;max-width:650px;">
                Generated successfully using WasteWise analytics processing.
            </p>
        </div>
        <div style="display:flex;gap:.75rem;flex-wrap:wrap;align-items:center;">
            <a href="{{ route('admin.analytics.index') }}" style="background:rgba(255,255,255,.2);color:#fff;padding:.6rem 1.2rem;border-radius:var(--radius-full);font-weight:700;font-size:.9rem;text-decoration:none;border:1px solid rgba(255,255,255,.3);">
                ← Back to Analytics
            </a>
            @if($reportExists)
                <a href="{{ route('admin.analytics.download-report') }}" style="background:#fff;color:var(--primary-dark);padding:.6rem 1.2rem;border-radius:var(--radius-full);font-weight:800;font-size:.9rem;text-decoration:none;box-shadow:var(--shadow-sm);display:inline-flex;align-items:center;gap:.4rem;">
                    📥 Download Report
                </a>
            @endif
        </div>
    </div>
</div>

<div class="container" style="padding-bottom:5rem;">

    @if(!$reportExists)
        <!-- Report Missing State -->
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
        <!-- Report Available Content -->

        <!-- Report Status & Metadata Bar -->
        <div style="background:#fff;border:1px solid var(--border-light);border-radius:var(--radius-xl);padding:1.25rem 1.75rem;margin-bottom:2rem;box-shadow:var(--shadow-sm);display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:1rem;">
            <div style="display:flex;align-items:center;gap:1rem;">
                <span style="background:#dcfce7;color:#166534;border:1px solid #86efac;padding:.3rem .85rem;border-radius:var(--radius-full);font-size:.82rem;font-weight:800;">
                    ✅ Report Active
                </span>
                <span style="color:var(--text-muted);font-size:.88rem;">
                    📅 Last Generated: <strong style="color:var(--text-dark);">{{ $reportLastModified }}</strong>
                </span>
                <span style="color:var(--text-muted);font-size:.88rem;">
                    📦 Size: <strong style="color:var(--text-dark);">{{ number_format($reportSize / 1024, 2) }} KB</strong>
                </span>
            </div>
            <div>
                <a href="{{ route('admin.analytics.download-report') }}" style="color:var(--primary-dark);font-weight:700;font-size:.9rem;text-decoration:none;display:inline-flex;align-items:center;gap:.3rem;">
                    📥 Download Raw Report File (.txt) →
                </a>
            </div>
        </div>

        <!-- Metric Summary Cards -->
        <div style="display:grid;grid-template-columns:repeat(auto-fit, minmax(220px, 1fr));gap:1.5rem;margin-bottom:2.5rem;">
            
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

        <!-- Python Insight Banner -->
        <div style="background:linear-gradient(135deg, #ecfdf5 0%, #d1fae5 100%);border:1.5px solid #a7f3d0;border-radius:var(--radius-xl);padding:1.75rem 2rem;margin-bottom:2.5rem;box-shadow:var(--shadow-sm);display:flex;align-items:center;gap:1.5rem;flex-wrap:wrap;">
            <div style="width:56px;height:56px;border-radius:50%;background:#047857;color:#fff;display:flex;align-items:center;justify-content:center;font-size:1.8rem;flex-shrink:0;box-shadow:0 4px 12px rgba(4,120,87,.3);">
                💡
            </div>
            <div style="flex:1;">
                <div style="font-size:.8rem;font-weight:800;text-transform:uppercase;letter-spacing:.05em;color:#065f46;margin-bottom:.3rem;">
                    💡 Python Generated Insight
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

        <!-- Category Search Breakdown -->
        <div style="background:#fff;border:1px solid var(--border-light);border-radius:var(--radius-xl);padding:2rem;box-shadow:var(--shadow-sm);margin-bottom:2.5rem;">
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

        <!-- Action Footer -->
        <div style="display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:1rem;background:#fff;border:1px solid var(--border-light);border-radius:var(--radius-xl);padding:1.5rem 2rem;box-shadow:var(--shadow-sm);">
            <a href="{{ route('admin.analytics.index') }}" style="background:#f1f5f9;color:var(--text-dark);padding:.75rem 1.4rem;border-radius:var(--radius-full);font-weight:700;font-size:.9rem;text-decoration:none;">
                ← Back to Analytics
            </a>
            <div style="display:flex;gap:1rem;flex-wrap:wrap;">
                <form action="{{ route('admin.analytics.generate-report') }}" method="POST" style="margin:0;">
                    @csrf
                    <button type="submit" style="background:var(--primary);color:#fff;border:none;padding:.75rem 1.4rem;border-radius:var(--radius-full);font-weight:700;font-size:.9rem;cursor:pointer;display:inline-flex;align-items:center;gap:.4rem;">
                        ⚙️ Re-generate Report
                    </button>
                </form>

                <a href="{{ route('admin.analytics.download-report') }}" style="background:var(--primary-dark);color:#fff;padding:.75rem 1.4rem;border-radius:var(--radius-full);font-weight:700;font-size:.9rem;text-decoration:none;display:inline-flex;align-items:center;gap:.4rem;">
                    📥 Download Report (.txt)
                </a>
            </div>
        </div>

    @endif

</div>
@endsection
