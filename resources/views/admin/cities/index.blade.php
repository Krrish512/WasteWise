@extends('layouts.app')

@section('title', 'Manage Cities – WasteWise Admin')

@section('content')
<section class="section" style="padding-block:2.5rem 5rem;">
    <div class="container">
        
        <!-- Header -->
        <div style="display:flex;justify-space-between;align-items:center;margin-bottom:2rem;flex-wrap:wrap;gap:1rem;">
            <div>
                <a href="{{ route('admin.dashboard') }}" style="color:var(--primary-dark);font-weight:600;font-size:.9rem;">← Back to Dashboard</a>
                <h1 style="font-family:'Outfit',sans-serif;font-size:2rem;font-weight:800;color:var(--text-dark);margin-top:.2rem;">
                    🏙️ Cities
                </h1>
                <p style="color:var(--text-muted);font-size:.95rem;">Manage coverage cities for collection centres</p>
            </div>
            <a href="{{ route('admin.cities.create') }}" style="background:var(--primary);color:#fff;padding:.75rem 1.4rem;border-radius:var(--radius-full);font-weight:700;font-size:.9rem;display:inline-flex;align-items:center;gap:.5rem;box-shadow:var(--shadow-sm);">
                ✨ Add New City
            </a>
        </div>

        <!-- Table Card -->
        <div style="background:var(--bg-white);border:1px solid var(--border);border-radius:var(--radius-xl);padding:2rem;box-shadow:var(--shadow-md);">
            @if($cities->isEmpty())
                <div style="text-align:center;padding:3rem 1rem;color:var(--text-muted);">
                    <div style="font-size:3rem;margin-bottom:.5rem;">🏙️</div>
                    <p style="font-size:1.1rem;font-weight:600;">No cities found.</p>
                </div>
            @else
                <div style="overflow-x:auto;">
                    <table style="width:100%;border-collapse:collapse;text-align:left;font-size:.95rem;">
                        <thead>
                            <tr style="background:var(--bg-base);border-bottom:2px solid var(--border-light);color:var(--text-muted);font-size:.85rem;text-transform:uppercase;letter-spacing:.05em;">
                                <th style="padding:.9rem 1rem;">ID</th>
                                <th style="padding:.9rem 1rem;">City Name</th>
                                <th style="padding:.9rem 1rem;">State</th>
                                <th style="padding:.9rem 1rem;">Collection Centres</th>
                                <th style="padding:.9rem 1rem;text-align:right;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($cities as $city)
                                <tr style="border-bottom:1px solid var(--border-light);">
                                    <td style="padding:1rem;color:var(--text-light);font-weight:600;">#{{ $city->id }}</td>
                                    <td style="padding:1rem;font-weight:700;color:var(--text-dark);">{{ $city->name }}</td>
                                    <td style="padding:1rem;color:var(--text-mid);">{{ $city->state ?? '—' }}</td>
                                    <td style="padding:1rem;">
                                        <span style="background:#faf5ff;color:#7e22ce;padding:.2rem .6rem;border-radius:var(--radius-full);font-size:.85rem;font-weight:600;">
                                            {{ $city->collection_centres_count }} centres
                                        </span>
                                    </td>
                                    <td style="padding:1rem;text-align:right;white-space:nowrap;">
                                        <a href="{{ route('admin.cities.edit', $city) }}" style="background:#f1f5f9;color:var(--text-dark);padding:.4rem .8rem;border-radius:var(--radius-md);font-weight:600;font-size:.85rem;margin-right:.4rem;">
                                            ✏️ Edit
                                        </a>
                                        <form action="{{ route('admin.cities.destroy', $city) }}" method="POST" style="display:inline;" onsubmit="return confirm('Are you sure you want to delete this city?');">
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
