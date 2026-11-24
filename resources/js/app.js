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
    closeMobileNav();

    Object.values(pages).forEach(p => { if (p) p.classList.remove('active'); });

    if (pages[pageId]) {
        pages[pageId].classList.add('active');
        currentPage = pageId;

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

        window.scrollTo({ top: 0, behavior: 'smooth' });

        if (pageId === 'centres') initCentresPage();
        if (pageId === 'results' && options.query) doSearch(options.query);

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

/* ── API Search & Results Handling ────────────────────────────────────────── */
const resultLoading  = document.getElementById('result-loading');
const resultSuccess  = document.getElementById('result-success');
const resultNotFound = document.getElementById('result-not-found');
const resultError    = document.getElementById('result-error');
const resultQueryLabel = document.getElementById('result-query-label');

let currentSearchResultsCentres = [];
let currentResultsAccessFilter = 'public_dropoff'; // Default ON for household searches
let currentSearchQuery = '';

function showResultState(state) {
    [resultLoading, resultSuccess, resultNotFound, resultError].forEach(el => {
        if (el) el.style.display = 'none';
    });
    const el = { loading: resultLoading, success: resultSuccess, notFound: resultNotFound, error: resultError }[state];
    if (el) el.style.display = '';
}

async function doSearch(query) {
    currentSearchQuery = query;
    if (resultQueryLabel) resultQueryLabel.textContent = `"${query}"`;

    showResultState('loading');

    const includeUnverified = document.getElementById('res-include-unverified')?.checked ? 1 : 0;

    try {
        const url = `${API_BASE}/waste/search?q=${encodeURIComponent(query)}&include_unverified=${includeUnverified}`;
        const resp = await fetch(url, {
            method: 'GET',
            headers: { 'Accept': 'application/json', 'Content-Type': 'application/json' },
            credentials: 'same-origin',
        });

        if (resp.status === 404) {
            const data = await resp.json().catch(() => ({}));
            showNotFound(data.message, data.suggestions, data.admin_note);
            return;
        }

        if (!resp.ok) {
            showError();
            return;
        }

        const data = await resp.json();

        if (!data.success || !data.data) {
            showNotFound(data.message, data.suggestions, data.admin_note);
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

function showNotFound(msg, suggestions, adminNote) {
    const msgEl = document.getElementById('not-found-msg');
    if (msgEl) {
        msgEl.textContent = msg || 'Sorry, we couldn\'t find this waste item. Try another name such as Old Laptop, Old Mobile, Plastic Bottle, or Newspaper.';
    }

    const suggestionsEl = document.getElementById('not-found-suggestions');
    if (suggestionsEl) {
        if (suggestions && suggestions.length > 0) {
            let html = '<p style="font-size:0.88rem;color:var(--text-muted);margin-bottom:0.75rem;font-weight:600">💡 Suggested items in catalog:</p><div class="chips" style="justify-content:center;margin-bottom:1.5rem">';
            suggestions.forEach(item => {
                html += `<button class="chip" onclick="triggerSearch('${item.name}')">🔍 ${item.name}</button>`;
            });
            html += '</div>';
            suggestionsEl.innerHTML = html;
        } else {
            suggestionsEl.innerHTML = '';
        }
    }

    const adminNoteEl = document.getElementById('not-found-admin-note');
    if (adminNoteEl) {
        adminNoteEl.textContent = adminNote || '';
    }

    showResultState('notFound');
}

function showError() {
    showResultState('error');
}

function renderResult(data) {
    setText('res-item-name', data.name);
    setText('res-item-desc', data.description);

    // Safety Banner Handling (e.g. Lithium-Ion warning)
    const safetyBanner = document.getElementById('res-safety-banner');
    const safetyText = document.getElementById('res-safety-warning-text');
    if (safetyBanner && safetyText) {
        if (data.safety_warning) {
            safetyText.textContent = data.safety_warning;
            safetyBanner.style.display = 'block';
        } else {
            safetyBanner.style.display = 'none';
        }
    }

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
    currentSearchResultsCentres = data.collection_centres ?? [];
    populateResultFilters(currentSearchResultsCentres);
    applyResultCentresFilter();
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

/* ── Result Centres Cascading Filter & Rendering ─────────────────────────── */
function populateResultFilters(centres) {
    const districtSelect = document.getElementById('res-filter-district');
    const citySelect = document.getElementById('res-filter-city');
    if (!districtSelect || !citySelect) return;

    const districts = new Set();
    const cities = new Set();

    centres.forEach(c => {
        if (c.district) districts.add(c.district);
        if (c.city) cities.add(c.city);
    });

    districtSelect.innerHTML = '<option value="">All Districts</option>' +
        Array.from(districts).sort().map(d => `<option value="${escHtml(d)}">${escHtml(d)}</option>`).join('');

    citySelect.innerHTML = '<option value="">All Cities</option>' +
        Array.from(cities).sort().map(ct => `<option value="${escHtml(ct)}">${escHtml(ct)}</option>`).join('');
}

function applyResultCentresFilter() {
    const district = document.getElementById('res-filter-district')?.value ?? '';
    const city = document.getElementById('res-filter-city')?.value ?? '';

    let filtered = currentSearchResultsCentres.filter(c => {
        // District filter
        if (district && c.district !== district) return false;
        // City filter
        if (city && c.city !== city) return false;

        // Access filter chip
        if (currentResultsAccessFilter === 'public_dropoff') {
            const isPublic = (c.public_access === 'public_dropoff' || c.facility_type === 'public_dropoff' || c.facility_type === 'municipal_centre');
            if (!isPublic) return false;
        } else if (currentResultsAccessFilter === 'recycler_pickup') {
            const isRecyclerOrPickup = (c.public_access === 'pickup_only' || c.facility_type === 'authorized_recycler' || c.facility_type === 'pickup_provider' || c.facility_type === 'processing_facility');
            if (!isRecyclerOrPickup) return false;
        }
        return true;
    });

    renderCentres(filtered);
}

// Result Filter Chip Event Handlers
document.querySelectorAll('.access-chip').forEach(chip => {
    chip.addEventListener('click', () => {
        document.querySelectorAll('.access-chip').forEach(c => c.classList.remove('active'));
        chip.classList.add('active');
        currentResultsAccessFilter = chip.dataset.accessFilter;
        applyResultCentresFilter();
    });
});

document.getElementById('res-filter-district')?.addEventListener('change', () => {
    const selDist = document.getElementById('res-filter-district').value;
    const citySelect = document.getElementById('res-filter-city');
    if (citySelect) {
        const matchingCities = new Set();
        currentSearchResultsCentres.forEach(c => {
            if (!selDist || c.district === selDist) {
                if (c.city) matchingCities.add(c.city);
            }
        });
        citySelect.innerHTML = '<option value="">All Cities</option>' +
            Array.from(matchingCities).sort().map(ct => `<option value="${escHtml(ct)}">${escHtml(ct)}</option>`).join('');
    }
    applyResultCentresFilter();
});

document.getElementById('res-filter-city')?.addEventListener('change', applyResultCentresFilter);

document.getElementById('res-include-unverified')?.addEventListener('change', () => {
    if (currentSearchQuery) {
        doSearch(currentSearchQuery);
    }
});

/* ── Card Building & Access Badges Helper ──────────────────────────────────── */
function buildCentreCardHtml(c, isSearchCard = false) {
    const lat = (c.latitude !== undefined && c.latitude !== null) ? c.latitude : c.lat;
    const lng = (c.longitude !== undefined && c.longitude !== null) ? c.longitude : c.lng;
    const hasCoords = (lat !== null && lat !== undefined && lng !== null && lng !== undefined && !isNaN(parseFloat(lat)) && !isNaN(parseFloat(lng)));

    // Access Badge Logic
    let accessBadgeHtml = '';
    const pubAccess = c.public_access || 'public_dropoff';
    const facType = c.facility_type || 'public_dropoff';

    if (pubAccess === 'public_dropoff' || facType === 'public_dropoff') {
        accessBadgeHtml = `<span class="access-badge access-badge-green">🌿 Public Drop-Off</span>`;
    } else if (pubAccess === 'pickup_only' || facType === 'pickup_provider') {
        accessBadgeHtml = `<span class="access-badge access-badge-amber">📞 Pickup / Call First</span>`;
    } else if (pubAccess === 'business_industrial_only' || facType === 'industrial_facility') {
        accessBadgeHtml = `<span class="access-badge access-badge-red">🏭 Business / Industrial Only</span>`;
    } else {
        accessBadgeHtml = `<span class="access-badge access-badge-amber">ℹ️ ${escHtml(pubAccess.replace(/_/g, ' '))}</span>`;
    }

    // Verification Badge Logic
    let verifyBadgeHtml = '';
    const authStatus = c.authorization_status || 'verified';
    const verifyDate = c.verification_date || c.authorization_valid_until;
    const dateText = verifyDate ? ` (${verifyDate.substring(0, 7)})` : '';

    if (authStatus === 'verified') {
        verifyBadgeHtml = `<span class="verify-badge verify-badge-green">✓ GPCB / Municipal Verified${dateText}</span>`;
    } else if (authStatus === 'needs_confirmation' || c.status === 'pending_verification') {
        verifyBadgeHtml = `<span class="verify-badge verify-badge-amber">⏳ Pending Review</span>`;
    } else if (authStatus === 'expired') {
        verifyBadgeHtml = `<span class="verify-badge verify-badge-red">⚠️ Authorization Expired</span>`;
    }

    // Distance Badge (if nearest active)
    const distBadge = (c.distance !== null && c.distance !== undefined)
        ? `<div class="centre-distance-badge">📏 ${c.distance < 1 ? (c.distance * 1000).toFixed(0) + ' m away' : c.distance.toFixed(1) + ' km away'}</div>`
        : '';

    // Directions & Call Logic
    let actionsHtml = '';
    if (hasCoords) {
        const mapsUrl = (userLocation && userLocation.lat && userLocation.lng)
            ? `https://www.google.com/maps/dir/?api=1&origin=${userLocation.lat},${userLocation.lng}&destination=${lat},${lng}`
            : `https://www.google.com/maps/dir/?api=1&destination=${lat},${lng}`;

        actionsHtml = `
            <a href="${mapsUrl}" target="_blank" rel="noopener noreferrer" class="directions-btn">
                🧭 Get Directions
            </a>
        `;
    } else {
        const fullAddress = `${c.name || ''}, ${c.address || ''}, ${c.city || ''}, Gujarat`;
        const searchUrl = `https://www.google.com/maps/search/?api=1&query=${encodeURIComponent(fullAddress)}`;
        const callBtn = c.phone
            ? `<a href="tel:${c.phone}" class="call-btn">📞 Call Before Visiting</a>`
            : '';

        actionsHtml = `
            <div style="display:flex;gap:.5rem;flex-wrap:wrap;width:100%;align-items:center;">
                <a href="${searchUrl}" target="_blank" rel="noopener noreferrer" class="search-map-btn" style="flex:1;">
                    🔍 Search Address on Map
                </a>
                ${callBtn}
            </div>
        `;
    }

    const cardClass = isSearchCard ? 'centre-card reveal' : 'centre-list-card reveal';

    return `
        <div class="${cardClass}" data-centre-id="${c.id || ''}">
            <div style="display:flex;justify-content:space-between;align-items:flex-start;gap:.5rem;margin-bottom:.5rem;">
                <div style="display:flex;flex-wrap:wrap;gap:.35rem;">
                    ${accessBadgeHtml}
                    ${verifyBadgeHtml}
                </div>
                <button type="button" class="btn-report-issue" onclick="openReportModal(${c.id || 'null'}, '${escHtml(c.name || '')}')" title="Report issue with this centre" style="background:transparent;border:none;cursor:pointer;font-size:.85rem;color:var(--text-light);padding:.2rem;">
                    🚩 Report
                </button>
            </div>

            <div class="centre-name" style="font-size:1.05rem;font-weight:700;color:var(--text-dark);margin-bottom:.3rem;">
                ♻️ ${escHtml(c.name)}
            </div>

            ${c.city ? `<div class="centre-city" style="font-size:.85rem;color:var(--text-muted);margin-bottom:.4rem;">📍 ${escHtml(c.city)}${c.district ? ` (${escHtml(c.district)} Dist.)` : ''}</div>` : ''}
            ${distBadge}

            ${c.address ? `<div class="centre-detail"><span class="centre-detail-icon">🏢</span><span>${escHtml(c.address)}</span></div>` : ''}
            ${c.phone ? `<div class="centre-detail"><span class="centre-detail-icon">📞</span><span><a href="tel:${c.phone}" style="color:var(--primary-dark);font-weight:600;">${escHtml(c.phone)}</a></span></div>` : ''}
            ${c.facility_type ? `<div class="centre-detail"><span class="centre-detail-icon">🏷️</span><span>Type: ${escHtml(c.facility_type.replace(/_/g, ' '))}</span></div>` : ''}
            ${hasCoords ? `<div class="centre-detail" style="font-size:.8rem;color:var(--text-light);"><span class="centre-detail-icon">🗺️</span><span>GPS: ${lat}, ${lng}</span></div>` : ''}

            <div style="margin-top:.85rem;">
                ${actionsHtml}
            </div>
        </div>
    `;
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

    container.innerHTML = centres.map(c => buildCentreCardHtml(c, true)).join('');
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

/* ── Collection Centres Directory Page ────────────────────────────────────── */
let centresData = (window.collectionCentresData && window.collectionCentresData.length > 0)
    ? window.collectionCentresData
    : [];

let userLocation = null;
let isNearestActive = false;
let currentDirAccessFilter = 'public_dropoff';

function calculateHaversineDistance(lat1, lon1, lat2, lon2) {
    if (lat1 === null || lon1 === null || lat2 === null || lon2 === null) return null;
    if (lat1 === undefined || lon1 === undefined || lat2 === undefined || lon2 === undefined) return null;

    const numLat1 = parseFloat(lat1);
    const numLon1 = parseFloat(lon1);
    const numLat2 = parseFloat(lat2);
    const numLon2 = parseFloat(lon2);

    if (isNaN(numLat1) || isNaN(numLon1) || isNaN(numLat2) || isNaN(numLon2)) return null;

    const R = 6371; // Earth radius in km
    const dLat = (numLat2 - numLat1) * (Math.PI / 180);
    const dLon = (numLon2 - numLon1) * (Math.PI / 180);
    const a =
        Math.sin(dLat / 2) * Math.sin(dLat / 2) +
        Math.cos(numLat1 * (Math.PI / 180)) * Math.cos(numLat2 * (Math.PI / 180)) *
        Math.sin(dLon / 2) * Math.sin(dLon / 2);
    const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
    return R * c;
}

function initCentresPage() {
    if (window.collectionCentresData && Array.isArray(window.collectionCentresData) && window.collectionCentresData.length > 0) {
        centresData = window.collectionCentresData;
    }

    populateDirectoryDropdowns();

    const filterInput = document.getElementById('centres-filter');
    const catFilter   = document.getElementById('centres-cat-filter');
    const distFilter  = document.getElementById('centres-district-filter');
    const cityFilter  = document.getElementById('centres-city-filter');
    const unverifiedToggle = document.getElementById('dir-include-unverified');
    const btnNearest  = document.getElementById('btn-find-nearest');
    const btnShowAll  = document.getElementById('btn-show-all');

    filterCentres();

    filterInput?.addEventListener('input', filterCentres);
    catFilter?.addEventListener('change', filterCentres);
    unverifiedToggle?.addEventListener('change', filterCentres);

    distFilter?.addEventListener('change', () => {
        const selDist = distFilter.value;
        const matchingCities = new Set();
        centresData.forEach(c => {
            if (!selDist || c.district === selDist) {
                if (c.city) matchingCities.add(c.city);
            }
        });
        if (cityFilter) {
            cityFilter.innerHTML = '<option value="">All Cities</option>' +
                Array.from(matchingCities).sort().map(ct => `<option value="${escHtml(ct)}">${escHtml(ct)}</option>`).join('');
        }
        filterCentres();
    });

    cityFilter?.addEventListener('change', filterCentres);

    if (btnNearest) {
        btnNearest.removeEventListener('click', handleFindNearest);
        btnNearest.addEventListener('click', handleFindNearest);
    }

    if (btnShowAll) {
        btnShowAll.removeEventListener('click', handleShowAll);
        btnShowAll.addEventListener('click', handleShowAll);
    }

    document.querySelectorAll('.dir-access-chip').forEach(chip => {
        chip.addEventListener('click', () => {
            document.querySelectorAll('.dir-access-chip').forEach(c => c.classList.remove('active'));
            chip.classList.add('active');
            currentDirAccessFilter = chip.dataset.dirAccess;
            filterCentres();
        });
    });
}

function populateDirectoryDropdowns() {
    const distFilter = document.getElementById('centres-district-filter');
    const cityFilter = document.getElementById('centres-city-filter');
    if (!distFilter || !cityFilter) return;

    const districts = new Set();
    const cities = new Set();

    centresData.forEach(c => {
        if (c.district) districts.add(c.district);
        if (c.city) cities.add(c.city);
    });

    distFilter.innerHTML = '<option value="">All Districts</option>' +
        Array.from(districts).sort().map(d => `<option value="${escHtml(d)}">${escHtml(d)}</option>`).join('');

    cityFilter.innerHTML = '<option value="">All Cities</option>' +
        Array.from(cities).sort().map(ct => `<option value="${escHtml(ct)}">${escHtml(ct)}</option>`).join('');
}

function handleFindNearest() {
    const btnNearest = document.getElementById('btn-find-nearest');
    const btnShowAll = document.getElementById('btn-show-all');
    const fallbackPrompt = document.getElementById('geo-fallback-prompt');

    if (!navigator.geolocation) {
        showNearestStatus("Your browser does not support location services.", "error");
        if (fallbackPrompt) fallbackPrompt.style.display = 'flex';
        return;
    }

    if (btnNearest) {
        btnNearest.disabled = true;
        btnNearest.innerHTML = '📍 Locating...';
    }

    navigator.geolocation.getCurrentPosition(
        position => {
            if (btnNearest) {
                btnNearest.disabled = false;
                btnNearest.innerHTML = '📍 Find Nearest Centre';
            }

            userLocation = {
                lat: position.coords.latitude,
                lng: position.coords.longitude
            };
            isNearestActive = true;

            showNearestStatus("📍 Showing centres nearest to you", "success");
            if (btnShowAll) btnShowAll.style.display = 'inline-flex';
            if (fallbackPrompt) fallbackPrompt.style.display = 'none';

            filterCentres();
        },
        error => {
            if (btnNearest) {
                btnNearest.disabled = false;
                btnNearest.innerHTML = '📍 Find Nearest Centre';
            }

            // Non-blocking fallback prompt letting users manually pick District/City
            if (fallbackPrompt) fallbackPrompt.style.display = 'flex';

            switch (error.code) {
                case error.PERMISSION_DENIED:
                    showNearestStatus("📍 Location access was denied. Please pick your District or City above.", "error");
                    break;
                case error.POSITION_UNAVAILABLE:
                    showNearestStatus("Unable to determine your location. Please pick your District or City above.", "error");
                    break;
                case error.TIMEOUT:
                    showNearestStatus("Location request timed out (8s). Select your District or City manually above.", "error");
                    break;
                default:
                    showNearestStatus("Location unavailable. Please pick your District or City above.", "error");
                    break;
            }
        },
        {
            enableHighAccuracy: true,
            timeout: 8000, // 8-second timeout requirement
            maximumAge: 0
        }
    );
}

function handleShowAll() {
    userLocation = null;
    isNearestActive = false;

    const btnShowAll = document.getElementById('btn-show-all');
    if (btnShowAll) btnShowAll.style.display = 'none';

    hideNearestStatus();
    filterCentres();
}

function showNearestStatus(msg, type = 'info') {
    const statusEl = document.getElementById('nearest-status');
    if (!statusEl) return;
    statusEl.textContent = msg;
    statusEl.className = `nearest-status ${type}`;
    statusEl.style.display = 'flex';
}

function hideNearestStatus() {
    const statusEl = document.getElementById('nearest-status');
    if (statusEl) {
        statusEl.style.display = 'none';
        statusEl.textContent = '';
    }
}

function filterCentres() {
    const term = document.getElementById('centres-filter')?.value.toLowerCase() ?? '';
    const cat  = document.getElementById('centres-cat-filter')?.value ?? '';
    const dist = document.getElementById('centres-district-filter')?.value ?? '';
    const city = document.getElementById('centres-city-filter')?.value ?? '';
    const includeUnverified = document.getElementById('dir-include-unverified')?.checked ?? false;

    let filtered = centresData.filter(c => {
        // Verification filter (hide pending_verification or expired unless toggle checked)
        if (!includeUnverified) {
            if (c.status === 'pending_verification') return false;
            if (c.authorization_status === 'expired') return false;
        }

        // District & City
        if (dist && c.district !== dist) return false;
        if (city && c.city !== city) return false;

        // Access filter chip
        if (currentDirAccessFilter === 'public_dropoff') {
            const isPublic = (c.public_access === 'public_dropoff' || c.facility_type === 'public_dropoff' || c.facility_type === 'municipal_centre');
            if (!isPublic) return false;
        } else if (currentDirAccessFilter === 'recycler_pickup') {
            const isRecyclerOrPickup = (c.public_access === 'pickup_only' || c.facility_type === 'authorized_recycler' || c.facility_type === 'pickup_provider' || c.facility_type === 'processing_facility');
            if (!isRecyclerOrPickup) return false;
        }

        // Search text term
        const matchTerm = !term ||
            (c.name && c.name.toLowerCase().includes(term)) ||
            (c.city && c.city.toLowerCase().includes(term)) ||
            (c.district && c.district.toLowerCase().includes(term)) ||
            (c.address && c.address.toLowerCase().includes(term));

        // Category filter
        const cCats = c.categories || (c.category ? [c.category] : []);
        const matchCat = !cat || cCats.includes(cat) || c.category === cat;

        return matchTerm && matchCat;
    });

    filtered = filtered.map(c => {
        const cLat = (c.latitude !== undefined && c.latitude !== null) ? c.latitude : c.lat;
        const cLng = (c.longitude !== undefined && c.longitude !== null) ? c.longitude : c.lng;
        const distKm = (isNearestActive && userLocation)
            ? calculateHaversineDistance(userLocation.lat, userLocation.lng, cLat, cLng)
            : null;
        return { ...c, distance: distKm };
    });

    if (isNearestActive && userLocation) {
        filtered.sort((a, b) => {
            if (a.distance !== null && b.distance !== null) {
                return a.distance - b.distance;
            }
            if (a.distance !== null) return -1;
            if (b.distance !== null) return 1;
            return (a.name || '').localeCompare(b.name || '');
        });
    }

    renderCentresList(filtered);
}

function renderCentresList(data) {
    const container = document.getElementById('centres-list-grid');
    if (!container) return;

    if (!data || data.length === 0) {
        container.innerHTML = `<div class="no-centres" style="grid-column:1/-1">
            <p>🔍 No collection centres match your search or filter criteria.</p>
        </div>`;
        return;
    }

    container.innerHTML = data.map(c => buildCentreCardHtml(c, false)).join('');
    initReveal();
}

/* ── "Report an Issue with This Centre" Modal Logic ───────────────────────── */
window.openReportModal = function(centreId, centreName) {
    const modal = document.getElementById('report-centre-modal');
    const nameEl = document.getElementById('report-centre-name');
    const idInput = document.getElementById('report-centre-id');

    if (!modal) return;

    if (idInput) idInput.value = centreId || '';
    if (nameEl) nameEl.textContent = centreName ? `Centre: ${centreName}` : '';

    modal.style.display = 'flex';
};

function closeReportModal() {
    const modal = document.getElementById('report-centre-modal');
    if (modal) modal.style.display = 'none';
    const form = document.getElementById('report-centre-form');
    if (form) form.reset();
}

document.getElementById('close-report-modal')?.addEventListener('click', closeReportModal);
document.getElementById('cancel-report-btn')?.addEventListener('click', closeReportModal);

document.getElementById('report-centre-form')?.addEventListener('submit', async (e) => {
    e.preventDefault();
    const centreId = document.getElementById('report-centre-id')?.value;
    if (!centreId) {
        showToast('Invalid collection centre specified.');
        return;
    }

    const submitBtn = document.getElementById('submit-report-btn');
    if (submitBtn) {
        submitBtn.disabled = true;
        submitBtn.textContent = 'Submitting...';
    }

    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    const payload = {
        issue_type: document.getElementById('report-issue-type')?.value,
        message: document.getElementById('report-message')?.value,
        name: document.getElementById('report-name')?.value,
        email: document.getElementById('report-email')?.value,
    };

    try {
        const resp = await fetch(`/collection-centres/${centreId}/report`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken || '',
            },
            body: JSON.stringify(payload),
        });

        const data = await resp.json().catch(() => ({}));

        if (resp.ok) {
            closeReportModal();
            showToast(data.message || 'Thank you! Your issue report has been submitted to the admin team for review.');
        } else {
            showToast(data.message || 'Error submitting report. Please try again.');
        }
    } catch (err) {
        console.error('Report submission error:', err);
        showToast('Network error submitting report. Please try again.');
    } finally {
        if (submitBtn) {
            submitBtn.disabled = false;
            submitBtn.textContent = 'Submit Report';
        }
    }
});

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
function showToast(msg, duration = 3500) {
    const toast = document.getElementById('toast');
    if (!toast) return;
    toast.textContent = msg;
    toast.classList.add('show');
    setTimeout(() => toast.classList.remove('show'), duration);
}

window.showToast = showToast;

/* ── Navigation event delegation & SPA Init ────────────────────────────────── */
if (document.getElementById('page-home')) {
    function getPageFromHash() {
        const rawHash = window.location.hash.replace(/^#\/?/, '');
        const validPages = ['home', 'how', 'centres', 'about', 'results'];
        return validPages.includes(rawHash) ? rawHash : null;
    }

    document.addEventListener('click', e => {
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

        const anchor = e.target.closest('a');
        if (anchor && anchor.href) {
            try {
                const url = new URL(anchor.href, window.location.origin);
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

    const urlParams = new URLSearchParams(window.location.search);
    const initialQuery = urlParams.get('q') || urlParams.get('search');
    if (initialQuery) {
        triggerSearch(initialQuery);
    } else {
        const initialPage = getPageFromHash() || 'home';
        navigateTo(initialPage);
    }
}
setTimeout(initReveal, 200);

window.triggerSearch = triggerSearch;
window.searchCategory = (q) => triggerSearch(q);
