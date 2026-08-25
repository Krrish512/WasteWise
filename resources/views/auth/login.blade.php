@extends('layouts.app')

@section('title', 'Login – WasteWise')

@section('content')
<section class="section" style="padding-block:3rem 5rem;">
    <div class="container" style="max-width:460px;">
        <div style="background:var(--bg-white);border:1px solid var(--border);border-radius:var(--radius-xl);padding:2.5rem;box-shadow:var(--shadow-lg);">
            
            <!-- Form Header -->
            <div style="text-align:center;margin-bottom:2rem;">
                <div style="width:56px;height:56px;background:var(--primary-pale);color:var(--primary-dark);border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:1.8rem;margin:0 auto 1rem;box-shadow:var(--shadow-sm);">
                    🔑
                </div>
                <h1 style="font-family:'Outfit',sans-serif;font-size:1.8rem;font-weight:800;color:var(--text-dark);margin-bottom:.4rem;">
                    Welcome Back
                </h1>
                <p style="color:var(--text-muted);font-size:.95rem;">
                    Log in to access your WasteWise search history & dashboard
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

            <!-- Login Form -->
            <form action="{{ route('login') }}" method="POST">
                @csrf

                <!-- Email -->
                <div style="margin-bottom:1.25rem;">
                    <label for="email" style="display:block;font-weight:600;font-size:.9rem;color:var(--text-dark);margin-bottom:.4rem;">
                        Email Address <span style="color:#dc2626;">*</span>
                    </label>
                    <input 
                        type="email" 
                        name="email" 
                        id="email" 
                        value="{{ old('email') }}" 
                        required 
                        placeholder="you@example.com"
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
                        placeholder="Enter your password"
                        style="width:100%;padding:.75rem 1rem;border:1.5px solid var(--border);border-radius:var(--radius-md);font-size:1rem;outline:none;transition:var(--transition);"
                        onfocus="this.style.borderColor='var(--primary)';"
                        onblur="this.style.borderColor='var(--border)';"
                    >
                </div>

                <!-- Remember Me Checkbox -->
                <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:1.75rem;font-size:.9rem;">
                    <label style="display:flex;align-items:center;gap:.5rem;cursor:pointer;color:var(--text-mid);">
                        <input type="checkbox" name="remember" style="accent-color:var(--primary);width:16px;height:16px;">
                        <span>Remember Me</span>
                    </label>
                </div>

                <!-- Submit Button -->
                <button 
                    type="submit" 
                    style="width:100%;background:var(--primary);color:#fff;border:none;padding:.85rem 1.5rem;border-radius:var(--radius-full);font-size:1rem;font-weight:700;cursor:pointer;box-shadow:var(--shadow-md);transition:var(--transition);"
                    onmouseover="this.style.background='var(--primary-dark)';"
                    onmouseout="this.style.background='var(--primary)';"
                >
                    Log In 🚀
                </button>
            </form>

            <!-- Link to Register -->
            <div style="text-align:center;margin-top:1.75rem;padding-top:1.25rem;border-top:1px solid var(--border-light);font-size:.95rem;color:var(--text-muted);">
                Don't have an account yet? 
                <a href="{{ route('register') }}" style="color:var(--primary-dark);font-weight:700;text-decoration:underline;">
                    Register Now
                </a>
            </div>

        </div>
    </div>
</section>
@endsection
