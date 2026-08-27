@extends('layouts.app')

@section('title', 'Admin Dashboard – WasteWise')

@section('content')
<section class="section" style="padding-block:2.5rem 5rem;">
    <div class="container">
        
        <!-- Welcome Hero Banner -->
        <div style="background:linear-gradient(135deg, #065f46 0%, #16a34a 100%);color:#fff;border-radius:var(--radius-xl);padding:2.5rem;box-shadow:var(--shadow-lg);margin-bottom:2.5rem;position:relative;overflow:hidden;">
            <div style="position:relative;z-index:2;display:flex;flex-wrap:wrap;align-items:center;justify-content:space-between;gap:1.5rem;">
                <div>
                    <div style="display:inline-flex;align-items:center;gap:.5rem;background:rgba(255,255,255,.2);backdrop-filter:blur(10px);padding:.35rem .9rem;border-radius:var(--radius-full);font-size:.85rem;font-weight:600;margin-bottom:1rem;color:#dcfce7;">
                        🔒 WasteWise Administrator
                    </div>
                    <h1 style="font-family:'Outfit',sans-serif;font-size:2.2rem;font-weight:800;line-height:1.2;margin-bottom:.5rem;">
                        WasteWise Management Console
                    </h1>
                    <p style="color:#dcfce7;font-size:1.05rem;max-width:550px;">
                        Logged in as <strong style="color:#fff;">{{ auth()->user()->email }}</strong>. Full real-time oversight of waste items, categories, centres, users, and feedback.
                    </p>
                </div>

                <div style="display:flex;gap:1rem;flex-wrap:wrap;align-items:center;">
                    <form action="{{ route('admin.logout') }}" method="POST" style="margin:0;">
                        @csrf
                        <button type="submit" style="background:rgba(255,255,255,.15);border:1px solid rgba(255,255,255,.3);color:#fff;padding:.75rem 1.4rem;border-radius:var(--radius-full);font-weight:700;font-size:.9rem;cursor:pointer;display:inline-flex;align-items:center;gap:.5rem;transition:var(--transition);" onmouseover="this.style.background='rgba(255,255,255,.25)';" onmouseout="this.style.background='rgba(255,255,255,.15)';">
                            🚪 Logout Admin
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Database Metrics Grid (Real Statistics) -->
        <div style="display:grid;grid-template-columns:repeat(auto-fit, minmax(210px, 1fr));gap:1.25rem;margin-bottom:2.5rem;">
            
            <a href="{{ route('admin.waste-items.index') }}" style="background:var(--bg-white);border:1px solid var(--border);border-radius:var(--radius-xl);padding:1.5rem;box-shadow:var(--shadow-sm);display:flex;align-items:center;gap:1rem;transition:var(--transition);" onmouseover="this.style.transform='translateY(-2px)';" onmouseout="this.style.transform='none';">
                <div style="width:48px;height:48px;background:var(--primary-pale);color:var(--primary-dark);border-radius:var(--radius-md);display:flex;align-items:center;justify-content:center;font-size:1.5rem;">💻</div>
                <div>
                    <div style="font-family:'Outfit',sans-serif;font-size:1.8rem;font-weight:800;color:var(--text-dark);line-height:1;">{{ $stats['waste_items'] }}</div>
                    <div style="color:var(--text-muted);font-size:.85rem;font-weight:600;margin-top:.2rem;">Waste Items</div>
                </div>
            </a>

            <a href="{{ route('admin.categories.index') }}" style="background:var(--bg-white);border:1px solid var(--border);border-radius:var(--radius-xl);padding:1.5rem;box-shadow:var(--shadow-sm);display:flex;align-items:center;gap:1rem;transition:var(--transition);" onmouseover="this.style.transform='translateY(-2px)';" onmouseout="this.style.transform='none';">
                <div style="width:48px;height:48px;background:#eff6ff;color:#1d4ed8;border-radius:var(--radius-md);display:flex;align-items:center;justify-content:center;font-size:1.5rem;">🏷️</div>
                <div>
                    <div style="font-family:'Outfit',sans-serif;font-size:1.8rem;font-weight:800;color:var(--text-dark);line-height:1;">{{ $stats['categories'] }}</div>
                    <div style="color:var(--text-muted);font-size:.85rem;font-weight:600;margin-top:.2rem;">Categories</div>
                </div>
            </a>

            <a href="{{ route('admin.disposal-methods.index') }}" style="background:var(--bg-white);border:1px solid var(--border);border-radius:var(--radius-xl);padding:1.5rem;box-shadow:var(--shadow-sm);display:flex;align-items:center;gap:1rem;transition:var(--transition);" onmouseover="this.style.transform='translateY(-2px)';" onmouseout="this.style.transform='none';">
                <div style="width:48px;height:48px;background:#f0fdf4;color:#15803d;border-radius:var(--radius-md);display:flex;align-items:center;justify-content:center;font-size:1.5rem;">🔧</div>
                <div>
                    <div style="font-family:'Outfit',sans-serif;font-size:1.8rem;font-weight:800;color:var(--text-dark);line-height:1;">{{ $stats['disposal_methods'] }}</div>
                    <div style="color:var(--text-muted);font-size:.85rem;font-weight:600;margin-top:.2rem;">Disposal Methods</div>
                </div>
            </a>

            <a href="{{ route('admin.cities.index') }}" style="background:var(--bg-white);border:1px solid var(--border);border-radius:var(--radius-xl);padding:1.5rem;box-shadow:var(--shadow-sm);display:flex;align-items:center;gap:1rem;transition:var(--transition);" onmouseover="this.style.transform='translateY(-2px)';" onmouseout="this.style.transform='none';">
                <div style="width:48px;height:48px;background:#fef2f2;color:#b91c1c;border-radius:var(--radius-md);display:flex;align-items:center;justify-content:center;font-size:1.5rem;">🌆</div>
                <div>
                    <div style="font-family:'Outfit',sans-serif;font-size:1.8rem;font-weight:800;color:var(--text-dark);line-height:1;">{{ $stats['cities'] }}</div>
                    <div style="color:var(--text-muted);font-size:.85rem;font-weight:600;margin-top:.2rem;">Cities</div>
                </div>
            </a>

            <a href="{{ route('admin.collection-centres.index') }}" style="background:var(--bg-white);border:1px solid var(--border);border-radius:var(--radius-xl);padding:1.5rem;box-shadow:var(--shadow-sm);display:flex;align-items:center;gap:1rem;transition:var(--transition);" onmouseover="this.style.transform='translateY(-2px)';" onmouseout="this.style.transform='none';">
                <div style="width:48px;height:48px;background:#faf5ff;color:#7e22ce;border-radius:var(--radius-md);display:flex;align-items:center;justify-content:center;font-size:1.5rem;">📍</div>
                <div>
                    <div style="font-family:'Outfit',sans-serif;font-size:1.8rem;font-weight:800;color:var(--text-dark);line-height:1;">{{ $stats['collection_centres'] }}</div>
                    <div style="color:var(--text-muted);font-size:.85rem;font-weight:600;margin-top:.2rem;">Collection Centres</div>
                </div>
            </a>

            <a href="{{ route('admin.users.index') }}" style="background:var(--bg-white);border:1px solid var(--border);border-radius:var(--radius-xl);padding:1.5rem;box-shadow:var(--shadow-sm);display:flex;align-items:center;gap:1rem;transition:var(--transition);" onmouseover="this.style.transform='translateY(-2px)';" onmouseout="this.style.transform='none';">
                <div style="width:48px;height:48px;background:#fdf2f8;color:#be185d;border-radius:var(--radius-md);display:flex;align-items:center;justify-content:center;font-size:1.5rem;">👥</div>
                <div>
                    <div style="font-family:'Outfit',sans-serif;font-size:1.8rem;font-weight:800;color:var(--text-dark);line-height:1;">{{ $stats['users'] }}</div>
                    <div style="color:var(--text-muted);font-size:.85rem;font-weight:600;margin-top:.2rem;">Registered Users</div>
                </div>
            </a>

            <a href="{{ route('admin.search-history.index') }}" style="background:var(--bg-white);border:1px solid var(--border);border-radius:var(--radius-xl);padding:1.5rem;box-shadow:var(--shadow-sm);display:flex;align-items:center;gap:1rem;transition:var(--transition);" onmouseover="this.style.transform='translateY(-2px)';" onmouseout="this.style.transform='none';">
                <div style="width:48px;height:48px;background:#fff7ed;color:#c2410c;border-radius:var(--radius-md);display:flex;align-items:center;justify-content:center;font-size:1.5rem;">📊</div>
                <div>
                    <div style="font-family:'Outfit',sans-serif;font-size:1.8rem;font-weight:800;color:var(--text-dark);line-height:1;">{{ $stats['searches_today'] }}</div>
                    <div style="color:var(--text-muted);font-size:.85rem;font-weight:600;margin-top:.2rem;">Searches Today</div>
                </div>
            </a>

            <a href="{{ route('admin.announcements.index') }}" style="background:var(--bg-white);border:1px solid var(--border);border-radius:var(--radius-xl);padding:1.5rem;box-shadow:var(--shadow-sm);display:flex;align-items:center;gap:1rem;transition:var(--transition);" onmouseover="this.style.transform='translateY(-2px)';" onmouseout="this.style.transform='none';">
                <div style="width:48px;height:48px;background:#ecfdf5;color:#047857;border-radius:var(--radius-md);display:flex;align-items:center;justify-content:center;font-size:1.5rem;">📢</div>
                <div>
                    <div style="font-family:'Outfit',sans-serif;font-size:1.8rem;font-weight:800;color:var(--text-dark);line-height:1;">{{ $stats['announcements_total'] }}</div>
                    <div style="color:var(--text-muted);font-size:.85rem;font-weight:600;margin-top:.2rem;">Announcements ({{ $stats['announcements_published'] }} Published / {{ $stats['announcements_draft'] }} Draft)</div>
                </div>
            </a>

            <a href="{{ route('admin.feedback.index') }}" style="background:var(--bg-white);border:1px solid var(--border);border-radius:var(--radius-xl);padding:1.5rem;box-shadow:var(--shadow-sm);display:flex;align-items:center;gap:1rem;transition:var(--transition);" onmouseover="this.style.transform='translateY(-2px)';" onmouseout="this.style.transform='none';">
                <div style="width:48px;height:48px;background:#fefce8;color:#a16207;border-radius:var(--radius-md);display:flex;align-items:center;justify-content:center;font-size:1.5rem;">💬</div>
                <div>
                    <div style="font-family:'Outfit',sans-serif;font-size:1.8rem;font-weight:800;color:var(--text-dark);line-height:1;">{{ $stats['pending_feedback'] }}</div>
                    <div style="color:var(--text-muted);font-size:.85rem;font-weight:600;margin-top:.2rem;">Pending Feedback</div>
                </div>
            </a>

            <div style="background:var(--bg-white);border:1px solid var(--border);border-radius:var(--radius-xl);padding:1.5rem;box-shadow:var(--shadow-sm);display:flex;align-items:center;gap:1rem;">
                <div style="width:48px;height:48px;background:#fef3c7;color:#b45309;border-radius:var(--radius-md);display:flex;align-items:center;justify-content:center;font-size:1.5rem;">⭐</div>
                <div>
                    <div style="font-family:'Outfit',sans-serif;font-size:1.8rem;font-weight:800;color:var(--text-dark);line-height:1;">{{ $stats['avg_rating'] }} / 5</div>
                    <div style="color:var(--text-muted);font-size:.85rem;font-weight:600;margin-top:.2rem;">Average Rating</div>
                </div>
            </div>

        </div>

        <!-- Active Admin Management Navigation Modules -->
        <div style="background:var(--bg-white);border:1px solid var(--border);border-radius:var(--radius-xl);padding:2rem;box-shadow:var(--shadow-md);">
            <h2 style="font-family:'Outfit',sans-serif;font-size:1.4rem;font-weight:800;color:var(--text-dark);margin-bottom:.5rem;display:flex;align-items:center;gap:.6rem;">
                ⚙️ Admin Management Modules
            </h2>
            <p style="color:var(--text-muted);font-size:.95rem;margin-bottom:1.5rem;">
                Select a module to manage WasteWise database records.
            </p>

            <div style="display:grid;grid-template-columns:repeat(auto-fit, minmax(240px, 1fr));gap:1.25rem;">
                
                <a href="{{ route('admin.announcements.index') }}" style="border:1px solid var(--border);border-radius:var(--radius-lg);padding:1.25rem;background:var(--bg-card);transition:var(--transition);display:block;" onmouseover="this.style.borderColor='var(--primary)';" onmouseout="this.style.borderColor='var(--border)';">
                    <div style="font-weight:700;color:var(--text-dark);font-size:1.05rem;margin-bottom:.3rem;">📢 Announcements</div>
                    <div style="font-size:.85rem;color:var(--text-muted);">Create, publish, edit, and delete announcements & updates</div>
                </a>

                <a href="{{ route('admin.waste-items.index') }}" style="border:1px solid var(--border);border-radius:var(--radius-lg);padding:1.25rem;background:var(--bg-card);transition:var(--transition);display:block;" onmouseover="this.style.borderColor='var(--primary)';" onmouseout="this.style.borderColor='var(--border)';">
                    <div style="font-weight:700;color:var(--text-dark);font-size:1.05rem;margin-bottom:.3rem;">📦 Waste Items</div>
                    <div style="font-size:.85rem;color:var(--text-muted);">View, add, edit, and delete waste items catalog</div>
                </a>

                <a href="{{ route('admin.categories.index') }}" style="border:1px solid var(--border);border-radius:var(--radius-lg);padding:1.25rem;background:var(--bg-card);transition:var(--transition);display:block;" onmouseover="this.style.borderColor='var(--primary)';" onmouseout="this.style.borderColor='var(--border)';">
                    <div style="font-weight:700;color:var(--text-dark);font-size:1.05rem;margin-bottom:.3rem;">🏷️ Categories</div>
                    <div style="font-size:.85rem;color:var(--text-muted);">Manage waste categories and descriptions</div>
                </a>

                <a href="{{ route('admin.disposal-methods.index') }}" style="border:1px solid var(--border);border-radius:var(--radius-lg);padding:1.25rem;background:var(--bg-card);transition:var(--transition);display:block;" onmouseover="this.style.borderColor='var(--primary)';" onmouseout="this.style.borderColor='var(--border)';">
                    <div style="font-weight:700;color:var(--text-dark);font-size:1.05rem;margin-bottom:.3rem;">♻️ Disposal Methods</div>
                    <div style="font-size:.85rem;color:var(--text-muted);">Manage disposal instructions and safety guidance</div>
                </a>

                <a href="{{ route('admin.cities.index') }}" style="border:1px solid var(--border);border-radius:var(--radius-lg);padding:1.25rem;background:var(--bg-card);transition:var(--transition);display:block;" onmouseover="this.style.borderColor='var(--primary)';" onmouseout="this.style.borderColor='var(--border)';">
                    <div style="font-weight:700;color:var(--text-dark);font-size:1.05rem;margin-bottom:.3rem;">🏙️ Cities</div>
                    <div style="font-size:.85rem;color:var(--text-muted);">Manage coverage cities for centres</div>
                </a>

                <a href="{{ route('admin.collection-centres.index') }}" style="border:1px solid var(--border);border-radius:var(--radius-lg);padding:1.25rem;background:var(--bg-card);transition:var(--transition);display:block;" onmouseover="this.style.borderColor='var(--primary)';" onmouseout="this.style.borderColor='var(--border)';">
                    <div style="font-weight:700;color:var(--text-dark);font-size:1.05rem;margin-bottom:.3rem;">📍 Collection Centres</div>
                    <div style="font-size:.85rem;color:var(--text-muted);">Manage recycling centres and contact info</div>
                </a>

                <a href="{{ route('admin.users.index') }}" style="border:1px solid var(--border);border-radius:var(--radius-lg);padding:1.25rem;background:var(--bg-card);transition:var(--transition);display:block;" onmouseover="this.style.borderColor='var(--primary)';" onmouseout="this.style.borderColor='var(--border)';">
                    <div style="font-weight:700;color:var(--text-dark);font-size:1.05rem;margin-bottom:.3rem;">👥 Users</div>
                    <div style="font-size:.85rem;color:var(--text-muted);">Manage registered users and admin privileges</div>
                </a>

                <a href="{{ route('admin.search-history.index') }}" style="border:1px solid var(--border);border-radius:var(--radius-lg);padding:1.25rem;background:var(--bg-card);transition:var(--transition);display:block;" onmouseover="this.style.borderColor='var(--primary)';" onmouseout="this.style.borderColor='var(--border)';">
                    <div style="font-weight:700;color:var(--text-dark);font-size:1.05rem;margin-bottom:.3rem;">📊 Search History</div>
                    <div style="font-size:.85rem;color:var(--text-muted);">Monitor user waste search activity logs</div>
                </a>

                <a href="{{ route('admin.feedback.index') }}" style="border:1px solid var(--border);border-radius:var(--radius-lg);padding:1.25rem;background:var(--bg-card);transition:var(--transition);display:block;" onmouseover="this.style.borderColor='var(--primary)';" onmouseout="this.style.borderColor='var(--border)';">
                    <div style="font-weight:700;color:var(--text-dark);font-size:1.05rem;margin-bottom:.3rem;">💬 User Feedback</div>
                    <div style="font-size:.85rem;color:var(--text-muted);">Review user ratings, reviews, and suggestions</div>
                </a>

            </div>
        </div>

    </div>
</section>
@endsection
