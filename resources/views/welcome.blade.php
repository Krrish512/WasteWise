<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="WasteWise – Smart Waste Disposal Assistant. Find the safest and most eco-friendly way to dispose of your waste items. Search for disposal methods and collection centres near you.">
    <meta name="keywords" content="waste disposal, e-waste, recycling, collection centres, eco-friendly, WasteWise">
    <meta name="author" content="WasteWise Team">
    <meta property="og:title" content="WasteWise – Smart Waste Disposal Assistant">
    <meta property="og:description" content="Discover the safest way to dispose of your waste. Real-time guidance powered by expert data.">
    <meta property="og:type" content="website">

    <title>WasteWise – Smart Waste Disposal Assistant</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&family=Outfit:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>

<!-- ═══════════════════════════════════════════════════════════ NAVBAR ═══ -->
<nav class="navbar" id="navbar">
    <div class="container">
        <div class="navbar-inner">
            <!-- Logo -->
            <div class="logo" data-page="home" role="button" tabindex="0" aria-label="WasteWise Home">
                <div class="logo-icon">🌿</div>
                <span class="logo-text">WasteWise</span>
            </div>

            <!-- Desktop Nav Links -->
            <div class="nav-links">
                <a class="nav-link active" data-page="home"    href="#" id="nav-home">🏠 Home</a>
                <a class="nav-link"         data-page="how"     href="#" id="nav-how">⚙️ How It Works</a>
                <a class="nav-link"         data-page="centres" href="#" id="nav-centres">📍 Collection Centres</a>
                <a class="nav-link"         data-page="about"   href="#" id="nav-about">ℹ️ About</a>

                @guest
                    <a class="nav-link" href="{{ route('login') }}">🔑 Login</a>
                    <a class="nav-link" href="{{ route('register') }}" style="background:var(--primary);color:#fff;padding:.4rem .9rem;border-radius:var(--radius-full);font-weight:600;">✨ Register</a>
                @endguest

                @auth
                    <a class="nav-link" href="{{ route('dashboard') }}">👤 Dashboard</a>
                    <form action="{{ route('logout') }}" method="POST" style="display:inline;">
                        @csrf
                        <button type="submit" class="nav-link" style="background:none;border:none;cursor:pointer;color:#dc2626;font-weight:600;">🚪 Logout</button>
                    </form>
                @endauth

                <button class="nav-admin-btn" data-page="adminLogin">🔒 Admin</button>
            </div>

            <!-- Hamburger -->
            <button class="hamburger" id="hamburger" aria-label="Open mobile menu">
                <span></span>
                <span></span>
                <span></span>
            </button>
        </div>
    </div>
</nav>

<!-- Mobile Nav Overlay -->
<div class="mobile-nav" id="mobile-nav" role="dialog" aria-label="Mobile navigation">
    <div class="mobile-nav-drawer">
        <button class="mobile-nav-close" id="mobile-nav-close" aria-label="Close menu">✕</button>
        <div style="display:flex;align-items:center;gap:.5rem;margin-bottom:.5rem">
            <div class="logo-icon" style="width:32px;height:32px;font-size:.9rem">🌿</div>
            <span class="logo-text" style="font-size:1.1rem">WasteWise</span>
        </div>
        <div class="mobile-nav-links">
            <a class="mobile-nav-link" data-page="home"       href="#">🏠 Home</a>
            <a class="mobile-nav-link" data-page="how"        href="#">⚙️ How It Works</a>
            <a class="mobile-nav-link" data-page="centres"    href="#">📍 Collection Centres</a>
            <a class="mobile-nav-link" data-page="about"      href="#">ℹ️ About</a>

            @guest
                <a class="mobile-nav-link" href="{{ route('login') }}">🔑 Login</a>
                <a class="mobile-nav-link" href="{{ route('register') }}">✨ Register</a>
            @endguest

            @auth
                <a class="mobile-nav-link" href="{{ route('dashboard') }}">👤 Dashboard</a>
                <form action="{{ route('logout') }}" method="POST" style="margin-top:.5rem;">
                    @csrf
                    <button type="submit" class="mobile-nav-link" style="width:100%;text-align:left;background:none;border:none;color:#dc2626;font-weight:600;">🚪 Logout</button>
                </form>
            @endauth

            <a class="mobile-nav-link" data-page="adminLogin" href="#">🔒 Admin Login</a>
        </div>
    </div>
</div>

<!-- Flash Alerts Container -->
@if(session('success') || session('error'))
<div class="container" style="margin-top:1.5rem;margin-bottom:0;">
    @if(session('success'))
        <div style="background:#dcfce7;border:1px solid #86efac;color:#166534;padding:.9rem 1.25rem;border-radius:var(--radius-md);display:flex;align-items:center;gap:.75rem;font-weight:500;box-shadow:var(--shadow-sm);">
            <span style="font-size:1.2rem">✅</span>
            <span>{{ session('success') }}</span>
        </div>
    @endif
    @if(session('error'))
        <div style="background:#fef2f2;border:1px solid #fca5a5;color:#991b1b;padding:.9rem 1.25rem;border-radius:var(--radius-md);display:flex;align-items:center;gap:.75rem;font-weight:500;box-shadow:var(--shadow-sm);">
            <span style="font-size:1.2rem">⚠️</span>
            <span>{{ session('error') }}</span>
        </div>
    @endif
</div>
@endif

<!-- ═══════════════════════════════════════════════════════════════════════════
     PAGE 1 – HOME
════════════════════════════════════════════════════════════════════════════ -->
<div class="page active" id="page-home">

    <!-- Hero -->
    <section class="hero">
        <!-- Animated blobs -->
        <div class="hero-blob hero-blob-1"></div>
        <div class="hero-blob hero-blob-2"></div>
        <div class="hero-blob hero-blob-3"></div>

        <div class="container">
            <div class="hero-inner">
                <div class="hero-badge">
                    🌍 <span>Smart Waste Disposal Assistant</span>
                </div>

                <h1 class="hero-title">
                    Dispose Waste <span class="gradient-text">Responsibly</span><br>
                    <span class="gradient-text">Save Our Planet</span>
                </h1>

                <p class="hero-subtitle">
                    WasteWise helps you find the safest, most eco-friendly way to dispose of any waste item — from old electronics to plastic bottles — with real collection centre locations near you.
                </p>

                <!-- Search Box -->
                <div class="search-container">
                    <div class="search-box">
                        <div class="search-icon">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
                            </svg>
                        </div>
                        <input
                            id="hero-search-input"
                            class="search-input"
                            type="text"
                            placeholder="Search waste item e.g. Old Laptop..."
                            autocomplete="off"
                            aria-label="Search waste item"
                            maxlength="100"
                        >
                        <button class="search-clear" id="hero-search-clear" aria-label="Clear search">✕</button>
                        <button class="search-btn pulse-ring" id="hero-search-btn" aria-label="Find disposal solution">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
                            </svg>
                            <span>Find Solution</span>
                        </button>
                    </div>
                    <div class="search-validation" id="hero-validation"></div>
                </div>

                <!-- Example Chips -->
                <div>
                    <p class="chips-label">💡 Try these examples:</p>
                    <div class="chips">
                        <button class="chip" data-chip="Old Laptop">💻 Old Laptop</button>
                        <button class="chip" data-chip="Old Mobile">📱 Old Mobile</button>
                        <button class="chip" data-chip="Plastic Bottle">🧴 Plastic Bottle</button>
                        <button class="chip" data-chip="Newspaper">📰 Newspaper</button>
                        <button class="chip" data-chip="Glass Bottle">🫙 Glass Bottle</button>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Stats Bar -->
    <div style="background:white;border-bottom:1px solid var(--border-light);padding:1.5rem 0">
        <div class="container">
            <div style="display:flex;justify-content:center;gap:3rem;flex-wrap:wrap">
                <div style="text-align:center">
                    <div style="font-family:'Outfit',sans-serif;font-size:1.8rem;font-weight:900;background:linear-gradient(135deg,var(--primary),var(--accent-teal));-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text">50+</div>
                    <div style="font-size:.8rem;color:var(--text-muted);font-weight:500">Waste Items</div>
                </div>
                <div style="text-align:center">
                    <div style="font-family:'Outfit',sans-serif;font-size:1.8rem;font-weight:900;background:linear-gradient(135deg,var(--primary),var(--accent-teal));-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text">12+</div>
                    <div style="font-size:.8rem;color:var(--text-muted);font-weight:500">Collection Centres</div>
                </div>
                <div style="text-align:center">
                    <div style="font-family:'Outfit',sans-serif;font-size:1.8rem;font-weight:900;background:linear-gradient(135deg,var(--primary),var(--accent-teal));-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text">8+</div>
                    <div style="font-size:.8rem;color:var(--text-muted);font-weight:500">Categories</div>
                </div>
                <div style="text-align:center">
                    <div style="font-family:'Outfit',sans-serif;font-size:1.8rem;font-weight:900;background:linear-gradient(135deg,var(--primary),var(--accent-teal));-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text">100%</div>
                    <div style="font-size:.8rem;color:var(--text-muted);font-weight:500">Free to Use</div>
                </div>
            </div>
        </div>
    </div>

    <!-- How It Works Preview -->
    <section class="how-it-works section">
        <div class="container">
            <div class="section-header">
                <div class="section-badge">⚙️ Process</div>
                <h2 class="section-title">How WasteWise Works</h2>
                <p class="section-subtitle">Three simple steps to responsible waste disposal — search, learn, and act.</p>
            </div>

            <div class="steps-grid">
                <div class="step-card reveal">
                    <div class="step-icon">🔍</div>
                    <div class="step-number">1</div>
                    <div class="step-title">Search Your Waste</div>
                    <p class="step-desc">Type in the name of your waste item — old laptop, plastic bottle, newspaper, or any other item you need to dispose of.</p>
                </div>
                <div class="step-card reveal">
                    <div class="step-icon">📋</div>
                    <div class="step-number">2</div>
                    <div class="step-title">Get Disposal Guidance</div>
                    <p class="step-desc">Receive category information, safe disposal methods, step-by-step instructions, and what to avoid doing.</p>
                </div>
                <div class="step-card reveal">
                    <div class="step-icon">📍</div>
                    <div class="step-number">3</div>
                    <div class="step-title">Find a Centre Near You</div>
                    <p class="step-desc">See collection centres in your city with address, phone number, and a direct "Get Directions" link to navigate there.</p>
                </div>
                <div class="step-card reveal">
                    <div class="step-icon">🌱</div>
                    <div class="step-number">4</div>
                    <div class="step-title">Make a Difference</div>
                    <p class="step-desc">Every item you dispose of correctly helps protect the environment, reduces pollution, and contributes to a cleaner India.</p>
                </div>
            </div>

            <div class="text-center" style="margin-top:2.5rem">
                <button class="btn btn-primary" data-page="how">Learn More ➜</button>
            </div>
        </div>
    </section>

    <!-- Waste Categories -->
    <section class="section" style="background:var(--bg-base)">
        <div class="container">
            <div class="section-header">
                <div class="section-badge">🗂️ Categories</div>
                <h2 class="section-title">Waste Categories We Cover</h2>
                <p class="section-subtitle">Click any category to explore disposal methods for that type of waste.</p>
            </div>

            <div class="categories-grid">
                <div class="category-card reveal" style="background:linear-gradient(135deg,#1a6b3c,#0f9b4e)" onclick="searchCategory('Old Laptop')">
                    <div class="category-emoji">💻</div>
                    <div class="category-name">Electronic Waste</div>
                    <div class="category-desc">Old laptops, mobiles, TVs, and other electronics</div>
                </div>
                <div class="category-card reveal" style="background:linear-gradient(135deg,#0369a1,#0ea5e9)" onclick="searchCategory('Plastic Bottle')">
                    <div class="category-emoji">♻️</div>
                    <div class="category-name">Plastic Waste</div>
                    <div class="category-desc">Bottles, bags, containers and packaging</div>
                </div>
                <div class="category-card reveal" style="background:linear-gradient(135deg,#7c3aed,#a78bfa)" onclick="searchCategory('Newspaper')">
                    <div class="category-emoji">📰</div>
                    <div class="category-name">Paper Waste</div>
                    <div class="category-desc">Newspapers, cardboard, office paper</div>
                </div>
                <div class="category-card reveal" style="background:linear-gradient(135deg,#b45309,#f59e0b)" onclick="searchCategory('Glass Bottle')">
                    <div class="category-emoji">🫙</div>
                    <div class="category-name">Glass Waste</div>
                    <div class="category-desc">Bottles, jars, mirrors and glassware</div>
                </div>
                <div class="category-card reveal" style="background:linear-gradient(135deg,#166534,#22c55e)" onclick="searchCategory('Vegetable Waste')">
                    <div class="category-emoji">🌱</div>
                    <div class="category-name">Organic Waste</div>
                    <div class="category-desc">Kitchen scraps, garden waste, food items</div>
                </div>
                <div class="category-card reveal" style="background:linear-gradient(135deg,#374151,#6b7280)" onclick="searchCategory('Old Iron')">
                    <div class="category-emoji">⚙️</div>
                    <div class="category-name">Metal Waste</div>
                    <div class="category-desc">Scrap metal, cans, appliances</div>
                </div>
                <div class="category-card reveal" style="background:linear-gradient(135deg,#991b1b,#ef4444)" onclick="searchCategory('Old Paint')">
                    <div class="category-emoji">⚠️</div>
                    <div class="category-name">Hazardous Waste</div>
                    <div class="category-desc">Chemicals, batteries, paint, solvents</div>
                </div>
                <div class="category-card reveal" style="background:linear-gradient(135deg,#c2410c,#fb923c)" onclick="searchCategory('Old Clothes')">
                    <div class="category-emoji">👕</div>
                    <div class="category-name">Textile Waste</div>
                    <div class="category-desc">Old clothes, fabrics and textiles</div>
                </div>
            </div>
        </div>
    </section>

    <!-- Why WasteWise -->
    <section class="why-section section">
        <div class="container">
            <div class="section-header">
                <div class="section-badge">✨ Why Us</div>
                <h2 class="section-title">Why Choose WasteWise?</h2>
                <p class="section-subtitle">We make responsible waste disposal simple, accessible, and actionable for everyone.</p>
            </div>

            <div class="why-grid">
                <div class="why-card reveal">
                    <div class="why-icon-wrap"><span class="why-emoji">⚡</span></div>
                    <div class="why-title">Instant Results</div>
                    <p class="why-desc">Get disposal guidance in seconds. Our search connects directly to a verified waste database for accurate, up-to-date information.</p>
                </div>
                <div class="why-card reveal">
                    <div class="why-icon-wrap"><span class="why-emoji">📍</span></div>
                    <div class="why-title">Local Collection Centres</div>
                    <p class="why-desc">Find nearby collection centres with addresses, phone numbers, and Google Maps directions — no more guessing where to go.</p>
                </div>
                <div class="why-card reveal">
                    <div class="why-icon-wrap"><span class="why-emoji">🛡️</span></div>
                    <div class="why-title">Safe Disposal Methods</div>
                    <p class="why-desc">Learn step-by-step instructions and safety warnings for each waste item so you and your family stay safe during disposal.</p>
                </div>
                <div class="why-card reveal">
                    <div class="why-icon-wrap"><span class="why-emoji">🌍</span></div>
                    <div class="why-title">Environmental Impact</div>
                    <p class="why-desc">Every correctly disposed item reduces landfill waste, prevents soil and water contamination, and helps protect biodiversity.</p>
                </div>
                <div class="why-card reveal">
                    <div class="why-icon-wrap"><span class="why-emoji">📱</span></div>
                    <div class="why-title">Mobile Friendly</div>
                    <p class="why-desc">WasteWise is fully responsive — use it from any device, anywhere, anytime. No app download required.</p>
                </div>
                <div class="why-card reveal">
                    <div class="why-icon-wrap"><span class="why-emoji">🆓</span></div>
                    <div class="why-title">Completely Free</div>
                    <p class="why-desc">WasteWise is a public service. No account needed, no charges, no ads — just straightforward waste disposal guidance.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="cta-section">
        <div class="container">
            <div class="cta-inner">
                <h2 class="cta-title">Ready to Dispose Responsibly?</h2>
                <p class="cta-desc">Join thousands of responsible citizens using WasteWise to keep our environment clean and green.</p>
                <button class="cta-btn" onclick="document.getElementById('hero-search-input').scrollIntoView({behavior:'smooth'});document.getElementById('hero-search-input').focus()" data-page="home">
                    🔍 Start Searching Now
                </button>
                <div style="margin-top:1.5rem">
                    <button class="btn btn-ghost" data-page="centres" style="display:inline-flex">
                        📍 Find Collection Centres
                    </button>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="footer-grid">
                <div class="footer-brand">
                    <div class="logo-text-footer">🌿 WasteWise</div>
                    <p>Smart waste disposal guidance for a cleaner, greener India. Find the right way to dispose of any waste item.</p>
                </div>
                <div>
                    <div class="footer-col-title">Navigate</div>
                    <a class="footer-link" data-page="home">Home</a>
                    <a class="footer-link" data-page="how">How It Works</a>
                    <a class="footer-link" data-page="centres">Collection Centres</a>
                    <a class="footer-link" data-page="about">About</a>
                </div>
                <div>
                    <div class="footer-col-title">Quick Search</div>
                    <span class="footer-link" data-chip="Old Laptop" onclick="triggerSearch('Old Laptop')">Old Laptop</span>
                    <span class="footer-link" data-chip="Old Mobile" onclick="triggerSearch('Old Mobile')">Old Mobile</span>
                    <span class="footer-link" data-chip="Plastic Bottle" onclick="triggerSearch('Plastic Bottle')">Plastic Bottle</span>
                    <span class="footer-link" data-chip="Newspaper" onclick="triggerSearch('Newspaper')">Newspaper</span>
                    <span class="footer-link" data-chip="Glass Bottle" onclick="triggerSearch('Glass Bottle')">Glass Bottle</span>
                </div>
                <div>
                    <div class="footer-col-title">Services</div>
                    <a class="footer-link" data-page="home">Disposal Guidance</a>
                    <a class="footer-link" data-page="centres">Collection Centres</a>
                    <a class="footer-link" data-page="how">Recycling & E-Waste</a>
                    <a class="footer-link" data-page="adminLogin">Admin Portal</a>
                </div>
            </div>
            <div class="footer-bottom">
                <span>© 2026 WasteWise. Built with ❤️ for a greener planet.</span>
                <div class="footer-bottom-right">
                    <span>Smart Waste Management</span>
                </div>
            </div>
        </div>
    </footer>

</div>

<!-- ═══════════════════════════════════════════════════════════════════════════
     PAGE 2 – SEARCH RESULTS
════════════════════════════════════════════════════════════════════════════ -->
<div class="page" id="page-results">

    <!-- Results Hero / Search Bar -->
    <div class="results-hero">
        <div class="container">
            <div class="results-search-wrap">
                <p class="results-label">🔍 Searching for: <strong id="result-query-label"></strong></p>
                <div class="search-box">
                    <div class="search-icon">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
                        </svg>
                    </div>
                    <input
                        id="res-search-input"
                        class="search-input"
                        type="text"
                        placeholder="Search another waste item..."
                        autocomplete="off"
                        aria-label="Search waste item"
                        maxlength="100"
                    >
                    <button class="search-clear" id="res-search-clear" aria-label="Clear search">✕</button>
                    <button class="search-btn" id="res-search-btn" aria-label="Search">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
                        </svg>
                        <span>Search</span>
                    </button>
                </div>
                <!-- Chips row -->
                <div class="chips" style="margin-top:1rem">
                    <button class="chip" data-chip="Old Laptop">💻 Old Laptop</button>
                    <button class="chip" data-chip="Old Mobile">📱 Old Mobile</button>
                    <button class="chip" data-chip="Plastic Bottle">🧴 Plastic Bottle</button>
                    <button class="chip" data-chip="Newspaper">📰 Newspaper</button>
                    <button class="chip" data-chip="Glass Bottle">🫙 Glass Bottle</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Results Area -->
    <div class="results-area">
        <div class="container">

            <!-- ── LOADING STATE ── -->
            <div id="result-loading" style="display:none">
                <div class="loading-wrap">
                    <div class="spinner"></div>
                    <p class="loading-text">🔍 Searching our waste database...</p>
                    <!-- Skeleton -->
                    <div class="skeleton-wrap" style="width:100%;max-width:860px">
                        <div class="skeleton-block" style="height:140px"></div>
                        <div class="skeleton-block" style="height:80px;width:70%"></div>
                        <div class="skeleton-block" style="height:60px;width:85%"></div>
                        <div class="skeleton-block" style="height:100px"></div>
                        <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(280px,1fr));gap:1rem">
                            <div class="skeleton-block" style="height:160px"></div>
                            <div class="skeleton-block" style="height:160px"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ── NOT FOUND STATE ── -->
            <div id="result-not-found" style="display:none">
                <div class="not-found-wrap">
                    <div class="not-found-icon">🔍</div>
                    <h2 class="not-found-title">Item Not Found</h2>
                    <p class="not-found-desc" id="not-found-msg">
                        Sorry, we couldn't find this waste item. Try another name such as Old Laptop, Old Mobile, Plastic Bottle, or Newspaper.
                    </p>
                    <div class="chips" style="justify-content:center;margin-bottom:1.5rem">
                        <button class="chip" data-chip="Old Laptop">💻 Old Laptop</button>
                        <button class="chip" data-chip="Old Mobile">📱 Old Mobile</button>
                        <button class="chip" data-chip="Plastic Bottle">🧴 Plastic Bottle</button>
                        <button class="chip" data-chip="Newspaper">📰 Newspaper</button>
                    </div>
                    <button class="btn btn-primary" data-page="home">← Back to Home</button>
                </div>
            </div>

            <!-- ── ERROR STATE ── -->
            <div id="result-error" style="display:none">
                <div class="error-wrap">
                    <div class="error-icon">⚠️</div>
                    <h2 class="error-title">Backend Unavailable</h2>
                    <p class="error-desc">
                        We couldn't connect to the WasteWise server. Please make sure the Laravel backend is running on <code style="background:#f1f5f9;padding:.15rem .4rem;border-radius:4px;font-size:.85rem">localhost:8000</code> and try again.
                    </p>
                    <button class="btn btn-primary" onclick="triggerSearch(document.getElementById('res-search-input').value)">
                        🔄 Retry
                    </button>
                </div>
            </div>

            <!-- ── SUCCESS STATE ── -->
            <div id="result-success" style="display:none">
                <div class="result-card">
                    <!-- Card Header -->
                    <div class="result-card-header">
                        <div class="result-category-badge">
                            <span id="res-category-emoji">🗑️</span>
                            <span id="res-category-badge">Category</span>
                        </div>
                        <h1 class="result-item-name" id="res-item-name">–</h1>
                        <p class="result-item-desc" id="res-item-desc">–</p>
                    </div>

                    <!-- Card Body -->
                    <div class="result-card-body">

                        <!-- Category & Method -->
                        <div class="result-section">
                            <h2 class="result-section-title">
                                <span class="icon">🏷️</span> Category &amp; Disposal Method
                            </h2>
                            <div style="display:flex;flex-wrap:wrap;gap:.75rem;margin-bottom:1rem">
                                <div class="tag tag-green">
                                    📂 Category: <strong id="res-category-label">–</strong>
                                </div>
                            </div>
                            <div class="disposal-method-badge">
                                ♻️ Safe Disposal: <span id="res-disposal-method">–</span>
                            </div>
                        </div>

                        <div class="divider"></div>

                        <!-- Instructions -->
                        <div class="result-section">
                            <h2 class="result-section-title">
                                <span class="icon">📋</span> Disposal Instructions
                            </h2>
                            <div class="info-box" id="res-instructions">–</div>
                        </div>

                        <div class="divider"></div>

                        <!-- Avoid -->
                        <div class="result-section">
                            <h2 class="result-section-title">
                                <span class="icon">🚫</span> What to Avoid
                            </h2>
                            <div class="info-box warning" id="res-avoid">–</div>
                        </div>

                        <div class="divider"></div>

                        <!-- Collection Centres -->
                        <div class="result-section">
                            <h2 class="result-section-title">
                                <span class="icon">📍</span> Nearby Collection Centres
                            </h2>
                            <div class="centres-grid" id="res-centres-list"></div>
                            <div class="no-centres" id="res-no-centres" style="display:none">
                                📭 No specific collection centres found for this category. Please contact your local municipal corporation for disposal guidance.
                            </div>
                        </div>

                    </div>
                </div>

                <!-- Action buttons -->
                <div style="display:flex;gap:1rem;justify-content:center;margin-top:2rem;flex-wrap:wrap">
                    <button class="btn btn-outline" data-page="home">← Search Again</button>
                    <button class="btn btn-primary" data-page="centres">📍 All Collection Centres</button>
                </div>
            </div>

        </div>
    </div>

</div>

<!-- ═══════════════════════════════════════════════════════════════════════════
     PAGE 3 – COLLECTION CENTRES
════════════════════════════════════════════════════════════════════════════ -->
<div class="page" id="page-centres">

    <div class="centres-page-hero">
        <div class="container">
            <div class="section-badge" style="justify-content:center">📍 Directory</div>
            <h1 class="section-title" style="margin-top:.75rem">Waste Collection Centres</h1>
            <p class="section-subtitle">Find authorised collection centres across India. Filter by city or category to locate one near you.</p>
        </div>
    </div>

    <section class="section" style="padding-top:2.5rem">
        <div class="container">
            <!-- Filters -->
            <div class="filter-row">
                <input
                    id="centres-filter"
                    class="filter-input"
                    type="text"
                    placeholder="🔍 Search by name, city, or address..."
                    aria-label="Filter collection centres"
                >
                <select id="centres-cat-filter" class="filter-input" style="max-width:220px" aria-label="Filter by category">
                    <option value="">All Categories</option>
                    <option value="Electronic Waste">💻 Electronic Waste</option>
                    <option value="Plastic Waste">♻️ Plastic Waste</option>
                    <option value="Paper Waste">📰 Paper Waste</option>
                    <option value="Glass Waste">🫙 Glass Waste</option>
                    <option value="Organic Waste">🌱 Organic Waste</option>
                    <option value="Metal Waste">⚙️ Metal Waste</option>
                    <option value="Hazardous Waste">⚠️ Hazardous Waste</option>
                    <option value="Textile Waste">👕 Textile Waste</option>
                </select>
            </div>

            <!-- Grid -->
            <div class="centres-list" id="centres-list-grid">
                <!-- Populated by JS -->
            </div>
        </div>
    </section>

    <!-- CTA -->
    <div style="background:var(--primary-pale);border-top:1px solid var(--border);padding:2.5rem 0;text-align:center">
        <div class="container">
            <p style="font-size:1rem;color:var(--text-mid);margin-bottom:1rem">
                🔍 Can't find a centre for your specific waste item? Search for it directly!
            </p>
            <button class="btn btn-primary" data-page="home">Search Waste Item →</button>
        </div>
    </div>

</div>

<!-- ═══════════════════════════════════════════════════════════════════════════
     PAGE 4 – HOW IT WORKS
════════════════════════════════════════════════════════════════════════════ -->
<div class="page" id="page-how">

    <div class="about-hero">
        <div class="container">
            <div class="section-badge" style="justify-content:center">⚙️ Process</div>
            <h1 class="section-title" style="margin-top:.75rem">How WasteWise Works</h1>
            <p class="section-subtitle">A simple, transparent process to help you dispose of waste the right way — every time.</p>
        </div>
    </div>

    <section class="how-it-works section">
        <div class="container">

            <!-- Detailed Steps -->
            <div class="steps-grid" style="margin-bottom:4rem">
                <div class="step-card reveal">
                    <div class="step-icon">🔍</div>
                    <div class="step-number">1</div>
                    <div class="step-title">Enter Your Waste Item</div>
                    <p class="step-desc">Type the name of your waste item in the search bar. You can search for exact names (e.g. "Old Laptop") or partial names (e.g. "laptop"). The system will find the best match.</p>
                </div>
                <div class="step-card reveal">
                    <div class="step-icon">🗄️</div>
                    <div class="step-number">2</div>
                    <div class="step-title">Smart Database Search</div>
                    <p class="step-desc">Our intelligent system scans verified records for matching waste categories, safe disposal practices, and localized collection centre details.</p>
                </div>
                <div class="step-card reveal">
                    <div class="step-icon">📋</div>
                    <div class="step-number">3</div>
                    <div class="step-title">View Disposal Guidance</div>
                    <p class="step-desc">You see the waste category, safe disposal method, step-by-step instructions, and important warnings about what to avoid — all from our verified database.</p>
                </div>
                <div class="step-card reveal">
                    <div class="step-icon">📍</div>
                    <div class="step-number">4</div>
                    <div class="step-title">Locate a Collection Centre</div>
                    <p class="step-desc">Collection centres that accept your waste category are listed with their name, city, address, phone number, and GPS coordinates for navigation.</p>
                </div>
                <div class="step-card reveal">
                    <div class="step-icon">🧭</div>
                    <div class="step-number">5</div>
                    <div class="step-title">Get Directions</div>
                    <p class="step-desc">Click "Get Directions" to open Google Maps with turn-by-turn navigation directly to the collection centre — no more guessing where to go.</p>
                </div>
                <div class="step-card reveal">
                    <div class="step-icon">🌱</div>
                    <div class="step-number">6</div>
                    <div class="step-title">Contribute to a Cleaner Planet</div>
                    <p class="step-desc">By disposing correctly, you prevent toxic chemicals from entering our soil, water, and air — protecting ecosystems and future generations.</p>
                </div>
            </div>

        </div>
    </section>

    <!-- CTA -->
    <section class="cta-section">
        <div class="container">
            <div class="cta-inner">
                <h2 class="cta-title">Try It Now!</h2>
                <p class="cta-desc">See the complete process in action — search for an old laptop or plastic bottle.</p>
                <button class="cta-btn" data-page="home">🔍 Start Searching</button>
            </div>
        </div>
    </section>

</div>

<!-- ═══════════════════════════════════════════════════════════════════════════
     PAGE 5 – ABOUT
════════════════════════════════════════════════════════════════════════════ -->
<div class="page" id="page-about">

    <div class="about-hero">
        <div class="container">
            <div class="section-badge" style="justify-content:center">ℹ️ About Us</div>
            <h1 class="section-title" style="margin-top:.75rem">About WasteWise</h1>
            <p class="section-subtitle">Empowering citizens with accessible, reliable waste disposal guidance for a cleaner and greener India.</p>
        </div>
    </div>

    <section class="section">
        <div class="container">

            <!-- Mission -->
            <div style="max-width:800px;margin:0 auto 4rem">
                <div class="why-card" style="padding:2.5rem;text-align:center">
                    <div style="font-size:3rem;margin-bottom:1.25rem">🌍</div>
                    <h2 class="section-title" style="margin-bottom:1rem">Our Mission</h2>
                    <p style="font-size:1.05rem;color:var(--text-muted);line-height:1.75">
                        WasteWise was created to solve a simple but critical problem: most people don't know the correct way to dispose of their waste. Improper disposal leads to soil contamination, water pollution, and long-term environmental damage. Our mission is to make proper waste disposal <strong>accessible, simple, and actionable</strong> for every citizen.
                    </p>
                </div>
            </div>

            <!-- Stats -->
            <div class="section-header">
                <div class="section-badge">📊 Impact</div>
                <h2 class="section-title">Our Impact</h2>
            </div>

            <div class="stat-grid">
                <div class="stat-card reveal">
                    <div class="stat-value">50+</div>
                    <div class="stat-label">Waste Items Catalogued</div>
                </div>
                <div class="stat-card reveal">
                    <div class="stat-value">8+</div>
                    <div class="stat-label">Waste Categories</div>
                </div>
                <div class="stat-card reveal">
                    <div class="stat-value">12+</div>
                    <div class="stat-label">Collection Centres</div>
                </div>
                <div class="stat-card reveal">
                    <div class="stat-value">10+</div>
                    <div class="stat-label">Cities Covered</div>
                </div>
                <div class="stat-card reveal">
                    <div class="stat-value">100%</div>
                    <div class="stat-label">Free to Use</div>
                </div>
                <div class="stat-card reveal">
                    <div class="stat-value">24/7</div>
                    <div class="stat-label">Always Available</div>
                </div>
            </div>

            <!-- Features Overview -->
            <div class="section-header" style="margin-top:4rem">
                <div class="section-badge">✨ Platform Features</div>
                <h2 class="section-title">Why WasteWise?</h2>
                <p class="section-subtitle">A public-first initiative for sustainable waste management and environmental protection.</p>
            </div>

            <div class="why-grid">
                <div class="why-card reveal">
                    <div class="why-icon-wrap"><span class="why-emoji">⚡</span></div>
                    <div class="why-title">Instant Search</div>
                    <p class="why-desc">Search for any household item or material to immediately get verified disposal recommendations.</p>
                </div>
                <div class="why-card reveal">
                    <div class="why-icon-wrap"><span class="why-emoji">🛡️</span></div>
                    <div class="why-title">Safe Disposal Guidelines</div>
                    <p class="why-desc">Clear step-by-step instructions and safety precautions to protect human health and the environment.</p>
                </div>
                <div class="why-card reveal">
                    <div class="why-icon-wrap"><span class="why-emoji">📍</span></div>
                    <div class="why-title">Centre Directory</div>
                    <p class="why-desc">Locate verified recycling centres, e-waste facilities, and municipal drop-off points with direct turn-by-turn directions.</p>
                </div>
            </div>

        </div>
    </section>

    <!-- CTA -->
    <section class="cta-section">
        <div class="container">
            <div class="cta-inner">
                <h2 class="cta-title">Be Part of the Change</h2>
                <p class="cta-desc">Start using WasteWise today and take one small step toward a cleaner, greener planet.</p>
                <button class="cta-btn" data-page="home">🔍 Try WasteWise Now</button>
            </div>
        </div>
    </section>

</div>

<!-- ═══════════════════════════════════════════════════════════════════════════
     PAGE 6 – ADMIN LOGIN (UI ONLY)
════════════════════════════════════════════════════════════════════════════ -->
<div class="page" id="page-admin-login">
    <div class="admin-login-wrap">
        <div class="admin-login-card">
            <div class="admin-login-header">
                <div class="admin-login-icon">🔒</div>
                <div class="admin-login-title">Admin Portal</div>
                <div class="admin-login-sub">WasteWise Management Console</div>
            </div>
            <div class="admin-login-body">
                <form id="admin-login-form" novalidate>
                    <div class="form-group">
                        <label class="form-label" for="admin-email">Email Address</label>
                        <input
                            id="admin-email"
                            class="form-control"
                            type="email"
                            placeholder="admin@wastewise.in"
                            autocomplete="username"
                            required
                        >
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="admin-password">Password</label>
                        <input
                            id="admin-password"
                            class="form-control"
                            type="password"
                            placeholder="••••••••"
                            autocomplete="current-password"
                            required
                        >
                    </div>
                    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:.75rem">
                        <label style="display:flex;align-items:center;gap:.5rem;font-size:.84rem;color:var(--text-muted);cursor:pointer">
                            <input type="checkbox" style="accent-color:var(--primary)"> Remember me
                        </label>
                        <a href="#" style="font-size:.84rem;color:var(--primary);font-weight:500">Forgot password?</a>
                    </div>
                    <button type="submit" class="form-submit">
                        🔓 Sign In to Admin
                    </button>
                </form>
                <div class="admin-note">
                    🔒 Authorized WasteWise Personnel Only.
                </div>
                <div style="text-align:center;margin-top:1rem">
                    <button class="btn btn-outline" style="font-size:.82rem;padding:.4rem .9rem" data-page="home">← Back to Home</button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ═══════════════════════════════════════════════════════════════════════════
     PAGE 7 – ADMIN DASHBOARD (UI PLACEHOLDER)
════════════════════════════════════════════════════════════════════════════ -->
<div class="page" id="page-admin-dash">

    <div class="admin-dash-header">
        <div class="container">
            <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:1rem">
                <div>
                    <div class="admin-dash-title">🌿 WasteWise Admin</div>
                    <div class="admin-dash-sub">Management Dashboard</div>
                </div>
                <div style="display:flex;align-items:center;gap:.75rem">
                    <div style="background:rgba(255,255,255,.15);border:1px solid rgba(255,255,255,.3);border-radius:var(--radius-md);padding:.45rem 1rem;font-size:.85rem;color:rgba(255,255,255,.9)">
                        👤 Admin User
                    </div>
                    <button class="btn btn-ghost" style="font-size:.82rem;padding:.4rem .9rem" data-page="adminLogin">🚪 Logout</button>
                </div>
            </div>
        </div>
    </div>

    <div class="section" style="padding-top:2rem">
        <div class="container">

            <!-- Stats -->
            <div class="admin-stats-grid">
                <div class="admin-stat-card">
                    <div class="admin-stat-icon" style="background:var(--primary-pale)">💻</div>
                    <div>
                        <div class="admin-stat-num">50</div>
                        <div class="admin-stat-label">Waste Items</div>
                    </div>
                </div>
                <div class="admin-stat-card">
                    <div class="admin-stat-icon" style="background:#eff6ff">🏷️</div>
                    <div>
                        <div class="admin-stat-num">8</div>
                        <div class="admin-stat-label">Categories</div>
                    </div>
                </div>
                <div class="admin-stat-card">
                    <div class="admin-stat-icon" style="background:#faf5ff">♻️</div>
                    <div>
                        <div class="admin-stat-num">12</div>
                        <div class="admin-stat-label">Collection Centres</div>
                    </div>
                </div>
                <div class="admin-stat-card">
                    <div class="admin-stat-icon" style="background:#fff7ed">📋</div>
                    <div>
                        <div class="admin-stat-num">—</div>
                        <div class="admin-stat-label">Searches Today</div>
                    </div>
                </div>
                <div class="admin-stat-card">
                    <div class="admin-stat-icon" style="background:#f0fdf4">🔧</div>
                    <div>
                        <div class="admin-stat-num">10</div>
                        <div class="admin-stat-label">Disposal Methods</div>
                    </div>
                </div>
                <div class="admin-stat-card">
                    <div class="admin-stat-icon" style="background:#fef2f2">🌆</div>
                    <div>
                        <div class="admin-stat-num">10</div>
                        <div class="admin-stat-label">Cities</div>
                    </div>
                </div>
            </div>

            <!-- Placeholder Panels -->
            <div class="admin-placeholder-grid">
                <div class="admin-placeholder-card">
                    <div class="admin-placeholder-title">
                        📋 Waste Items Management
                    </div>
                    <div class="admin-coming-soon">
                        <div class="icon">🔐</div>
                        <div class="label">Admin CRUD functionality coming soon</div>
                        <div style="font-size:.8rem;color:var(--text-light)">Authentication required — Phase 2 implementation</div>
                    </div>
                </div>
                <div class="admin-placeholder-card">
                    <div class="admin-placeholder-title">
                        📊 Recent Searches
                    </div>
                    <div class="admin-coming-soon">
                        <div class="icon">📈</div>
                        <div class="label">Analytics dashboard coming soon</div>
                        <div style="font-size:.8rem;color:var(--text-light)">Phase 2 implementation</div>
                    </div>
                </div>
            </div>

            <div class="admin-placeholder-grid" style="margin-top:1.5rem">
                <div class="admin-placeholder-card">
                    <div class="admin-placeholder-title">📍 Collection Centres</div>
                    <div class="admin-coming-soon">
                        <div class="icon">🗺️</div>
                        <div class="label">Centre management coming soon</div>
                    </div>
                </div>
                <div class="admin-placeholder-card">
                    <div class="admin-placeholder-title">⚙️ System Settings</div>
                    <div class="admin-coming-soon">
                        <div class="icon">⚙️</div>
                        <div class="label">Settings panel coming soon</div>
                    </div>
                </div>
            </div>

            <!-- Note -->
            <div style="background:var(--primary-pale);border:1.5px solid var(--border);border-radius:var(--radius-xl);padding:1.5rem 2rem;margin-top:2rem;display:flex;align-items:flex-start;gap:1rem">
                <span style="font-size:1.5rem">🌿</span>
                <div>
                    <div style="font-family:'Outfit',sans-serif;font-weight:700;color:var(--text-dark);margin-bottom:.3rem">WasteWise Administration Console</div>
                    <p style="font-size:.88rem;color:var(--text-muted);line-height:1.65">
                        This management console enables platform administrators to manage waste category catalogs, disposal methods, collection center directories, and system analytics.
                    </p>
                </div>
            </div>

            <div style="text-align:center;margin-top:2rem">
                <button class="btn btn-primary" data-page="home">🏠 Back to WasteWise</button>
            </div>
        </div>
    </div>

</div>

<!-- ═══════════════════════════════════════════════════════════════════════════
     TOAST NOTIFICATION
════════════════════════════════════════════════════════════════════════════ -->
<div class="toast" id="toast"></div>

<!-- Inline script for category card clicks (needs to reference app.js triggerSearch) -->
<script>
function searchCategory(q) {
    // Trigger search - app.js exposes triggerSearch globally
    if (typeof triggerSearch === 'function') {
        triggerSearch(q);
    }
}
</script>

</body>
</html>
