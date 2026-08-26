@extends('layouts.app')

@section('title', 'Manage Collection Centres – WasteWise Admin')

@section('content')
<section class="section" style="padding-block:2.5rem 5rem;">
    <div class="container">
        
        <!-- Header -->
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:2rem;flex-wrap:wrap;gap:1rem;">
            <div>
                <a href="{{ route('admin.dashboard') }}" style="color:var(--primary-dark);font-weight:600;font-size:.9rem;">← Back to Dashboard</a>
                <h1 style="font-family:'Outfit',sans-serif;font-size:2rem;font-weight:800;color:var(--text-dark);margin-top:.2rem;">
                    📍 Collection Centres
                </h1>
                <p style="color:var(--text-muted);font-size:.95rem;">Manage authorised waste collection centres and locations</p>
            </div>
            <a href="{{ route('admin.collection-centres.create') }}" style="background:var(--primary);color:#fff;padding:.75rem 1.4rem;border-radius:var(--radius-full);font-weight:700;font-size:.9rem;display:inline-flex;align-items:center;gap:.5rem;box-shadow:var(--shadow-sm);">
                ✨ Add Collection Centre
            </a>
        </div>

        <!-- Table Card -->
        <div style="background:var(--bg-white);border:1px solid var(--border);border-radius:var(--radius-xl);padding:2rem;box-shadow:var(--shadow-md);">
            @if($centres->isEmpty())
                <div style="text-align:center;padding:3rem 1rem;color:var(--text-muted);">
                    <div style="font-size:3rem;margin-bottom:.5rem;">📍</div>
                    <p style="font-size:1.1rem;font-weight:600;">No collection centres found.</p>
                </div>
            @else
                <div style="overflow-x:auto;">
                    <table style="width:100%;border-collapse:collapse;text-align:left;font-size:.95rem;">
                        <thead>
                            <tr style="background:var(--bg-base);border-bottom:2px solid var(--border-light);color:var(--text-muted);font-size:.85rem;text-transform:uppercase;letter-spacing:.05em;">
                                <th style="padding:.9rem 1rem;">Centre Name</th>
                                <th style="padding:.9rem 1rem;">City</th>
                                <th style="padding:.9rem 1rem;">Category</th>
                                <th style="padding:.9rem 1rem;">Address</th>
                                <th style="padding:.9rem 1rem;">Phone</th>
                                <th style="padding:.9rem 1rem;text-align:right;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($centres as $centre)
                                <tr style="border-bottom:1px solid var(--border-light);">
                                    <td style="padding:1rem;font-weight:700;color:var(--text-dark);">{{ $centre->name }}</td>
                                    <td style="padding:1rem;color:var(--text-mid);">{{ $centre->city ? $centre->city->name : '—' }}</td>
                                    <td style="padding:1rem;">
                                        @if($centre->category)
                                            <span style="background:var(--primary-pale);color:var(--primary-dark);padding:.2rem .6rem;border-radius:var(--radius-full);font-size:.85rem;font-weight:600;">
                                                📦 {{ $centre->category->name }}
                                            </span>
                                        @else
                                            <span style="color:var(--text-muted);">—</span>
                                        @endif
                                    </td>
                                    <td style="padding:1rem;color:var(--text-mid);max-width:250px;">{{ Str::limit($centre->address, 60) }}</td>
                                    <td style="padding:1rem;color:var(--text-muted);">{{ $centre->phone ?? '—' }}</td>
                                    <td style="padding:1rem;text-align:right;white-space:nowrap;">
                                        <a href="{{ route('admin.collection-centres.edit', $centre) }}" style="background:#f1f5f9;color:var(--text-dark);padding:.4rem .8rem;border-radius:var(--radius-md);font-weight:600;font-size:.85rem;margin-right:.4rem;">
                                            ✏️ Edit
                                        </a>
                                        <form action="{{ route('admin.collection-centres.destroy', $centre) }}" method="POST" style="display:inline;" onsubmit="return confirm('Are you sure you want to delete this collection centre?');">
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
