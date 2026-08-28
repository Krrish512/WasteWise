@extends('layouts.app')

@section('title', 'Manage Sustainability Tips – WasteWise Admin')

@section('content')
<section class="section" style="padding-block:2.5rem 5rem;">
    <div class="container">
        
        <!-- Header -->
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:2rem;flex-wrap:wrap;gap:1rem;">
            <div>
                <a href="{{ route('admin.dashboard') }}" style="color:var(--primary-dark);font-weight:600;font-size:.9rem;">← Back to Dashboard</a>
                <h1 style="font-family:'Outfit',sans-serif;font-size:2rem;font-weight:800;color:var(--text-dark);margin-top:.2rem;">
                    🌱 Sustainability Tips Management
                </h1>
                <p style="color:var(--text-muted);font-size:.95rem;">Create, edit, publish, and delete eco-friendly sustainability tips</p>
            </div>
            <a href="{{ route('admin.sustainability-tips.create') }}" style="background:var(--primary);color:#fff;padding:.75rem 1.4rem;border-radius:var(--radius-full);font-weight:700;font-size:.9rem;display:inline-flex;align-items:center;gap:.5rem;box-shadow:var(--shadow-sm);">
                ➕ Add Sustainability Tip
            </a>
        </div>

        <!-- Filter Bar -->
        <div style="background:var(--bg-white);border:1px solid var(--border);border-radius:var(--radius-xl);padding:1.25rem 1.5rem;box-shadow:var(--shadow-sm);margin-bottom:1.5rem;">
            <form action="{{ route('admin.sustainability-tips.index') }}" method="GET" style="display:flex;flex-wrap:wrap;gap:1rem;align-items:center;justify-content:space-between;">
                <div style="display:flex;flex-wrap:wrap;gap:1rem;align-items:center;flex:1;min-width:280px;">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="🔍 Search tip title or content..." style="flex:1;min-width:200px;padding:.6rem 1rem;border:1px solid var(--border);border-radius:var(--radius-md);font-size:.9rem;">
                    
                    <select name="category" style="padding:.6rem 1rem;border:1px solid var(--border);border-radius:var(--radius-md);font-size:.9rem;background:#fff;color:var(--text-dark);">
                        <option value="">All Categories</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat }}" {{ request('category') === $cat ? 'selected' : '' }}>{{ $cat }}</option>
                        @endforeach
                    </select>
                </div>
                
                <div style="display:flex;gap:.5rem;">
                    <button type="submit" style="background:var(--primary);color:#fff;padding:.6rem 1.2rem;border-radius:var(--radius-md);font-weight:600;font-size:.9rem;border:none;cursor:pointer;">
                        Filter
                    </button>
                    @if(request('search') || request('category'))
                        <a href="{{ route('admin.sustainability-tips.index') }}" style="background:#f1f5f9;color:var(--text-dark);padding:.6rem 1rem;border-radius:var(--radius-md);font-weight:600;font-size:.9rem;text-decoration:none;display:inline-block;">
                            Reset
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- Table Card -->
        <div style="background:var(--bg-white);border:1px solid var(--border);border-radius:var(--radius-xl);padding:2rem;box-shadow:var(--shadow-md);">
            @if($tips->isEmpty())
                <div style="text-align:center;padding:3rem 1rem;color:var(--text-muted);">
                    <div style="font-size:3rem;margin-bottom:.5rem;">🌱</div>
                    <p style="font-size:1.1rem;font-weight:600;">No sustainability tips found.</p>
                </div>
            @else
                <div style="overflow-x:auto;">
                    <table style="width:100%;border-collapse:collapse;text-align:left;font-size:.95rem;">
                        <thead>
                            <tr style="background:var(--bg-base);border-bottom:2px solid var(--border-light);color:var(--text-muted);font-size:.85rem;text-transform:uppercase;letter-spacing:.05em;">
                                <th style="padding:.9rem 1rem;">Tip Info</th>
                                <th style="padding:.9rem 1rem;">Category</th>
                                <th style="padding:.9rem 1rem;">Status</th>
                                <th style="padding:.9rem 1rem;">Published Date</th>
                                <th style="padding:.9rem 1rem;text-align:right;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($tips as $tip)
                                <tr style="border-bottom:1px solid var(--border-light);">
                                    <td style="padding:1rem;font-weight:700;color:var(--text-dark);max-width:280px;">
                                        <div style="display:flex;align-items:center;gap:.75rem;">
                                            @if($tip->image)
                                                <img src="{{ asset($tip->image) }}" alt="" style="width:42px;height:42px;object-fit:cover;border-radius:var(--radius-md);border:1px solid var(--border);flex-shrink:0;">
                                            @else
                                                <div style="width:42px;height:42px;background:#ecfdf5;color:#047857;border-radius:var(--radius-md);display:flex;align-items:center;justify-content:center;font-size:1.2rem;flex-shrink:0;border:1px solid #a7f3d0;">
                                                    🌱
                                                </div>
                                            @endif
                                            <div>
                                                <div>{{ $tip->title }}</div>
                                                <div style="font-size:.78rem;color:var(--text-muted);font-weight:400;">/sustainability-tips/{{ $tip->slug }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td style="padding:1rem;">
                                        <span style="background:#eff6ff;color:#1e40af;border:1px solid #bfdbfe;padding:.25rem .75rem;border-radius:var(--radius-full);font-size:.8rem;font-weight:600;">
                                            🏷️ {{ $tip->category }}
                                        </span>
                                    </td>
                                    <td style="padding:1rem;">
                                        @if($tip->is_published)
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
                                        {{ $tip->published_at ? $tip->published_at->format('M d, Y H:i') : '—' }}
                                    </td>
                                    <td style="padding:1rem;text-align:right;white-space:nowrap;">
                                        <form action="{{ route('admin.sustainability-tips.toggle-published', $tip) }}" method="POST" style="display:inline;margin-right:.4rem;">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" style="background:{{ $tip->is_published ? '#fff7ed' : '#f0fdf4' }};color:{{ $tip->is_published ? '#c2410c' : '#15803d' }};border:1px solid {{ $tip->is_published ? '#ffedd5' : '#bbf7d0' }};padding:.4rem .8rem;border-radius:var(--radius-md);font-weight:600;font-size:.85rem;cursor:pointer;">
                                                {{ $tip->is_published ? '🚫 Unpublish' : '🚀 Publish' }}
                                            </button>
                                        </form>

                                        <a href="{{ route('admin.sustainability-tips.edit', $tip) }}" style="background:#f1f5f9;color:var(--text-dark);padding:.4rem .8rem;border-radius:var(--radius-md);font-weight:600;font-size:.85rem;margin-right:.4rem;text-decoration:none;display:inline-block;">
                                            ✏️ Edit
                                        </a>

                                        <form action="{{ route('admin.sustainability-tips.destroy', $tip) }}" method="POST" style="display:inline;" onsubmit="return confirm('Are you sure you want to delete this tip?');">
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

                @if($tips->hasPages())
                    <div style="margin-top:1.5rem;">
                        {{ $tips->links() }}
                    </div>
                @endif
            @endif
        </div>

    </div>
</section>
@endsection
