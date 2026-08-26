/**
 * WasteWise – Frontend JavaScript
 * Connects to Laravel API at http://localhost:8000/api/waste/search
 */

const API_BASE = window.location.origin ? `${window.location.origin}/api` : '/api';

/* ── Page Router ──────────────────────────────────────────────────────────── */
const pages = {
    home: document.getElementById('page-home'),
    results: document.getElementById('page-results'),
    centres: document.getElementById('page-centres'),
    how: document.getElementById('page-how'),
    about: document.getElementById('page-about'),
    adminLogin: document.getElementById('page-admin-login'),
    adminDash: document.getElementById('page-admin-dash'),
};

let currentPage = 'home';

function navigateTo(pageId, options = {}) {
    // Close mobile nav if open
    closeMobileNav();

    // Hide all pages
    Object.values(pages).forEach(p => { if (p) p.classList.remove('active'); });

    // Show target page
    if (pages[pageId]) {
        pages[pageId].classList.add('active');
        currentPage = pageId;

        // Update active nav links
        const navLinks = {
            home: document.getElementById('nav-home'),
            how: document.getElementById('nav-how'),
            centres: document.getElementById('nav-centres'),
            about: document.getElementById('nav-about'),
        };
        Object.entries(navLinks).forEach(([id, el]) => {
            if (el) el.classList.toggle('active', id === pageId);
        });

        document.querySelectorAll('[data-page]').forEach(el => {
            el.classList.toggle('active', el.dataset.page === pageId);
        });

        // Scroll to top
        window.scrollTo({ top: 0, behavior: 'smooth' });

        // Page-specific init
        if (pageId === 'centres') initCentresPage();
        if (pageId === 'results' && options.query) doSearch(options.query);

        // Init scroll reveal
        setTimeout(initReveal, 100);
    }
}

/* ── Mobile Nav ───────────────────────────────────────────────────────────── */
const hamburger = document.getElementById('hamburger');
const mobileNav = document.getElementById('mobile-nav');
const mobileNavClose = document.getElementById('mobile-nav-close');

function openMobileNav() {
    mobileNav.classList.add('open');
    hamburger.classList.add('open');
    document.body.style.overflow = 'hidden';
}

function closeMobileNav() {
    mobileNav.classList.remove('open');
    hamburger.classList.remove('open');
    document.body.style.overflow = '';
}

hamburger?.addEventListener('click', openMobileNav);
mobileNavClose?.addEventListener('click', closeMobileNav);
mobileNav?.addEventListener('click', e => {
    if (e.target === mobileNav || e.target.closest('.mobile-nav-link') || e.target.closest('button')) {
        closeMobileNav();
    }
});

/* ── Navbar scroll effect ─────────────────────────────────────────────────── */
const navbar = document.getElementById('navbar');
window.addEventListener('scroll', () => {
    navbar.classList.toggle('scrolled', window.scrollY > 20);
}, { passive: true });

/* ── Search Logic ─────────────────────────────────────────────────────────── */
const heroSearchInput = document.getElementById('hero-search-input');
const heroSearchBtn   = document.getElementById('hero-search-btn');
const heroSearchClear = document.getElementById('hero-search-clear');
const heroValidation  = document.getElementById('hero-validation');

const resSearchInput = document.getElementById('res-search-input');
const resSearchBtn   = document.getElementById('res-search-btn');
const resSearchClear = document.getElementById('res-search-clear');

// Sync inputs
function syncSearchInputs(val) {
    if (heroSearchInput) heroSearchInput.value = val;
    if (resSearchInput)  resSearchInput.value  = val;
    toggleClearBtns(val);
}

function toggleClearBtns(val) {
    const show = val.trim().length > 0;
    heroSearchClear?.classList.toggle('visible', show);
    resSearchClear?.classList.toggle('visible', show);
}

heroSearchInput?.addEventListener('input', e => {
    toggleClearBtns(e.target.value);
    hideValidation();
    if (resSearchInput) resSearchInput.value = e.target.value;
});

resSearchInput?.addEventListener('input', e => {
    toggleClearBtns(e.target.value);
    if (heroSearchInput) heroSearchInput.value = e.target.value;
});

heroSearchClear?.addEventListener('click', () => {
    syncSearchInputs('');
    heroSearchInput?.focus();
});

resSearchClear?.addEventListener('click', () => {
    syncSearchInputs('');
    resSearchInput?.focus();
});

// Enter key search
[heroSearchInput, resSearchInput].forEach(inp => {
    inp?.addEventListener('keydown', e => {
        if (e.key === 'Enter') triggerSearch(inp.value);
    });
});

heroSearchBtn?.addEventListener('click', () => triggerSearch(heroSearchInput.value));
resSearchBtn?.addEventListener('click',   () => triggerSearch(resSearchInput.value));

function triggerSearch(rawQuery) {
    const q = rawQuery.trim();

    if (!q) {
        showValidation('Please enter a waste item to search.');
        return;
    }
    if (q.length < 2) {
        showValidation('Search term must be at least 2 characters.');
        return;
    }

    hideValidation();
    syncSearchInputs(q);
    navigateTo('results', { query: q });
}

function showValidation(msg) {
    if (heroValidation) {
        heroValidation.textContent = msg;
        heroValidation.style.opacity = '1';
    }
}

function hideValidation() {
    if (heroValidation) {
        heroValidation.textContent = '';
        heroValidation.style.opacity = '0';
    }
}

/* ── Chips ────────────────────────────────────────────────────────────────── */
document.querySelectorAll('[data-chip]').forEach(chip => {
    chip.addEventListener('click', () => {
        const query = chip.dataset.chip;
        triggerSearch(query);
    });
});

/* ── API Search ───────────────────────────────────────────────────────────── */
const resultLoading  = document.getElementById('result-loading');
const resultSuccess  = document.getElementById('result-success');
const resultNotFound = document.getElementById('result-not-found');
const resultError    = document.getElementById('result-error');
const resultQueryLabel = document.getElementById('result-query-label');

function showResultState(state) {
    [resultLoading, resultSuccess, resultNotFound, resultError].forEach(el => {
        if (el) el.style.display = 'none';
    });
    const el = { loading: resultLoading, success: resultSuccess, notFound: resultNotFound, error: resultError }[state];
    if (el) el.style.display = '';
}

async function doSearch(query) {
    if (resultQueryLabel) resultQueryLabel.textContent = `"${query}"`;

    showResultState('loading');

    try {
        const url = `${API_BASE}/waste/search?q=${encodeURIComponent(query)}`;
        const resp = await fetch(url, {
            method: 'GET',
            headers: { 'Accept': 'application/json', 'Content-Type': 'application/json' },
            credentials: 'same-origin',
        });

        if (resp.status === 404) {
            const data = await resp.json().catch(() => ({}));
            showNotFound(data.message);
            return;
        }

        if (!resp.ok) {
            showError();
            return;
        }

        const data = await resp.json();

        if (!data.success || !data.data) {
            showNotFound(data.message);
            return;
        }

        renderResult(data.data);
        showResultState('success');
        setTimeout(initReveal, 80);

    } catch (err) {
        console.error('WasteWise API error:', err);
        showError();
    }
}

function showNotFound(msg) {
    const msgEl = document.getElementById('not-found-msg');
    if (msgEl) {
        msgEl.textContent = msg || 'Sorry, we couldn\'t find this waste item. Try another name such as Old Laptop, Old Mobile, Plastic Bottle, or Newspaper.';
    }
    showResultState('notFound');
}

function showError() {
    showResultState('error');
}

function renderResult(data) {
    // Item name & description
    setText('res-item-name', data.name);
    setText('res-item-desc', data.description);

    // Category badge
    const catName = data.category?.name ?? 'Uncategorized';
    setText('res-category-badge', catName);
    setText('res-category-label', catName);
    setCategoryEmoji('res-category-emoji', catName);

    // Disposal method
    const dm = data.disposal_method;
    setText('res-disposal-method', dm?.name ?? 'N/A');
    setText('res-instructions', dm?.instructions ?? 'No specific instructions available.');
    setText('res-avoid', dm?.avoid ?? 'No specific warnings available.');

    // Collection centres
    renderCentres(data.collection_centres ?? []);
}

function setText(id, value) {
    const el = document.getElementById(id);
    if (el) el.textContent = value ?? '';
}

function setCategoryEmoji(id, catName) {
    const el = document.getElementById(id);
    if (!el) return;
    const map = {
        'Electronic Waste': '💻',
        'E-Waste': '💻',
        'Plastic Waste': '♻️',
        'Paper Waste': '📰',
        'Glass Waste': '🫙',
        'Organic Waste': '🌱',
        'Metal Waste': '⚙️',
        'Hazardous Waste': '⚠️',
        'Textile Waste': '👕',
        'Biomedical Waste': '🏥',
    };
    el.textContent = map[catName] ?? '🗑️';
}

function renderCentres(centres) {
    const container = document.getElementById('res-centres-list');
    const noCentres = document.getElementById('res-no-centres');
    if (!container) return;

    container.innerHTML = '';

    if (!centres || centres.length === 0) {
        container.style.display = 'none';
        if (noCentres) noCentres.style.display = '';
        return;
    }

    container.style.display = '';
    if (noCentres) noCentres.style.display = 'none';

    centres.forEach(centre => {
        const lat = centre.latitude;
        const lng = centre.longitude;
        const hasCoords = lat && lng;
        const mapsUrl = hasCoords
            ? `https://www.google.com/maps/dir/?api=1&destination=${lat},${lng}`
            : `https://www.google.com/maps/search/?api=1&query=${encodeURIComponent(centre.name + ' ' + (centre.city ?? ''))}`;

        const card = document.createElement('div');
        card.className = 'centre-card reveal';
        card.innerHTML = `
            <div class="centre-name">♻️ ${escHtml(centre.name)}</div>
            ${centre.city ? `<div class="centre-city">📍 ${escHtml(centre.city)}</div>` : ''}
            ${centre.address ? `<div class="centre-detail"><span class="centre-detail-icon">🏢</span><span>${escHtml(centre.address)}</span></div>` : ''}
            ${centre.phone   ? `<div class="centre-detail"><span class="centre-detail-icon">📞</span><span>${escHtml(centre.phone)}</span></div>` : ''}
            ${hasCoords      ? `<div class="centre-detail"><span class="centre-detail-icon">🗺️</span><span>${lat}, ${lng}</span></div>` : ''}
            <a href="${mapsUrl}" target="_blank" rel="noopener noreferrer" class="directions-btn">
                🧭 Get Directions
            </a>
        `;
        container.appendChild(card);
    });
}

function escHtml(str) {
    if (!str) return '';
    return String(str)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#39;');
}

/* ── Collection Centres Page ──────────────────────────────────────────────── */
const centresData = [
    { name: 'GreenEarth Recycling Hub', city: 'Mumbai', address: '45 Marol Industrial Area, Andheri East', phone: '+91 22 2835 4567', category: 'Electronic Waste', lat: 19.1136, lng: 72.8697 },
    { name: 'EcoMart Collection Point', city: 'Pune', address: '12 Hadapsar Industrial Estate', phone: '+91 20 2683 1234', category: 'Plastic Waste', lat: 18.5103, lng: 73.9302 },
    { name: 'CleanCycle Centre', city: 'Bangalore', address: '78 Electronic City Phase 1', phone: '+91 80 2852 3456', category: 'Electronic Waste', lat: 12.8399, lng: 77.6770 },
    { name: 'PaperPath Recyclers', city: 'Delhi', address: '34 Okhla Industrial Area Phase 2', phone: '+91 11 2692 7890', category: 'Paper Waste', lat: 28.5384, lng: 77.2700 },
    { name: 'GlassWorks Collection', city: 'Hyderabad', address: '56 Patancheru Industrial Area', phone: '+91 40 2304 5678', category: 'Glass Waste', lat: 17.5288, lng: 78.2666 },
    { name: 'BioGreen Organics', city: 'Chennai', address: '23 Ambattur Industrial Estate', phone: '+91 44 2625 9012', category: 'Organic Waste', lat: 13.0975, lng: 80.1540 },
    { name: 'MetalMax Scrap Hub', city: 'Kolkata', address: '9 Tangra Industrial Growth Centre', phone: '+91 33 2334 5678', category: 'Metal Waste', lat: 22.5387, lng: 88.3871 },
    { name: 'SafeDrop Hazmat Centre', city: 'Ahmedabad', address: '67 GIDC Vatva Industrial Estate', phone: '+91 79 2583 4567', category: 'Hazardous Waste', lat: 22.9734, lng: 72.6389 },
    { name: 'FibreLoop Textiles', city: 'Surat', address: '14 Sachin GIDC', phone: '+91 261 239 7890', category: 'Textile Waste', lat: 21.1046, lng: 72.8714 },
    { name: 'CityGreen E-Waste', city: 'Mumbai', address: '7 Kalina Business Park, BKC', phone: '+91 22 6744 1234', category: 'Electronic Waste', lat: 19.0670, lng: 72.8647 },
    { name: 'RecyclePoint Nagpur', city: 'Nagpur', address: '22 Hingna MIDC', phone: '+91 712 265 3456', category: 'Plastic Waste', lat: 21.1272, lng: 79.1095 },
    { name: 'EarthCycle Jaipur', city: 'Jaipur', address: '5 Sitapura Industrial Area', phone: '+91 141 271 7890', category: 'Organic Waste', lat: 26.7748, lng: 75.8648 },
];

let centresFiltered = [...centresData];

function initCentresPage() {
    const filterInput = document.getElementById('centres-filter');
    const catFilter   = document.getElementById('centres-cat-filter');
    renderCentresList(centresData);

    filterInput?.addEventListener('input', filterCentres);
    catFilter?.addEventListener('change', filterCentres);
}

function filterCentres() {
    const term = document.getElementById('centres-filter')?.value.toLowerCase() ?? '';
    const cat  = document.getElementById('centres-cat-filter')?.value ?? '';

    centresFiltered = centresData.filter(c => {
        const matchTerm = !term || c.name.toLowerCase().includes(term) ||
            c.city.toLowerCase().includes(term) || c.address.toLowerCase().includes(term);
        const matchCat  = !cat || c.category === cat;
        return matchTerm && matchCat;
    });

    renderCentresList(centresFiltered);
}

function renderCentresList(data) {
    const container = document.getElementById('centres-list-grid');
    if (!container) return;

    if (data.length === 0) {
        container.innerHTML = `<div class="no-centres" style="grid-column:1/-1">
            <p>🔍 No collection centres match your filter.</p>
        </div>`;
        return;
    }

    container.innerHTML = data.map(c => {
        const mapsUrl = `https://www.google.com/maps/dir/?api=1&destination=${c.lat},${c.lng}`;
        return `
        <div class="centre-list-card reveal">
            <div class="centre-list-header">
                <div>
                    <div class="centre-name" style="font-size:1rem;margin-bottom:.3rem">${escHtml(c.name)}</div>
                    <div class="centre-city">📍 ${escHtml(c.city)}</div>
                </div>
                <div class="centre-list-icon">♻️</div>
            </div>
            <div class="centre-detail"><span class="centre-detail-icon">🏢</span><span>${escHtml(c.address)}</span></div>
            <div class="centre-detail"><span class="centre-detail-icon">📞</span><span>${escHtml(c.phone)}</span></div>
            <div class="centre-detail"><span class="centre-detail-icon">🏷️</span><span>${escHtml(c.category)}</span></div>
            <a href="${mapsUrl}" target="_blank" rel="noopener noreferrer" class="directions-btn">
                🧭 Get Directions
            </a>
        </div>`;
    }).join('');

    initReveal();
}

/* ── Scroll Reveal ────────────────────────────────────────────────────────── */
function initReveal() {
    const reveals = document.querySelectorAll('.reveal:not(.revealed)');
    if (!reveals.length) return;

    const observer = new IntersectionObserver(entries => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('revealed');
                observer.unobserve(entry.target);
            }
        });
    }, { threshold: 0.1 });

    reveals.forEach(el => observer.observe(el));
}

/* ── Toast ────────────────────────────────────────────────────────────────── */
function showToast(msg, duration = 3000) {
    const toast = document.getElementById('toast');
    if (!toast) return;
    toast.textContent = msg;
    toast.classList.add('show');
    setTimeout(() => toast.classList.remove('show'), duration);
}

/* ── Navigation event delegation & SPA Init ────────────────────────────────── */
if (document.getElementById('page-home')) {
    function getPageFromHash() {
        const rawHash = window.location.hash.replace(/^#\/?/, '');
        const validPages = ['home', 'how', 'centres', 'about', 'results'];
        return validPages.includes(rawHash) ? rawHash : null;
    }

    document.addEventListener('click', e => {
        // 1. Elements with data-page attribute (e.g. CTA buttons, footer links on welcome.blade.php)
        const dataPageEl = e.target.closest('[data-page]');
        if (dataPageEl) {
            const pageId = dataPageEl.dataset.page;
            if (pages[pageId]) {
                e.preventDefault();
                if (pageId === 'home') {
                    if (window.location.hash) {
                        history.pushState(null, '', window.location.pathname);
                    }
                } else {
                    window.location.hash = pageId;
                }
                navigateTo(pageId);
                return;
            }
        }

        // 2. Anchor links pointing to SPA sections on welcome.blade.php
        const anchor = e.target.closest('a');
        if (anchor && anchor.href) {
            try {
                const url = new URL(anchor.href, window.location.origin);
                // Only intercept if target URL path is home ('/' or current welcome page)
                if (url.pathname === '/' || url.pathname === window.location.pathname) {
                    const hashPage = url.hash.replace(/^#\/?/, '');
                    if (hashPage && pages[hashPage]) {
                        e.preventDefault();
                        window.location.hash = hashPage;
                        navigateTo(hashPage);
                    } else if (!url.hash && (anchor.id === 'nav-home' || anchor.classList.contains('logo'))) {
                        e.preventDefault();
                        if (window.location.hash) {
                            history.pushState(null, '', window.location.pathname);
                        }
                        navigateTo('home');
                    }
                }
            } catch (err) {
                // Ignore non-standard links
            }
        }
    });

    window.addEventListener('hashchange', () => {
        const targetPage = getPageFromHash() || 'home';
        navigateTo(targetPage);
    });

    // Initial page load SPA routing
    const initialPage = getPageFromHash() || 'home';
    navigateTo(initialPage);
}
setTimeout(initReveal, 200);

// ── Global Exports (for Blade onclick handlers) ────────────────────────────
window.triggerSearch = triggerSearch;
window.searchCategory = (q) => triggerSearch(q);
