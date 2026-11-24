@extends('layouts.app')

@section('title', 'User Feedback & Centre Reports – WasteWise Admin')

@section('content')
<section class="section" style="padding-block:2.5rem 5rem;">
    <div class="container">
        
        <!-- Header -->
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:2rem;flex-wrap:wrap;gap:1rem;">
            <div>
                <a href="{{ route('admin.dashboard') }}" style="color:var(--primary-dark);font-weight:600;font-size:.9rem;">← Back to Dashboard</a>
                <h1 style="font-family:'Outfit',sans-serif;font-size:2rem;font-weight:800;color:var(--text-dark);margin-top:.2rem;">
                    💬 User Feedback & Centre Issue Reports
                </h1>
                <p style="color:var(--text-muted);font-size:.95rem;">Review citizen feedback, suggestions, and reported issues for collection centres</p>
            </div>
            @if(isset($centresNeedingReviewCount) && $centresNeedingReviewCount > 0)
                <div style="background:#fee2e2;border:1px solid #fca5a5;padding:.6rem 1.2rem;border-radius:var(--radius-lg);display:flex;align-items:center;gap:.75rem;">
                    <span style="font-size:1.4rem;">🚨</span>
                    <div>
                        <div style="font-weight:700;color:#991b1b;font-size:.92rem;">
                            {{ $centresNeedingReviewCount }} Centre {{ Str::plural('Report', $centresNeedingReviewCount) }} Needing Review
                        </div>
                        <a href="{{ route('admin.feedback.index', ['type' => 'centres', 'reviewed' => '0']) }}" style="font-size:.82rem;color:#b91c1c;font-weight:600;text-decoration:underline;">
                            Filter Unresolved Reports →
                        </a>
                    </div>
                </div>
            @endif
        </div>

        @if(session('success'))
            <div style="background:#ecfdf5;border:1px solid #a7f3d0;color:#065f46;padding:.9rem 1.25rem;border-radius:var(--radius-md);margin-bottom:1.5rem;font-weight:600;">
                ✓ {{ session('success') }}
            </div>
        @endif

        <!-- Filter tabs / pills -->
        <div style="display:flex;gap:.5rem;margin-bottom:1.25rem;flex-wrap:wrap;">
            <a href="{{ route('admin.feedback.index') }}" 
               style="padding:.5rem 1rem;border-radius:var(--radius-md);font-weight:600;font-size:.88rem;text-decoration:none;
               {{ !request('type') ? 'background:var(--primary-dark);color:#fff;' : 'background:#fff;border:1px solid var(--border);color:var(--text-dark);' }}">
                All Items ({{ $feedbacks->count() }})
            </a>
            <a href="{{ route('admin.feedback.index', ['type' => 'centres']) }}" 
               style="padding:.5rem 1rem;border-radius:var(--radius-md);font-weight:600;font-size:.88rem;text-decoration:none;
               {{ request('type') === 'centres' ? 'background:var(--primary-dark);color:#fff;' : 'background:#fff;border:1px solid var(--border);color:var(--text-dark);' }}">
                📍 Centre Issue Reports
                @if(isset($centresNeedingReviewCount) && $centresNeedingReviewCount > 0)
                    <span style="background:#ef4444;color:#fff;border-radius:999px;padding:.1rem .45rem;font-size:.75rem;margin-left:.3rem;">
                        {{ $centresNeedingReviewCount }}
                    </span>
                @endif
            </a>
            <a href="{{ route('admin.feedback.index', ['type' => 'general']) }}" 
               style="padding:.5rem 1rem;border-radius:var(--radius-md);font-weight:600;font-size:.88rem;text-decoration:none;
               {{ request('type') === 'general' ? 'background:var(--primary-dark);color:#fff;' : 'background:#fff;border:1px solid var(--border);color:var(--text-dark);' }}">
                ⭐ General Feedback
            </a>
        </div>

        <!-- Filters Form -->
        <form action="{{ route('admin.feedback.index') }}" method="GET" style="margin-bottom:1.5rem;display:flex;gap:1rem;flex-wrap:wrap;align-items:center;">
            @if(request('type'))
                <input type="hidden" name="type" value="{{ request('type') }}">
            @endif
            <select name="rating" style="padding:.6rem 1rem;border:1px solid var(--border);border-radius:var(--radius-md);font-size:.9rem;background:#fff;min-width:160px;">
                <option value="">All Ratings</option>
                @for($r = 5; $r >= 1; $r--)
                    <option value="{{ $r }}" {{ request('rating') == $r ? 'selected' : '' }}>{{ $r }} Stars ⭐</option>
                @endfor
            </select>
            <select name="reviewed" style="padding:.6rem 1rem;border:1px solid var(--border);border-radius:var(--radius-md);font-size:.9rem;background:#fff;min-width:180px;">
                <option value="">All Statuses</option>
                <option value="0" {{ request('reviewed') === '0' ? 'selected' : '' }}>Pending Review</option>
                <option value="1" {{ request('reviewed') === '1' ? 'selected' : '' }}>Reviewed / Resolved</option>
            </select>
            <button type="submit" style="background:var(--primary-dark);color:#fff;padding:.6rem 1.2rem;border:none;border-radius:var(--radius-md);font-weight:600;font-size:.9rem;cursor:pointer;">
                Filter
            </button>
            @if(request()->filled('rating') || request()->filled('reviewed') || request()->filled('type'))
                <a href="{{ route('admin.feedback.index') }}" style="background:#f1f5f9;color:var(--text-dark);padding:.6rem 1rem;border-radius:var(--radius-md);font-weight:600;font-size:.9rem;text-decoration:none;">
                    Clear
                </a>
            @endif
        </form>

        <!-- Table Card -->
        <div style="background:var(--bg-white);border:1px solid var(--border);border-radius:var(--radius-xl);padding:1.5rem;box-shadow:var(--shadow-md);">
            @if($feedbacks->isEmpty())
                <div style="text-align:center;padding:3rem 1rem;color:var(--text-muted);">
                    <div style="font-size:3rem;margin-bottom:.5rem;">💬</div>
                    <p style="font-size:1.1rem;font-weight:600;">No feedback entries found matching your criteria.</p>
                </div>
            @else
                <div style="overflow-x:auto;">
                    <table style="width:100%;border-collapse:collapse;text-align:left;font-size:.92rem;">
                        <thead>
                            <tr style="background:var(--bg-base);border-bottom:2px solid var(--border-light);color:var(--text-muted);font-size:.82rem;text-transform:uppercase;letter-spacing:.05em;">
                                <th style="padding:.9rem 1rem;">Type & Submitter</th>
                                <th style="padding:.9rem 1rem;">Target Centre / Rating</th>
                                <th style="padding:.9rem 1rem;">Issue / Message</th>
                                <th style="padding:.9rem 1rem;">Status</th>
                                <th style="padding:.9rem 1rem;">Date</th>
                                <th style="padding:.9rem 1rem;text-align:right;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($feedbacks as $item)
                                <tr style="border-bottom:1px solid var(--border-light); {{ $item->collection_centre_id && !$item->is_reviewed ? 'background:#fffbeb;' : '' }}">
                                    <!-- Type & Submitter -->
                                    <td style="padding:1rem;">
                                        <div style="display:flex;align-items:center;gap:.4rem;margin-bottom:.25rem;">
                                            @if($item->collection_centre_id)
                                                <span style="background:#fee2e2;color:#991b1b;font-weight:700;font-size:.75rem;padding:.15rem .5rem;border-radius:var(--radius-full);">
                                                    🚨 Centre Issue
                                                </span>
                                            @else
                                                <span style="background:#e0f2fe;color:#0369a1;font-weight:700;font-size:.75rem;padding:.15rem .5rem;border-radius:var(--radius-full);">
                                                    ⭐ General
                                                </span>
                                            @endif
                                        </div>
                                        <div style="font-weight:700;color:var(--text-dark);">
                                            {{ $item->user ? $item->user->name : ($item->name ?: 'Anonymous Citizen') }}
                                        </div>
                                        <div style="font-size:.8rem;color:var(--text-muted);">
                                            {{ $item->user ? $item->user->email : ($item->email ?: 'No email') }}
                                        </div>
                                    </td>

                                    <!-- Target Centre / Rating -->
                                    <td style="padding:1rem;">
                                        @if($item->collectionCentre)
                                            <div style="font-weight:700;color:var(--text-dark);">
                                                <a href="{{ route('admin.collection-centres.edit', $item->collectionCentre) }}" style="color:var(--primary-dark);text-decoration:underline;">
                                                    {{ $item->collectionCentre->name }}
                                                </a>
                                            </div>
                                            <div style="font-size:.8rem;color:var(--text-muted);">
                                                📍 {{ $item->collectionCentre->city?->name ?? '—' }} (Status: <span style="font-weight:600;">{{ $item->collectionCentre->status }}</span>)
                                            </div>
                                        @else
                                            <div style="font-weight:700;color:#d97706;white-space:nowrap;">
                                                {{ str_repeat('⭐', $item->rating) }}
                                            </div>
                                            <div style="font-size:.8rem;color:var(--text-muted);">
                                                Helpful: {{ $item->helpful ? 'Yes 👍' : 'No 👎' }}
                                            </div>
                                        @endif
                                    </td>

                                    <!-- Issue / Message -->
                                    <td style="padding:1rem;max-width:320px;">
                                        @if($item->issue_type)
                                            <span style="display:inline-block;background:#fef3c7;color:#92400e;font-size:.78rem;font-weight:700;padding:.15rem .55rem;border-radius:var(--radius-sm);margin-bottom:.3rem;">
                                                ⚠️ {{ ucwords(str_replace('_', ' ', $item->issue_type)) }}
                                            </span>
                                        @endif
                                        <div style="font-size:.86rem;color:var(--text-dark);line-height:1.4;word-break:break-word;">
                                            {{ Str::limit($item->message, 120) }}
                                        </div>
                                    </td>

                                    <!-- Status -->
                                    <td style="padding:1rem;white-space:nowrap;">
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

                                    <!-- Date -->
                                    <td style="padding:1rem;color:var(--text-muted);font-size:.85rem;white-space:nowrap;">
                                        {{ $item->created_at ? $item->created_at->format('M d, Y') : '—' }}
                                    </td>

                                    <!-- Actions -->
                                    <td style="padding:1rem;text-align:right;white-space:nowrap;">
                                        <a href="{{ route('admin.feedback.show', $item) }}" style="background:#eff6ff;color:#1d4ed8;padding:.35rem .7rem;border-radius:var(--radius-md);font-weight:600;font-size:.82rem;margin-right:.3rem;text-decoration:none;">
                                            👁️ Detail
                                        </a>

                                        @if($item->collection_centre_id)
                                            <a href="{{ route('admin.collection-centres.edit', $item->collection_centre_id) }}" style="background:#f0fdf4;color:#166534;border:1px solid #bbf7d0;padding:.35rem .7rem;border-radius:var(--radius-md);font-weight:600;font-size:.82rem;margin-right:.3rem;text-decoration:none;">
                                                ✏️ Edit Centre
                                            </a>

                                            @if(!$item->is_reviewed)
                                                <form action="{{ route('admin.feedback.mark-centre-pending', $item) }}" method="POST" style="display:inline;" onsubmit="return confirm('Mark this collection centre as pending_verification and resolve report?');">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit" style="background:#fff7ed;color:#c2410c;border:1px solid #fed7aa;padding:.35rem .7rem;border-radius:var(--radius-md);font-weight:600;font-size:.82rem;cursor:pointer;margin-right:.3rem;" title="Flag centre for review">
                                                        ⏳ Mark Pending
                                                    </button>
                                                </form>

                                                <form action="{{ route('admin.feedback.resolve', $item) }}" method="POST" style="display:inline;">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit" style="background:#ecfdf5;color:#047857;border:1px solid #a7f3d0;padding:.35rem .7rem;border-radius:var(--radius-md);font-weight:600;font-size:.82rem;cursor:pointer;margin-right:.3rem;">
                                                        ✅ Resolve
                                                    </button>
                                                </form>
                                            @endif
                                        @else
                                            <form action="{{ route('admin.feedback.toggle-reviewed', $item) }}" method="POST" style="display:inline;">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" style="background:{{ $item->is_reviewed ? '#f1f5f9' : '#ecfdf5' }};color:{{ $item->is_reviewed ? '#475569' : '#047857' }};border:1px solid {{ $item->is_reviewed ? '#cbd5e1' : '#a7f3d0' }};padding:.35rem .7rem;border-radius:var(--radius-md);font-weight:600;font-size:.82rem;cursor:pointer;margin-right:.3rem;">
                                                    {{ $item->is_reviewed ? 'Unmark' : 'Mark Reviewed' }}
                                                </button>
                                            </form>
                                        @endif

                                        <form action="{{ route('admin.feedback.destroy', $item) }}" method="POST" style="display:inline;" onsubmit="return confirm('Are you sure you want to delete this record?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" style="background:#fef2f2;color:#dc2626;border:1px solid #fca5a5;padding:.35rem .7rem;border-radius:var(--radius-md);font-weight:600;font-size:.82rem;cursor:pointer;">
                                                🗑️
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
