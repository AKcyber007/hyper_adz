@extends('layouts.app', [
    'title' => 'Ad Space Coimbatore | Indoor Advertising Screen Locations | Hyper Adz Network',
    'description' => 'Explore the Hyper Adz indoor advertising network — premium ad screen locations in restaurants, gyms, salons, medical stores and more across Coimbatore. Book ad space today.'
])

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
<style>
    /* Full height map layout */
    .network-container {
        position: relative;
        width: 100%;
        height: calc(100vh - 160px);
        min-height: 550px;
        background: #f1f5f9;
        overflow: hidden;
        border: none;
    }
    
    .network-map-canvas {
        width: 100%;
        height: 100%;
        z-index: 1;
    }

    /* Floating Location Cards List Overlay */
    .floating-panel {
        position: absolute;
        top: 20px;
        left: 20px;
        width: 380px;
        height: calc(100% - 40px);
        background: rgba(255, 255, 255, 0.96);
        backdrop-filter: blur(12px);
        border: 1px solid rgba(226, 232, 240, 0.9);
        border-radius: 24px;
        box-shadow: 0 10px 25px rgba(15, 23, 42, 0.08);
        z-index: 10;
        display: flex;
        flex-direction: column;
        padding: 20px;
        transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
    }

    /* Floating Search Bar */
    .map-search-bar {
        position: absolute;
        bottom: 20px;
        left: calc(50% + 190px); /* adjusted center offset due to left panel width */
        transform: translateX(-50%);
        width: 380px;
        background: #ffffff;
        border: 1px solid rgba(226, 232, 240, 0.9);
        border-radius: 99px;
        box-shadow: 0 10px 25px rgba(15, 23, 42, 0.08);
        z-index: 10;
        padding: 8px 18px;
        display: flex;
        align-items: center;
        gap: 10px;
        transition: all 0.3s ease;
    }
    .map-search-bar input {
        border: none;
        outline: none;
        width: 100%;
        font-size: 0.85rem;
        color: #334155;
    }
    .map-search-bar i {
        color: #94a3b8;
        font-size: 1rem;
    }

    /* Location Cards list container */
    .cards-container {
        flex-grow: 1;
        overflow-y: auto;
        margin-top: 15px;
        padding-right: 4px;
    }
    .cards-container::-webkit-scrollbar {
        width: 4px;
    }
    .cards-container::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 99px;
    }

    /* Location Card Details */
    .location-card {
        position: relative;
        background: #ffffff;
        border: 1px solid #f1f5f9;
        border-radius: 20px;
        padding: 12px;
        display: flex;
        gap: 12px;
        cursor: pointer;
        transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
        box-shadow: 0 2px 4px rgba(15, 23, 42, 0.01);
    }
    .location-card:hover {
        transform: translateY(-2px);
        border-color: rgba(79, 70, 229, 0.2);
        box-shadow: 0 10px 15px -3px rgba(15, 23, 42, 0.04);
    }
    .location-card.highlighted {
        border-color: #4f46e5 !important;
        box-shadow: 0 10px 20px -3px rgba(79, 70, 229, 0.08);
        background: #f8fafc;
    }

    .location-card-image {
        width: 84px;
        height: 74px;
        border-radius: 14px;
        overflow: hidden;
        background: #f1f5f9;
        flex-shrink: 0;
    }
    .location-card-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .location-card-content {
        flex-grow: 1;
        min-width: 0;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }

    .status-dot {
        position: absolute;
        top: 12px;
        right: 12px;
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background: #10b981;
        border: 1px solid #ffffff;
    }

    /* Details Sliding Drawer */
    .details-drawer {
        position: absolute;
        top: 0;
        right: 0;
        width: 420px;
        height: 100%;
        background: #ffffff;
        box-shadow: -15px 0 45px rgba(15, 23, 42, 0.08);
        z-index: 20;
        transform: translateX(100%);
        transition: transform 0.35s cubic-bezier(0.16, 1, 0.3, 1);
        display: flex;
        flex-direction: column;
        border-left: 1px solid #f1f5f9;
    }
    .details-drawer.open {
        transform: translateX(0);
    }

    .drawer-header {
        position: relative;
        padding: 20px;
        border-bottom: 1px solid #f1f5f9;
    }
    .drawer-close-btn {
        position: absolute;
        top: 20px;
        right: 20px;
        border: none;
        background: #f1f5f9;
        color: #64748b;
        width: 32px;
        height: 32px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.2s;
    }
    .drawer-close-btn:hover {
        background: #e2e8f0;
        color: #0f172a;
    }

    .drawer-body {
        flex-grow: 1;
        overflow-y: auto;
        padding: 20px;
        space-y: 20px;
    }
    .drawer-body::-webkit-scrollbar {
        width: 4px;
    }
    .drawer-body::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 99px;
    }

    .drawer-image-wrapper {
        width: 100%;
        height: 180px;
        border-radius: 20px;
        overflow: hidden;
        background: #f1f5f9;
        margin-bottom: 20px;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
    }
    .drawer-image-wrapper img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .drawer-meta-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 12px;
        margin: 20px 0;
    }
    .drawer-meta-item {
        background: #f8fafc;
        border: 1px solid #f1f5f9;
        border-radius: 16px;
        padding: 10px;
        text-align: center;
    }

    .drawer-footer {
        padding: 20px;
        border-top: 1px solid #f1f5f9;
        background: #ffffff;
    }

    /* Coimbatore Map Pins styling */
    .ha-marker-container {
        position: relative;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .ha-map-pin {
        width: 32px;
        height: 32px;
        background: #4f46e5;
        border-radius: 50% 50% 50% 0;
        transform: rotate(-45deg);
        display: flex;
        align-items: center;
        justify-content: center;
        border: 2px solid #ffffff;
        box-shadow: 0 4px 12px rgba(79, 70, 229, 0.35);
        transition: all 0.25s ease;
    }
    .ha-map-pin i {
        transform: rotate(45deg);
        color: #ffffff;
        font-size: 0.9rem;
    }
    .ha-map-pin:hover {
        transform: rotate(-45deg) scale(1.1);
        background: #4338ca;
    }

    /* Client-side pagination buttons */
    .page-nav-btn {
        width: 30px;
        height: 30px;
        border-radius: 8px;
        border: 1px solid #e2e8f0;
        background: #ffffff;
        color: #475569;
        font-size: 0.8rem;
        font-weight: 700;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.2s;
    }
    .page-nav-btn:hover {
        border-color: #cbd5e1;
        background: #f8fafc;
    }
    .page-nav-btn.active {
        background: #4f46e5;
        border-color: #4f46e5;
        color: #ffffff;
    }

    @media (max-width: 991px) {
        .network-container {
            height: auto;
            display: flex;
            flex-direction: column;
        }
        .floating-panel {
            position: relative;
            top: 0;
            left: 0;
            width: 100%;
            height: 480px;
            border-radius: 24px 24px 0 0;
            border: none;
            box-shadow: none;
        }
        .network-map-canvas {
            height: 350px;
            border-radius: 0 0 24px 24px;
        }
        .map-search-bar {
            position: relative;
            bottom: 0;
            left: 0;
            transform: none;
            width: 100%;
            margin: 10px 0;
            box-shadow: none;
        }
        .details-drawer {
            position: fixed;
            z-index: 1000;
            width: 100%;
        }
    }
</style>
@endpush

@section('content')
<div class="subpage-banner" style="background-image: url('{{ asset('images/network-banner.png') }}'); height: 80px; padding: 0; display: flex; align-items: center; justify-content: center;">
    <div class="subpage-banner-overlay"></div>
    <div class="container text-center text-white position-relative" style="z-index: 5;">
        <h1 class="text-white fw-bold m-0" style="font-size: 1.5rem; letter-spacing: -0.02em;">Advertising Network</h1>
    </div>
</div>

<div class="bg-grid-pattern">
    <div class="gradient-blob gradient-blob-1"></div>
    <div class="gradient-blob gradient-blob-2"></div>

    <section class="network-section p-0">
        <div class="container-fluid p-0">
            <div class="network-container">
                <!-- Left Sidebar: Location Panel -->
                <aside class="floating-panel">
                    <div class="d-flex align-items-center justify-between border-b border-slate-100 pb-3 mb-3">
                        <div>
                            <span class="text-xxs font-bold text-indigo-650 uppercase tracking-widest">Coimbatore</span>
                            <h4 class="fw-bold mb-0 text-slate-900" style="font-family: 'Sora', sans-serif; font-size: 1.15rem;">Found <span id="locations-count">0</span> space</h4>
                        </div>
                        <button class="btn btn-sm btn-light border py-1.5 px-3 rounded-xl text-xxs font-bold flex items-center gap-1.5 text-slate-700" onclick="document.getElementById('filters-collapse').classList.toggle('d-none')">
                            <i class="bi bi-sliders"></i> Filters
                        </button>
                    </div>

                    <!-- Collapsible Filters Box -->
                    <div id="filters-collapse" class="d-none bg-slate-50 border border-slate-100 rounded-2xl p-3 mb-3">
                        <div class="row g-2">
                            <div class="col-6">
                                <label class="text-[9px] font-bold text-slate-500 uppercase tracking-wider mb-1 block">Category</label>
                                <select id="category-filter-select" class="form-select text-xxs p-2 border-slate-200" style="border-radius: 8px;">
                                    <option value="">All Categories</option>
                                    @foreach($categories as $cat)
                                        <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-6">
                                <label class="text-[9px] font-bold text-slate-500 uppercase tracking-wider mb-1 block">City</label>
                                <select id="city-filter-select" class="form-select text-xxs p-2 border-slate-200" style="border-radius: 8px;">
                                    <option value="">All Cities</option>
                                    @foreach($cities as $city)
                                        <option value="{{ $city }}">{{ $city }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Scrollable Cards Box -->
                    <div class="cards-container space-y-2.5" id="location-cards-box">
                        <!-- Javascript will inject list cards here -->
                    </div>

                    <!-- Pagination Navigation -->
                    <div class="d-flex align-items-center justify-content-center gap-2 border-t border-slate-100 pt-3 mt-3" id="pagination-box">
                        <!-- Javascript will inject page dots here -->
                    </div>
                </aside>

                <!-- Floating Search Box on Map -->
                <div class="map-search-bar">
                    <i class="bi bi-search"></i>
                    <input type="text" placeholder="Search location, venue, category..." id="network-search-input">
                </div>

                <!-- Leaflet Map Canvas -->
                <div id="networkMap" class="network-map-canvas" aria-label="Hyper Adz network map"></div>

                <!-- Right Side details Drawer -->
                <div class="details-drawer" id="details-drawer">
                    <div class="drawer-header">
                        <button class="drawer-close-btn" id="drawer-close-btn">
                            <i class="bi bi-x-lg"></i>
                        </button>
                        <div class="pe-5">
                            <span class="text-[10px] font-bold text-indigo-650 uppercase tracking-widest" id="drawer-category">Category</span>
                            <h2 class="fw-bold mb-1 mt-1 text-slate-900" style="font-family: 'Sora', sans-serif; font-size: 1.3rem;" id="drawer-name">Aryaas Park</h2>
                            <div class="d-flex align-items-center gap-2 mt-1">
                                <div class="text-amber-500 text-xs flex gap-0.5">
                                    <i class="bi bi-star-fill"></i>
                                    <i class="bi bi-star-fill"></i>
                                    <i class="bi bi-star-fill"></i>
                                    <i class="bi bi-star-fill"></i>
                                    <i class="bi bi-star-half"></i>
                                </div>
                                <span class="text-slate-500 font-semibold text-[10px]">4.6 (12 reviews)</span>
                            </div>
                        </div>
                    </div>

                    <div class="drawer-body">
                        <div class="drawer-image-wrapper">
                            <img src="" id="drawer-image" alt="Venue Cover">
                        </div>

                        <div class="space-y-4">
                            <div class="space-y-1">
                                <span class="block text-[10px] text-slate-400 font-bold uppercase tracking-wider">Location / Address</span>
                                <p class="text-slate-800 text-xs leading-relaxed" id="drawer-address">Address details</p>
                            </div>

                            <div class="space-y-1">
                                <span class="block text-[10px] text-slate-400 font-bold uppercase tracking-wider">Description</span>
                                <p class="text-slate-600 text-xs leading-relaxed" id="drawer-description">Description text details</p>
                            </div>

                            <div class="drawer-meta-grid">
                                <div class="drawer-meta-item">
                                    <span class="block text-[8px] text-slate-400 font-bold uppercase">Screen</span>
                                    <span class="block text-sm font-extrabold text-slate-800 mt-0.5" id="drawer-screen-details" style="font-size: 0.7rem; line-height: 1.2;">-</span>
                                </div>
                                <div class="drawer-meta-item">
                                    <span class="block text-[8px] text-slate-400 font-bold uppercase">Monthly Audience</span>
                                    <span class="block text-sm font-extrabold text-indigo-650 mt-0.5 font-mono" id="drawer-impressions">0</span>
                                </div>
                            </div>
                            <div class="flex gap-2" id="drawer-media-support">
                            </div>
                        </div>
                    </div>

                    <div class="drawer-footer">
                        <div class="flex items-center justify-between gap-4 mb-3.5">
                            <div>
                                <span class="block text-[10px] text-slate-400 font-bold uppercase">Base Price</span>
                                <span class="text-xl font-extrabold text-slate-900 font-mono" id="drawer-rate">₹0.00</span>
                                <span class="text-xxs text-slate-400">/ day</span>
                            </div>
                            <span class="text-xxs text-slate-500 bg-slate-100 px-3 py-1.5 rounded-full font-bold whitespace-nowrap" id="drawer-repeats">Daily repeats</span>
                        </div>
                        <a href="{{ route('contact', ['form' => 'advertiser']) }}" class="btn btn-primary w-100 py-3 rounded-xl font-bold flex items-center justify-center gap-2 shadow-lg shadow-indigo-600/10">
                            <i class="bi bi-megaphone"></i>
                            <span>START ADVERTISING</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Coimbatore Center Coords
        const coimbatoreCenter = [11.0168, 76.9558];
        const defaultZoom = 12;

        // Initialize Map
        const map = L.map('networkMap', {
            zoomControl: true,
            scrollWheelZoom: true
        }).setView(coimbatoreCenter, defaultZoom);

        // Load OSM Tiles
        L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright" target="_blank">OpenStreetMap</a> contributors'
        }).addTo(map);

        // Store reference dictionary to markers to sync interactions
        let markersDict = {};
        let allLocations = [];
        let currentPage = 1;
        const perPage = 4;

        // DOM Handles
        const searchInput = document.getElementById('network-search-input');
        const categorySelect = document.getElementById('category-filter-select');
        const citySelect = document.getElementById('city-filter-select');
        const cardsBox = document.getElementById('location-cards-box');
        const locationsCount = document.getElementById('locations-count');
        const paginationBox = document.getElementById('pagination-box');
        const detailsDrawer = document.getElementById('details-drawer');
        const drawerCloseBtn = document.getElementById('drawer-close-btn');

        // Close details drawer
        drawerCloseBtn.addEventListener('click', function() {
            detailsDrawer.classList.remove('open');
            document.querySelectorAll('.location-card').forEach(card => card.classList.remove('highlighted'));
        });

        // Function to Fetch and Draw pins/cards
        function fetchNetworkNodes() {
            const searchVal = searchInput.value;
            const catVal = categorySelect.value;
            const cityVal = citySelect.value;

            // AXIOS GET Request with Query Parameters
            axios.get('/api/network/locations', {
                params: {
                    search: searchVal,
                    category_id: catVal,
                    city: cityVal
                }
            })
            .then(response => {
                allLocations = response.data;
                currentPage = 1;
                
                // Clear previous markers from map
                Object.values(markersDict).forEach(marker => map.removeLayer(marker));
                markersDict = {};

                locationsCount.textContent = allLocations.length;

                // Create Map Markers
                allLocations.forEach(location => {
                    const pinHtml = `
                        <div class="ha-marker-container">
                            <div class="ha-map-pin">
                                <i class="bi ${location.category.icon ?? 'bi-display'}"></i>
                            </div>
                        </div>
                    `;

                    const customIcon = L.divIcon({
                        html: pinHtml,
                        className: 'custom-map-marker',
                        iconSize: [36, 36],
                        iconAnchor: [18, 36],
                        popupAnchor: [0, -36]
                    });

                    // Create & Add Marker
                    const marker = L.marker([location.latitude, location.longitude], { icon: customIcon })
                        .addTo(map);

                    markersDict[location.id] = marker;

                    // Click Marker Handler (Opens drawer and zooms)
                    marker.on('click', function() {
                        map.setView([location.latitude, location.longitude], 14, { animate: true });
                        showLocationDetails(location);
                        highlightSidebarCard(location.id, true);
                    });
                });

                renderCardsList();
            })
            .catch(error => {
                console.error('Error fetching network coordinates:', error);
            });
        }

        // Render card list based on pagination
        function renderCardsList() {
            cardsBox.innerHTML = '';
            paginationBox.innerHTML = '';

            if (allLocations.length === 0) {
                cardsBox.innerHTML = `
                    <div class="text-center py-5 text-muted">
                        <i class="bi bi-geo-alt text-3xl mb-2 d-block opacity-40"></i>
                        <span class="small">No advertising locations found.</span>
                    </div>
                `;
                return;
            }

            const startIndex = (currentPage - 1) * perPage;
            const pageLocations = allLocations.slice(startIndex, startIndex + perPage);

            pageLocations.forEach(location => {
                const rate = location.price_per_day || Math.max(50, Math.round(parseInt(location.daily_footfall.replace(/,/g, '')) / 100));

                const cardDiv = document.createElement('div');
                cardDiv.className = 'location-card';
                cardDiv.id = `card-${location.id}`;
                cardDiv.innerHTML = `
                    <div class="status-dot"></div>
                    <div class="location-card-image">
                        <img src="${location.primary_image}">
                    </div>
                    <div class="location-card-content">
                        <div>
                            <span class="text-[9px] font-bold text-slate-400 uppercase block tracking-wider">${location.category.name}</span>
                            <h6 class="fw-bold text-slate-800 mb-0.5 text-truncate" style="font-family: 'Sora', sans-serif; font-size: 0.88rem;">${location.name}</h6>
                            <span class="text-[10px] text-slate-500 block"><i class="bi bi-geo-alt"></i> ${location.city}</span>
                        </div>
                        <div class="mt-2 flex align-items-end justify-between">
                            <div>
                                <span class="text-xs font-extrabold text-slate-900 font-mono">₹${rate}</span>
                                <span class="text-[9px] text-slate-400">/ day</span>
                            </div>
                        </div>
                    </div>
                `;

                // Card Click Handler
                cardDiv.addEventListener('click', function() {
                    highlightSidebarCard(location.id, false);
                    map.setView([location.latitude, location.longitude], 14, { animate: true });
                    showLocationDetails(location);
                });

                cardsBox.appendChild(cardDiv);
            });

            // Render pagination dots/numbers
            const totalPages = Math.ceil(allLocations.length / perPage);
            if (totalPages > 1) {
                // Prev
                const prevBtn = document.createElement('button');
                prevBtn.className = 'page-nav-btn';
                prevBtn.innerHTML = '<i class="bi bi-chevron-left"></i>';
                prevBtn.disabled = currentPage === 1;
                prevBtn.addEventListener('click', () => {
                    if (currentPage > 1) {
                        currentPage--;
                        renderCardsList();
                    }
                });
                paginationBox.appendChild(prevBtn);

                // Numbers
                for (let i = 1; i <= totalPages; i++) {
                    const pageBtn = document.createElement('button');
                    pageBtn.className = `page-nav-btn ${currentPage === i ? 'active' : ''}`;
                    pageBtn.textContent = i;
                    pageBtn.addEventListener('click', () => {
                        currentPage = i;
                        renderCardsList();
                    });
                    paginationBox.appendChild(pageBtn);
                }

                // Next
                const nextBtn = document.createElement('button');
                nextBtn.className = 'page-nav-btn';
                nextBtn.innerHTML = '<i class="bi bi-chevron-right"></i>';
                nextBtn.disabled = currentPage === totalPages;
                nextBtn.addEventListener('click', () => {
                    if (currentPage < totalPages) {
                        currentPage++;
                        renderCardsList();
                    }
                });
                paginationBox.appendChild(nextBtn);
            }
        }

        // Populate and show the right side details drawer
        function showLocationDetails(location) {
            const rate = location.price_per_day || Math.max(50, Math.round(parseInt(location.daily_footfall.replace(/,/g, '')) / 100));

            document.getElementById('drawer-category').textContent = location.category.name;
            document.getElementById('drawer-name').textContent = location.name;
            document.getElementById('drawer-image').src = location.primary_image;
            document.getElementById('drawer-address').innerHTML = `<i class="bi bi-geo-alt text-indigo-650"></i> ${location.address}, ${location.city}, ${location.state || 'Tamil Nadu'}`;
            document.getElementById('drawer-description').textContent = location.description || 'Premium indoor advertising display screens mapped for dynamic target scheduling and visual content rollout.';
            document.getElementById('drawer-screen-details').innerHTML = `${location.screen_size ? location.screen_size + ' inches' : '-'} <br><span class="text-[9px] text-slate-500 font-normal">${location.screen_orientation ? location.screen_orientation + ' screen' : ''}</span>`;
            document.getElementById('drawer-impressions').textContent = location.audience_count || '0';
            document.getElementById('drawer-rate').textContent = `₹${rate}`;
            document.getElementById('drawer-repeats').textContent = `${location.repeats_per_day || 0} Ad repeats / day`;
            
            const mediaContainer = document.getElementById('drawer-media-support');
            mediaContainer.innerHTML = '';
            if(location.video_supported) {
                mediaContainer.innerHTML += `<span class="px-2 py-1 bg-blue-50 text-blue-600 rounded text-[9px] font-bold"><i class="bi bi-camera-video"></i> Video</span>`;
            }
            if(location.audio_supported) {
                mediaContainer.innerHTML += `<span class="px-2 py-1 bg-emerald-50 text-emerald-600 rounded text-[9px] font-bold"><i class="bi bi-volume-up"></i> Audio</span>`;
            }

            detailsDrawer.classList.add('open');
        }

        // Helper to Toggle active card highlights and scroll
        function highlightSidebarCard(id, scrollToCard) {
            // Remove previous highlights
            document.querySelectorAll('.location-card').forEach(card => card.classList.remove('highlighted'));

            const card = document.getElementById(`card-${id}`);
            if (card) {
                card.classList.add('highlighted');
                if (scrollToCard) {
                    card.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
                }
            }
        }

        // Listeners (AXIOS calls trigger immediately on typing/selection change)
        searchInput.addEventListener('input', debounce(fetchNetworkNodes, 300));
        categorySelect.addEventListener('change', fetchNetworkNodes);
        citySelect.addEventListener('change', fetchNetworkNodes);

        // Fetch initial set of nodes
        fetchNetworkNodes();

        // Debounce helper to prevent heavy typing refetches
        function debounce(func, delay) {
            let timeout;
            return function(...args) {
                clearTimeout(timeout);
                timeout = setTimeout(() => func.apply(this, args), delay);
            };
        }
    });
</script>
@endpush
