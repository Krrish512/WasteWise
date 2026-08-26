@extends('layouts.app')

@section('title', 'User Management – WasteWise Admin')

@section('content')
<section class="section" style="padding-block:2.5rem 5rem;">
    <div class="container">
        
        <!-- Header -->
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:2rem;flex-wrap:wrap;gap:1rem;">
            <div>
                <a href="{{ route('admin.dashboard') }}" style="color:var(--primary-dark);font-weight:600;font-size:.9rem;">← Back to Dashboard</a>
                <h1 style="font-family:'Outfit',sans-serif;font-size:2rem;font-weight:800;color:var(--text-dark);margin-top:.2rem;">
                    👥 User Management
                </h1>
                <p style="color:var(--text-muted);font-size:.95rem;">Manage registered users and administrator roles</p>
            </div>
        </div>

        <!-- Table Card -->
        <div style="background:var(--bg-white);border:1px solid var(--border);border-radius:var(--radius-xl);padding:2rem;box-shadow:var(--shadow-md);">
            @if($users->isEmpty())
                <div style="text-align:center;padding:3rem 1rem;color:var(--text-muted);">
                    <div style="font-size:3rem;margin-bottom:.5rem;">👥</div>
                    <p style="font-size:1.1rem;font-weight:600;">No registered users found.</p>
                </div>
            @else
                <div style="overflow-x:auto;">
                    <table style="width:100%;border-collapse:collapse;text-align:left;font-size:.95rem;">
                        <thead>
                            <tr style="background:var(--bg-base);border-bottom:2px solid var(--border-light);color:var(--text-muted);font-size:.85rem;text-transform:uppercase;letter-spacing:.05em;">
                                <th style="padding:.9rem 1rem;">ID</th>
                                <th style="padding:.9rem 1rem;">User Name</th>
                                <th style="padding:.9rem 1rem;">Email Address</th>
                                <th style="padding:.9rem 1rem;">Role</th>
                                <th style="padding:.9rem 1rem;">Registered On</th>
                                <th style="padding:.9rem 1rem;text-align:right;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($users as $user)
                                <tr style="border-bottom:1px solid var(--border-light);">
                                    <td style="padding:1rem;color:var(--text-light);font-weight:600;">#{{ $user->id }}</td>
                                    <td style="padding:1rem;font-weight:700;color:var(--text-dark);">
                                        {{ $user->name }}
                                        @if($user->id === auth()->id())
                                            <span style="background:#dcfce7;color:#166534;font-size:.75rem;padding:.15rem .5rem;border-radius:var(--radius-full);margin-left:.4rem;font-weight:700;">(You)</span>
                                        @endif
                                    </td>
                                    <td style="padding:1rem;color:var(--text-mid);">{{ $user->email }}</td>
                                    <td style="padding:1rem;">
                                        @if($user->isAdmin())
                                            <span style="background:#fef3c7;color:#92400e;padding:.25rem .7rem;border-radius:var(--radius-full);font-size:.85rem;font-weight:700;">
                                                🔒 Administrator
                                            </span>
                                        @else
                                            <span style="background:var(--bg-base);color:var(--text-muted);padding:.25rem .7rem;border-radius:var(--radius-full);font-size:.85rem;font-weight:600;">
                                                👤 Normal User
                                            </span>
                                        @endif
                                    </td>
                                    <td style="padding:1rem;color:var(--text-muted);font-size:.85rem;white-space:nowrap;">
                                        🕒 {{ $user->created_at ? $user->created_at->format('M d, Y') : '—' }}
                                    </td>
                                    <td style="padding:1rem;text-align:right;white-space:nowrap;">
                                        @if($user->id !== auth()->id())
                                            <form action="{{ route('admin.users.toggle-admin', $user) }}" method="POST" style="display:inline;" onsubmit="return confirm('Change administrator role for {{ $user->email }}?');">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" style="background:{{ $user->isAdmin() ? '#fff7ed' : '#ecfdf5' }};color:{{ $user->isAdmin() ? '#c2410c' : '#047857' }};border:1px solid {{ $user->isAdmin() ? '#ffedd5' : '#a7f3d0' }};padding:.4rem .8rem;border-radius:var(--radius-md);font-weight:600;font-size:.85rem;cursor:pointer;margin-right:.4rem;">
                                                    {{ $user->isAdmin() ? '⬇️ Revoke Admin' : '⭐ Make Admin' }}
                                                </button>
                                            </form>
                                            <form action="{{ route('admin.users.destroy', $user) }}" method="POST" style="display:inline;" onsubmit="return confirm('Are you sure you want to delete account {{ $user->email }}?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" style="background:#fef2f2;color:#dc2626;border:1px solid #fca5a5;padding:.4rem .8rem;border-radius:var(--radius-md);font-weight:600;font-size:.85rem;cursor:pointer;">
                                                    🗑️ Delete User
                                                </button>
                                            </form>
                                        @else
                                            <span style="color:var(--text-light);font-size:.85rem;font-style:italic;">Current Account</span>
                                        @endif
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
