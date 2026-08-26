@extends('layouts.app')

@section('title', 'Search History – WasteWise Admin')

@section('content')
<section class="section" style="padding-block:2.5rem 5rem;">
    <div class="container">
        
        <!-- Header -->
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:2rem;flex-wrap:wrap;gap:1rem;">
            <div>
                <a href="{{ route('admin.dashboard') }}" style="color:var(--primary-dark);font-weight:600;font-size:.9rem;">← Back to Dashboard</a>
                <h1 style="font-family:'Outfit',sans-serif;font-size:2rem;font-weight:800;color:var(--text-dark);margin-top:.2rem;">
                    📊 User Search History
                </h1>
                <p style="color:var(--text-muted);font-size:.95rem;">Activity log of authenticated user waste item searches</p>
            </div>
        </div>

        <!-- Filter Bar -->
        <form action="{{ route('admin.search-history.index') }}" method="GET" style="margin-bottom:1.5rem;display:flex;gap:1rem;flex-wrap:wrap;">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="🔍 Search by user name, email, or item..." style="padding:.6rem 1rem;border:1px solid var(--border);border-radius:var(--radius-md);font-size:.9rem;flex:1;min-width:240px;">
            <button type="submit" style="background:var(--primary-dark);color:#fff;padding:.6rem 1.2rem;border:none;border-radius:var(--radius-md);font-weight:600;font-size:.9rem;cursor:pointer;">
                Filter
            </button>
            @if(request()->filled('search'))
                <a href="{{ route('admin.search-history.index') }}" style="background:#f1f5f9;color:var(--text-dark);padding:.6rem 1rem;border-radius:var(--radius-md);font-weight:600;font-size:.9rem;">
                    Clear
                </a>
            @endif
        </form>

        <!-- Table Card -->
        <div style="background:var(--bg-white);border:1px solid var(--border);border-radius:var(--radius-xl);padding:2rem;box-shadow:var(--shadow-md);">
            @if($histories->isEmpty())
                <div style="text-align:center;padding:3rem 1rem;color:var(--text-muted);">
                    <div style="font-size:3rem;margin-bottom:.5rem;">📊</div>
                    <p style="font-size:1.1rem;font-weight:600;">No search history records found.</p>
                </div>
            @else
                <div style="overflow-x:auto;">
                    <table style="width:100%;border-collapse:collapse;text-align:left;font-size:.95rem;">
                        <thead>
                            <tr style="background:var(--bg-base);border-bottom:2px solid var(--border-light);color:var(--text-muted);font-size:.85rem;text-transform:uppercase;letter-spacing:.05em;">
                                <th style="padding:.9rem 1rem;">ID</th>
                                <th style="padding:.9rem 1rem;">User</th>
                                <th style="padding:.9rem 1rem;">Searched Waste Item</th>
                                <th style="padding:.9rem 1rem;">Category</th>
                                <th style="padding:.9rem 1rem;">Searched At</th>
                                <th style="padding:.9rem 1rem;text-align:right;">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($histories as $history)
                                <tr style="border-bottom:1px solid var(--border-light);">
                                    <td style="padding:1rem;color:var(--text-light);font-weight:600;">#{{ $history->id }}</td>
                                    <td style="padding:1rem;">
                                        @if($history->user)
                                            <div style="font-weight:700;color:var(--text-dark);">{{ $history->user->name }}</div>
                                            <div style="font-size:.82rem;color:var(--text-muted);">{{ $history->user->email }}</div>
                                        @else
                                            <span style="color:var(--text-muted);font-style:italic;">Guest User</span>
                                        @endif
                                    </td>
                                    <td style="padding:1rem;font-weight:700;color:var(--text-dark);">
                                        {{ $history->wasteItem ? $history->wasteItem->name : 'N/A' }}
                                    </td>
                                    <td style="padding:1rem;">
                                        @if($history->wasteItem && $history->wasteItem->category)
                                            <span style="background:var(--primary-pale);color:var(--primary-dark);padding:.2rem .6rem;border-radius:var(--radius-full);font-size:.85rem;font-weight:600;">
                                                📦 {{ $history->wasteItem->category->name }}
                                            </span>
                                        @else
                                            <span style="color:var(--text-muted);">—</span>
                                        @endif
                                    </td>
                                    <td style="padding:1rem;color:var(--text-muted);font-size:.85rem;white-space:nowrap;">
                                        🕒 {{ \Carbon\Carbon::parse($history->searched_at)->format('M d, Y • h:i A') }}
                                    </td>
                                    <td style="padding:1rem;text-align:right;white-space:nowrap;">
                                        <form action="{{ route('admin.search-history.destroy', $history) }}" method="POST" style="display:inline;" onsubmit="return confirm('Are you sure you want to delete this search history record?');">
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
