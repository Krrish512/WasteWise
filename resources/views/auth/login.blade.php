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
                    Log in to access your WasteWise search history &amp; dashboard
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
                    <label for="login-password" style="display:block;font-weight:600;font-size:.9rem;color:var(--text-dark);margin-bottom:.4rem;">
                        Password <span style="color:#dc2626;">*</span>
                    </label>
                    <div style="position:relative;">
                        <input 
                            type="password" 
                            name="password" 
                            id="login-password" 
                            required 
                            placeholder="Enter your password"
                            style="width:100%;padding:.75rem 2.85rem .75rem 1rem;border:1.5px solid var(--border);border-radius:var(--radius-md);font-size:1rem;outline:none;transition:var(--transition);"
                            onfocus="this.style.borderColor='var(--primary)';"
                            onblur="this.style.borderColor='var(--border)';"
                        >
                        <button 
                            type="button" 
                            id="toggle-login-password"
                            aria-label="Show password"
                            style="position:absolute;right:.65rem;top:50%;transform:translateY(-50%);background:transparent;border:none;padding:.35rem;color:#64748b;cursor:pointer;display:inline-flex;align-items:center;justify-content:center;border-radius:8px;z-index:10;"
                        >
                            <!-- Eye open icon -->
                            <svg class="eye-open" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" xmlns="http://www.w3.org/2000/svg">
                                <path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"></path>
                                <circle cx="12" cy="12" r="3"></circle>
                            </svg>
                            <!-- Eye closed icon (hidden by default) -->
                            <svg class="eye-closed" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" xmlns="http://www.w3.org/2000/svg" style="display:none;">
                                <path d="M9.88 9.88a3 3 0 1 0 4.24 4.24"></path>
                                <path d="M10.73 5.08A10.43 10.43 0 0 1 12 5c7 0 10 7 10 7a13.16 13.16 0 0 1-1.67 2.68"></path>
                                <path d="M6.61 6.61A13.52 13.52 0 0 0 2 12s3 7 10 7a9.74 9.74 0 0 0 5.39-1.61"></path>
                                <line x1="2" x2="22" y1="2" y2="22"></line>
                            </svg>
                        </button>
                    </div>
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

{{-- Self-contained password toggle – no dependency on app.js --}}
<script>
(function() {
    var btn = document.getElementById('toggle-login-password');
    var input = document.getElementById('login-password');
    if (!btn || !input) return;

    btn.addEventListener('click', function(e) {
        e.preventDefault();
        e.stopPropagation();
        if (input.type === 'password') {
            input.type = 'text';
            btn.setAttribute('aria-label', 'Hide password');
            btn.querySelector('.eye-open').style.display = 'none';
            btn.querySelector('.eye-closed').style.display = 'block';
        } else {
            input.type = 'password';
            btn.setAttribute('aria-label', 'Show password');
            btn.querySelector('.eye-open').style.display = 'block';
            btn.querySelector('.eye-closed').style.display = 'none';
        }
    });
})();
</script>
@endsection
