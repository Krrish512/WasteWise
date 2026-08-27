@extends('layouts.app')

@section('title', 'Manage Announcements – WasteWise Admin')

@section('content')
<section class="section" style="padding-block:2.5rem 5rem;">
    <div class="container">
        
        <!-- Header -->
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:2rem;flex-wrap:wrap;gap:1rem;">
            <div>
                <a href="{{ route('admin.dashboard') }}" style="color:var(--primary-dark);font-weight:600;font-size:.9rem;">← Back to Dashboard</a>
                <h1 style="font-family:'Outfit',sans-serif;font-size:2rem;font-weight:800;color:var(--text-dark);margin-top:.2rem;">
                    📢 Announcements & Updates
                </h1>
                <p style="color:var(--text-muted);font-size:.95rem;">Create, publish, edit, and manage public updates</p>
            </div>
            <a href="{{ route('admin.announcements.create') }}" style="background:var(--primary);color:#fff;padding:.75rem 1.4rem;border-radius:var(--radius-full);font-weight:700;font-size:.9rem;display:inline-flex;align-items:center;gap:.5rem;box-shadow:var(--shadow-sm);">
                ➕ Add Announcement
            </a>
        </div>

        <!-- Table Card -->
        <div style="background:var(--bg-white);border:1px solid var(--border);border-radius:var(--radius-xl);padding:2rem;box-shadow:var(--shadow-md);">
            @if($announcements->isEmpty())
                <div style="text-align:center;padding:3rem 1rem;color:var(--text-muted);">
                    <div style="font-size:3rem;margin-bottom:.5rem;">📢</div>
                    <p style="font-size:1.1rem;font-weight:600;">No announcements found.</p>
                </div>
            @else
                <div style="overflow-x:auto;">
                    <table style="width:100%;border-collapse:collapse;text-align:left;font-size:.95rem;">
                        <thead>
                            <tr style="background:var(--bg-base);border-bottom:2px solid var(--border-light);color:var(--text-muted);font-size:.85rem;text-transform:uppercase;letter-spacing:.05em;">
                                <th style="padding:.9rem 1rem;">Title</th>
                                <th style="padding:.9rem 1rem;">Status</th>
                                <th style="padding:.9rem 1rem;">Published Date</th>
                                <th style="padding:.9rem 1rem;">Created Date</th>
                                <th style="padding:.9rem 1rem;text-align:right;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($announcements as $announcement)
                                <tr style="border-bottom:1px solid var(--border-light);">
                                    <td style="padding:1rem;font-weight:700;color:var(--text-dark);max-width:280px;">
                                        <div style="display:flex;align-items:center;gap:.5rem;">
                                            @if($announcement->image)
                                                <img src="{{ asset($announcement->image) }}" alt="" style="width:36px;height:36px;object-fit:cover;border-radius:var(--radius-sm);border:1px solid var(--border);">
                                            @endif
                                            <div>
                                                <div>{{ $announcement->title }}</div>
                                                <div style="font-size:.78rem;color:var(--text-muted);font-weight:400;">/announcements/{{ $announcement->slug }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td style="padding:1rem;">
                                        @if($announcement->is_published)
                                            <span style="background:#dcfce7;color:#166534;padding:.25rem .75rem;border-radius:var(--radius-full);font-size:.8rem;font-weight:700;display:inline-flex;align-items:center;gap:.3rem;">
                                                🟢 Published
                                            </span>
                                        @else
                                            <span style="background:#fef3c7;color:#92400e;padding:.25rem .75rem;border-radius:var(--radius-full);font-size:.8rem;font-weight:700;display:inline-flex;align-items:center;gap:.3rem;">
                                                🟠 Draft
                                            </span>
                                        @endif
                                    </td>
                                    <td style="padding:1rem;color:var(--text-mid);font-size:.88rem;">
                                        {{ $announcement->published_at ? $announcement->published_at->format('M d, Y H:i') : '—' }}
                                    </td>
                                    <td style="padding:1rem;color:var(--text-mid);font-size:.88rem;">
                                        {{ $announcement->created_at ? $announcement->created_at->format('M d, Y') : '—' }}
                                    </td>
                                    <td style="padding:1rem;text-align:right;white-space:nowrap;">
                                        <form action="{{ route('admin.announcements.toggle-published', $announcement) }}" method="POST" style="display:inline;margin-right:.4rem;">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" style="background:{{ $announcement->is_published ? '#fff7ed' : '#f0fdf4' }};color:{{ $announcement->is_published ? '#c2410c' : '#15803d' }};border:1px solid {{ $announcement->is_published ? '#ffedd5' : '#bbf7d0' }};padding:.4rem .8rem;border-radius:var(--radius-md);font-weight:600;font-size:.85rem;cursor:pointer;">
                                                {{ $announcement->is_published ? '🚫 Unpublish' : '🚀 Publish' }}
                                            </button>
                                        </form>

                                        <a href="{{ route('admin.announcements.edit', $announcement) }}" style="background:#f1f5f9;color:var(--text-dark);padding:.4rem .8rem;border-radius:var(--radius-md);font-weight:600;font-size:.85rem;margin-right:.4rem;text-decoration:none;display:inline-block;">
                                            ✏️ Edit
                                        </a>

                                        <form action="{{ route('admin.announcements.destroy', $announcement) }}" method="POST" style="display:inline;" onsubmit="return confirm('Are you sure you want to delete this announcement?');">
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

                @if($announcements->hasPages())
                    <div style="margin-top:1.5rem;">
                        {{ $announcements->links() }}
                    </div>
                @endif
            @endif
        </div>

    </div>
</section>
@endsection
