@extends('layouts.app')

@section('title', 'Manage Waste Items – WasteWise Admin')

@section('content')
<section class="section" style="padding-block:2.5rem 5rem;">
    <div class="container">
        
        <!-- Header -->
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:2rem;flex-wrap:wrap;gap:1rem;">
            <div>
                <a href="{{ route('admin.dashboard') }}" style="color:var(--primary-dark);font-weight:600;font-size:.9rem;">← Back to Dashboard</a>
                <h1 style="font-family:'Outfit',sans-serif;font-size:2rem;font-weight:800;color:var(--text-dark);margin-top:.2rem;">
                    📦 Waste Items Catalog
                </h1>
                <p style="color:var(--text-muted);font-size:.95rem;">Manage catalogued waste items, categories, and disposal methods</p>
            </div>
            <a href="{{ route('admin.waste-items.create') }}" style="background:var(--primary);color:#fff;padding:.75rem 1.4rem;border-radius:var(--radius-full);font-weight:700;font-size:.9rem;display:inline-flex;align-items:center;gap:.5rem;box-shadow:var(--shadow-sm);">
                ✨ Add New Waste Item
            </a>
        </div>

        <!-- Filter Bar -->
        <form action="{{ route('admin.waste-items.index') }}" method="GET" style="margin-bottom:1.5rem;display:flex;gap:1rem;flex-wrap:wrap;">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="🔍 Search item by name, keyword..." style="padding:.6rem 1rem;border:1px solid var(--border);border-radius:var(--radius-md);font-size:.9rem;flex:1;min-width:220px;">
            <select name="category_id" style="padding:.6rem 1rem;border:1px solid var(--border);border-radius:var(--radius-md);font-size:.9rem;background:#fff;min-width:180px;">
                <option value="">All Categories</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                @endforeach
            </select>
            <button type="submit" style="background:var(--primary-dark);color:#fff;padding:.6rem 1.2rem;border:none;border-radius:var(--radius-md);font-weight:600;font-size:.9rem;cursor:pointer;">
                Filter
            </button>
            @if(request()->filled('search') || request()->filled('category_id'))
                <a href="{{ route('admin.waste-items.index') }}" style="background:#f1f5f9;color:var(--text-dark);padding:.6rem 1rem;border-radius:var(--radius-md);font-weight:600;font-size:.9rem;">
                    Clear
                </a>
            @endif
        </form>

        <!-- Table Card -->
        <div style="background:var(--bg-white);border:1px solid var(--border);border-radius:var(--radius-xl);padding:2rem;box-shadow:var(--shadow-md);">
            @if($items->isEmpty())
                <div style="text-align:center;padding:3rem 1rem;color:var(--text-muted);">
                    <div style="font-size:3rem;margin-bottom:.5rem;">📦</div>
                    <p style="font-size:1.1rem;font-weight:600;">No waste items found.</p>
                </div>
            @else
                <div style="overflow-x:auto;">
                    <table style="width:100%;border-collapse:collapse;text-align:left;font-size:.95rem;">
                        <thead>
                            <tr style="background:var(--bg-base);border-bottom:2px solid var(--border-light);color:var(--text-muted);font-size:.85rem;text-transform:uppercase;letter-spacing:.05em;">
                                <th style="padding:.9rem 1rem;">Item Name</th>
                                <th style="padding:.9rem 1rem;">Category</th>
                                <th style="padding:.9rem 1rem;">Disposal Method</th>
                                <th style="padding:.9rem 1rem;">Keywords</th>
                                <th style="padding:.9rem 1rem;text-align:right;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($items as $item)
                                <tr style="border-bottom:1px solid var(--border-light);">
                                    <td style="padding:1rem;font-weight:700;color:var(--text-dark);">{{ $item->name }}</td>
                                    <td style="padding:1rem;">
                                        @if($item->category)
                                            <span style="background:var(--primary-pale);color:var(--primary-dark);padding:.2rem .6rem;border-radius:var(--radius-full);font-size:.85rem;font-weight:600;">
                                                📦 {{ $item->category->name }}
                                            </span>
                                        @else
                                            <span style="color:var(--text-muted);">—</span>
                                        @endif
                                    </td>
                                    <td style="padding:1rem;color:var(--text-mid);">
                                        {{ $item->disposalMethod ? $item->disposalMethod->name : '—' }}
                                    </td>
                                    <td style="padding:1rem;color:var(--text-muted);font-size:.85rem;max-width:200px;">
                                        {{ $item->keywords ?? '—' }}
                                    </td>
                                    <td style="padding:1rem;text-align:right;white-space:nowrap;">
                                        <a href="{{ route('admin.waste-items.edit', $item) }}" style="background:#f1f5f9;color:var(--text-dark);padding:.4rem .8rem;border-radius:var(--radius-md);font-weight:600;font-size:.85rem;margin-right:.4rem;">
                                            ✏️ Edit
                                        </a>
                                        <form action="{{ route('admin.waste-items.destroy', $item) }}" method="POST" style="display:inline;" onsubmit="return confirm('Are you sure you want to delete this waste item?');">
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
