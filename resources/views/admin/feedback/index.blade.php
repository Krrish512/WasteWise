@extends('layouts.app')

@section('title', 'User Feedback Management – WasteWise Admin')

@section('content')
<section class="section" style="padding-block:2.5rem 5rem;">
    <div class="container">
        
        <!-- Header -->
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:2rem;flex-wrap:wrap;gap:1rem;">
            <div>
                <a href="{{ route('admin.dashboard') }}" style="color:var(--primary-dark);font-weight:600;font-size:.9rem;">← Back to Dashboard</a>
                <h1 style="font-family:'Outfit',sans-serif;font-size:2rem;font-weight:800;color:var(--text-dark);margin-top:.2rem;">
                    💬 User Feedback Management
                </h1>
                <p style="color:var(--text-muted);font-size:.95rem;">Review and manage ratings, reviews, and feature suggestions</p>
            </div>
        </div>

        <!-- Filters -->
        <form action="{{ route('admin.feedback.index') }}" method="GET" style="margin-bottom:1.5rem;display:flex;gap:1rem;flex-wrap:wrap;">
            <select name="rating" style="padding:.6rem 1rem;border:1px solid var(--border);border-radius:var(--radius-md);font-size:.9rem;background:#fff;min-width:160px;">
                <option value="">All Ratings</option>
                @for($r = 5; $r >= 1; $r--)
                    <option value="{{ $r }}" {{ request('rating') == $r ? 'selected' : '' }}>{{ $r }} Stars ⭐</option>
                @endfor
            </select>
            <select name="reviewed" style="padding:.6rem 1rem;border:1px solid var(--border);border-radius:var(--radius-md);font-size:.9rem;background:#fff;min-width:180px;">
                <option value="">All Statuses</option>
                <option value="0" {{ request('reviewed') === '0' ? 'selected' : '' }}>Pending Review</option>
                <option value="1" {{ request('reviewed') === '1' ? 'selected' : '' }}>Reviewed</option>
            </select>
            <button type="submit" style="background:var(--primary-dark);color:#fff;padding:.6rem 1.2rem;border:none;border-radius:var(--radius-md);font-weight:600;font-size:.9rem;cursor:pointer;">
                Filter
            </button>
            @if(request()->filled('rating') || request()->filled('reviewed'))
                <a href="{{ route('admin.feedback.index') }}" style="background:#f1f5f9;color:var(--text-dark);padding:.6rem 1rem;border-radius:var(--radius-md);font-weight:600;font-size:.9rem;">
                    Clear
                </a>
            @endif
        </form>

        <!-- Table Card -->
        <div style="background:var(--bg-white);border:1px solid var(--border);border-radius:var(--radius-xl);padding:2rem;box-shadow:var(--shadow-md);">
            @if($feedbacks->isEmpty())
                <div style="text-align:center;padding:3rem 1rem;color:var(--text-muted);">
                    <div style="font-size:3rem;margin-bottom:.5rem;">💬</div>
                    <p style="font-size:1.1rem;font-weight:600;">No feedback entries found.</p>
                </div>
            @else
                <div style="overflow-x:auto;">
                    <table style="width:100%;border-collapse:collapse;text-align:left;font-size:.95rem;">
                        <thead>
                            <tr style="background:var(--bg-base);border-bottom:2px solid var(--border-light);color:var(--text-muted);font-size:.85rem;text-transform:uppercase;letter-spacing:.05em;">
                                <th style="padding:.9rem 1rem;">User / Contributor</th>
                                <th style="padding:.9rem 1rem;">Rating</th>
                                <th style="padding:.9rem 1rem;">Helpful</th>
                                <th style="padding:.9rem 1rem;">Status</th>
                                <th style="padding:.9rem 1rem;">Date</th>
                                <th style="padding:.9rem 1rem;text-align:right;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($feedbacks as $item)
                                <tr style="border-bottom:1px solid var(--border-light);">
                                    <td style="padding:1rem;">
                                        <div style="font-weight:700;color:var(--text-dark);">
                                            {{ $item->user ? $item->user->name : ($item->name ?: 'Anonymous Guest') }}
                                        </div>
                                        <div style="font-size:.82rem;color:var(--text-muted);">
                                            {{ $item->user ? $item->user->email : ($item->email ?: 'No email') }}
                                        </div>
                                    </td>
                                    <td style="padding:1rem;font-weight:700;color:#d97706;white-space:nowrap;">
                                        {{ str_repeat('⭐', $item->rating) }}
                                    </td>
                                    <td style="padding:1rem;">
                                        @if($item->helpful)
                                            <span style="color:#166534;font-weight:600;font-size:.85rem;">Yes 👍</span>
                                        @else
                                            <span style="color:#991b1b;font-weight:600;font-size:.85rem;">No 👎</span>
                                        @endif
                                    </td>
                                    <td style="padding:1rem;">
                                        @if($item->is_reviewed)
                                            <span style="background:#dcfce7;color:#15803d;padding:.2rem .65rem;border-radius:var(--radius-full);font-size:.82rem;font-weight:700;">
                                                ✓ Reviewed
                                            </span>
                                        @else
                                            <span style="background:#fef3c7;color:#b45309;padding:.2rem .65rem;border-radius:var(--radius-full);font-size:.82rem;font-weight:700;">
                                                ⏳ Pending
                                            </span>
                                        @endif
                                    </td>
                                    <td style="padding:1rem;color:var(--text-muted);font-size:.85rem;white-space:nowrap;">
                                        {{ $item->created_at ? $item->created_at->format('M d, Y') : '—' }}
                                    </td>
                                    <td style="padding:1rem;text-align:right;white-space:nowrap;">
                                        <a href="{{ route('admin.feedback.show', $item) }}" style="background:#eff6ff;color:#1d4ed8;padding:.4rem .8rem;border-radius:var(--radius-md);font-weight:600;font-size:.85rem;margin-right:.4rem;">
                                            👁️ View Detail
                                        </a>
                                        <form action="{{ route('admin.feedback.toggle-reviewed', $item) }}" method="POST" style="display:inline;">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" style="background:{{ $item->is_reviewed ? '#f1f5f9' : '#ecfdf5' }};color:{{ $item->is_reviewed ? '#475569' : '#047857' }};border:1px solid {{ $item->is_reviewed ? '#cbd5e1' : '#a7f3d0' }};padding:.4rem .8rem;border-radius:var(--radius-md);font-weight:600;font-size:.85rem;cursor:pointer;margin-right:.4rem;">
                                                {{ $item->is_reviewed ? 'Unmark' : 'Mark Reviewed' }}
                                            </button>
                                        </form>
                                        <form action="{{ route('admin.feedback.destroy', $item) }}" method="POST" style="display:inline;" onsubmit="return confirm('Are you sure you want to delete this feedback?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" style="background:#fef2f2;color:#dc2626;border:1px solid #fca5a5;padding:.4rem .8rem;border-radius:var(--radius-md);font-weight:600;font-size:.85rem;cursor:pointer;">
                                                🗑️ Delete
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

    </div>
</section>
@endsection
