@extends('layouts.app')

@section('title', 'Admin Login – WasteWise')

@section('content')
<section class="section" style="padding-block:3rem 5rem;">
    <div class="container" style="max-width:460px;">
        <div style="background:var(--bg-white);border:1px solid var(--border);border-radius:var(--radius-xl);padding:2.5rem;box-shadow:var(--shadow-lg);">
            
            <!-- Header -->
            <div style="text-align:center;margin-bottom:2rem;">
                <div style="width:56px;height:56px;background:var(--primary-pale);color:var(--primary-dark);border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:1.8rem;margin:0 auto 1rem;box-shadow:var(--shadow-sm);">
                    🔒
                </div>
                <h1 style="font-family:'Outfit',sans-serif;font-size:1.8rem;font-weight:800;color:var(--text-dark);margin-bottom:.4rem;">
                    Admin Portal
                </h1>
                <p style="color:var(--text-muted);font-size:.95rem;">
                    Authorized WasteWise personnel login
                </p>
            </div>

            <!-- Validation Errors Summary -->
            @if ($errors->any())
                <div style="background:#fef2f2;border:1px solid #fca5a5;color:#991b1b;padding:.9rem 1.25rem;border-radius:var(--radius-md);margin-bottom:1.5rem;font-size:.9rem;">
                    <strong style="display:block;margin-bottom:.3rem;font-weight:700;">Login Failed:</strong>
                    <ul style="margin:0;padding-left:1.2rem;list-style-type:disc;">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Admin Login Form -->
            <form action="{{ route('admin.login.submit') }}" method="POST">
                @csrf

                <!-- Email -->
                <div style="margin-bottom:1.25rem;">
                    <label for="email" style="display:block;font-weight:600;font-size:.9rem;color:var(--text-dark);margin-bottom:.4rem;">
                        Admin Email <span style="color:#dc2626;">*</span>
                    </label>
                    <input 
                        type="email" 
                        name="email" 
                        id="email" 
                        value="{{ old('email') }}" 
                        required 
                        placeholder="admin@wastewise.in"
                        style="width:100%;padding:.75rem 1rem;border:1.5px solid var(--border);border-radius:var(--radius-md);font-size:1rem;outline:none;transition:var(--transition);"
                        onfocus="this.style.borderColor='var(--primary)';"
                        onblur="this.style.borderColor='var(--border)';"
                    >
                </div>

                <!-- Password -->
                <div style="margin-bottom:1.5rem;">
                    <label for="password" style="display:block;font-weight:600;font-size:.9rem;color:var(--text-dark);margin-bottom:.4rem;">
                        Password <span style="color:#dc2626;">*</span>
                    </label>
                    <input 
                        type="password" 
                        name="password" 
                        id="password" 
                        required 
                        placeholder="••••••••"
                        style="width:100%;padding:.75rem 1rem;border:1.5px solid var(--border);border-radius:var(--radius-md);font-size:1rem;outline:none;transition:var(--transition);"
                        onfocus="this.style.borderColor='var(--primary)';"
                        onblur="this.style.borderColor='var(--border)';"
                    >
                </div>

                <!-- Submit Button -->
                <button 
                    type="submit" 
                    style="width:100%;background:linear-gradient(135deg,#065f46 0%,#16a34a 100%);color:#fff;border:none;padding:.85rem;border-radius:var(--radius-md);font-weight:700;font-size:1rem;cursor:pointer;box-shadow:var(--shadow-md);transition:var(--transition);"
                    onmouseover="this.style.opacity='.95';"
                    onmouseout="this.style.opacity='1';"
                >
                    🔒 Login as Admin
                </button>
            </form>

            <div style="text-align:center;margin-top:1.5rem;padding-top:1.25rem;border-top:1px solid var(--border-light);">
                <a href="{{ route('home') }}" style="color:var(--text-muted);font-size:.9rem;font-weight:500;text-decoration:none;">
                    ← Back to WasteWise Home
                </a>
            </div>

        </div>
    </div>
</section>
@endsection
