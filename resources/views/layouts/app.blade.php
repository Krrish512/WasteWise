<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="WasteWise – Smart Waste Disposal Assistant. Find the safest and most eco-friendly way to dispose of your waste items.">
    <title>@yield('title', 'WasteWise – Smart Waste Disposal Assistant')</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&family=Outfit:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">

    <!-- WasteWise Favicon -->
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon.png') }}">
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>

@include('partials.navbar')

<!-- Flash Alerts Container -->
<div class="container" style="margin-top:1.5rem;">
    @if(session('success'))
        <div style="background:#dcfce7;border:1px solid #86efac;color:#166534;padding:.9rem 1.25rem;border-radius:var(--radius-md);margin-bottom:1rem;display:flex;align-items:center;gap:.75rem;font-weight:500;box-shadow:var(--shadow-sm);">
            <span style="font-size:1.2rem">✅</span>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    @if(session('error'))
        <div style="background:#fef2f2;border:1px solid #fca5a5;color:#991b1b;padding:.9rem 1.25rem;border-radius:var(--radius-md);margin-bottom:1rem;display:flex;align-items:center;gap:.75rem;font-weight:500;box-shadow:var(--shadow-sm);">
            <span style="font-size:1.2rem">⚠️</span>
            <span>{{ session('error') }}</span>
        </div>
    @endif
</div>

<!-- Main Content -->
<main>
    @yield('content')
</main>

<!-- Footer -->
<footer style="background:#065f46;color:#e6f4ea;padding-block:3rem 2rem;margin-top:5rem;border-top:1px solid var(--border-light);">
    <div class="container" style="text-align:center;">
        <div style="display:flex;align-items:center;justify-content:center;gap:.6rem;margin-bottom:1rem;">
            <div style="font-size:1.4rem;">🌿</div>
            <span style="font-family:'Outfit',sans-serif;font-size:1.4rem;font-weight:800;color:#fff;">WasteWise</span>
        </div>
        <p style="color:#a7f3d0;max-width:500px;margin:0 auto 1.5rem;font-size:.95rem;">
            Empowering communities to make sustainable waste disposal decisions. Safe, smart, and eco-friendly.
        </p>
        <div style="border-top:1px solid rgba(255,255,255,.1);padding-top:1.5rem;font-size:.85rem;color:#6ee7b7;">
            &copy; {{ date('Y') }} WasteWise. Built with Laravel. All rights reserved.
        </div>
    </div>
</footer>

</body>
</html>
