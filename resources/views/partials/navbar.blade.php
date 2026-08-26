<!-- ═══════════════════════════════════════════════════════════ NAVBAR ═══ -->
<nav class="navbar" id="navbar">
    <div class="container">
        <div class="navbar-inner">
            <!-- Logo -->
            <a href="{{ route('home') }}" class="logo" aria-label="WasteWise Home">
                <div class="logo-icon">🌿</div>
                <span class="logo-text">WasteWise</span>
            </a>

            <!-- Desktop Nav Links -->
            <div class="nav-links">
                <a class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}" href="{{ route('home') }}" id="nav-home">🏠 Home</a>
                <a class="nav-link" href="{{ url('/#how') }}" id="nav-how">⚙️ How It Works</a>
                <a class="nav-link" href="{{ url('/#centres') }}" id="nav-centres">📍 Collection Centres</a>
                <a class="nav-link" href="{{ url('/#about') }}" id="nav-about">ℹ️ About</a>
                <a class="nav-link {{ request()->routeIs('feedback.*') ? 'active' : '' }}" href="{{ route('feedback.create') }}">💬 Feedback</a>

                @guest
                    <a class="nav-link {{ request()->routeIs('login') ? 'active' : '' }}" href="{{ route('login') }}">🔑 Login</a>
                    <a class="nav-link {{ request()->routeIs('register') ? 'active' : '' }}" href="{{ route('register') }}" style="background:var(--primary);color:#fff;padding:.4rem .9rem;border-radius:var(--radius-full);font-weight:600;">✨ Register</a>
                @endguest

                @auth
                    @if(auth()->user()->isAdmin())
                        <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}">🔒 Admin Dashboard</a>
                        <form action="{{ route('logout') }}" method="POST" style="display:inline;">
                            @csrf
                            <button type="submit" class="nav-link" style="background:none;border:none;cursor:pointer;color:#dc2626;font-weight:600;">🚪 Logout</button>
                        </form>
                    @else
                        <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">👤 Dashboard</a>
                        <form action="{{ route('logout') }}" method="POST" style="display:inline;">
                            @csrf
                            <button type="submit" class="nav-link" style="background:none;border:none;cursor:pointer;color:#dc2626;font-weight:600;">🚪 Logout</button>
                        </form>
                    @endif
                @endauth
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
            <a class="mobile-nav-link" href="{{ route('home') }}">🏠 Home</a>
            <a class="mobile-nav-link" href="{{ url('/#how') }}">⚙️ How It Works</a>
            <a class="mobile-nav-link" href="{{ url('/#centres') }}">📍 Collection Centres</a>
            <a class="mobile-nav-link" href="{{ url('/#about') }}">ℹ️ About</a>
            <a class="mobile-nav-link" href="{{ route('feedback.create') }}">💬 Feedback</a>

            @guest
                <a class="mobile-nav-link" href="{{ route('login') }}">🔑 Login</a>
                <a class="mobile-nav-link" href="{{ route('register') }}">✨ Register</a>
            @endguest

            @auth
                @if(auth()->user()->isAdmin())
                    <a class="mobile-nav-link" href="{{ route('admin.dashboard') }}">🔒 Admin Dashboard</a>
                    <form action="{{ route('logout') }}" method="POST" style="margin-top:.5rem;">
                        @csrf
                        <button type="submit" class="mobile-nav-link" style="width:100%;text-align:left;background:none;border:none;color:#dc2626;font-weight:600;">🚪 Logout</button>
                    </form>
                @else
                    <a class="mobile-nav-link" href="{{ route('dashboard') }}">👤 Dashboard</a>
                    <form action="{{ route('logout') }}" method="POST" style="margin-top:.5rem;">
                        @csrf
                        <button type="submit" class="mobile-nav-link" style="width:100%;text-align:left;background:none;border:none;color:#dc2626;font-weight:600;">🚪 Logout</button>
                    </form>
                @endif
            @endauth
        </div>
    </div>
</div>
