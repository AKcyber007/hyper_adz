@extends('layouts.advertiser')

@section('title', 'Locations / Networks')

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
<style>
    /* Full height map layout within dashboard */
    .network-container {
        position: relative;
        width: 100%;
        height: calc(100vh - 120px);
        min-height: 550px;
        background: #f1f5f9;
        overflow: hidden;
        border-radius: 24px;
        border: 1px solid #e2e8f0;
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
        width: 340px;
        height: calc(100% - 40px);
        background: rgba(255, 255, 255, 0.96);
        backdrop-filter: blur(12px);
        border: 1px solid rgba(226, 232, 240, 0.9);
        border-radius: 20px;
        box-shadow: 0 10px 25px rgba(15, 23, 42, 0.08);
        z-index: 10;
        display: flex;
        flex-direction: column;
        padding: 16px;
        transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
    }

    /* Floating Search Bar */
    .map-search-bar {
        position: absolute;
        top: 20px;
        left: 380px; /* adjusted for left panel width */
        width: 320px;
        background: #ffffff;
        border: 1px solid rgba(226, 232, 240, 0.9);
        border-radius: 99px;
        box-shadow: 0 4px 15px rgba(15, 23, 42, 0.05);
        z-index: 10;
        padding: 8px 18px;
        display: flex;
        align-items: center;
        gap: 10px;
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

    /* Selected Summary Bar */
    .selected-summary-bar {
        position: absolute;
        bottom: 20px;
        left: 380px;
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        box-shadow: 0 10px 25px rgba(15, 23, 42, 0.08);
        z-index: 10;
        padding: 12px 20px;
        display: flex;
        align-items: center;
        gap: 20px;
        opacity: 0;
        transform: translateY(20px);
        pointer-events: none;
        transition: all 0.3s ease;
    }
    .selected-summary-bar.show {
        opacity: 1;
        transform: translateY(0);
        pointer-events: auto;
    }

    /* Location Cards list container */
    .cards-container {
        flex-grow: 1;
        overflow-y: auto;
        margin-top: 15px;
        padding-right: 4px;
    }
    .cards-container::-webkit-scrollbar { width: 4px; }
    .cards-container::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 99px; }

    /* Location Card Details */
    .location-card {
        position: relative;
        background: #ffffff;
        border: 1px solid #f1f5f9;
        border-radius: 16px;
        padding: 12px;
        display: flex;
        gap: 12px;
        cursor: pointer;
        transition: all 0.25s ease;
        box-shadow: 0 2px 4px rgba(15, 23, 42, 0.01);
    }
    .location-card:hover {
        transform: translateY(-2px);
        border-color: rgba(79, 70, 229, 0.2);
    }
    .location-card.highlighted {
        border-color: #4f46e5 !important;
        box-shadow: 0 10px 20px -3px rgba(79, 70, 229, 0.08);
        background: #f8fafc;
    }

    .location-card-image {
        width: 70px;
        height: 60px;
        border-radius: 12px;
        overflow: hidden;
        background: #f1f5f9;
        flex-shrink: 0;
    }
    .location-card-image img { width: 100%; height: 100%; object-fit: cover; }
    .location-card-content { flex-grow: 1; min-width: 0; display: flex; flex-direction: column; justify-content: space-between; }

    /* Details Sliding Drawer */
    .details-drawer {
        position: absolute;
        top: 0;
        right: 0;
        width: 380px;
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
    .details-drawer.open { transform: translateX(0); }

    .drawer-header { position: relative; padding: 20px; border-bottom: 1px solid #f1f5f9; }
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
    .drawer-close-btn:hover { background: #e2e8f0; color: #0f172a; }

    .drawer-body { flex-grow: 1; overflow-y: auto; padding: 20px; space-y: 20px; }
    .drawer-body::-webkit-scrollbar { width: 4px; }
    .drawer-body::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 99px; }

    .drawer-image-wrapper { width: 100%; height: 160px; border-radius: 16px; overflow: hidden; background: #f1f5f9; margin-bottom: 20px; }
    .drawer-image-wrapper img { width: 100%; height: 100%; object-fit: cover; }

    .drawer-meta-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 12px; margin: 20px 0; }
    .drawer-meta-item { background: #f8fafc; border: 1px solid #f1f5f9; border-radius: 12px; padding: 10px; text-align: center; }

    .drawer-footer { padding: 20px; border-top: 1px solid #f1f5f9; background: #ffffff; }

    /* Map Pins */
    .ha-marker-container { position: relative; display: flex; align-items: center; justify-content: center; }
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
    .ha-map-pin i { transform: rotate(45deg); color: #ffffff; font-size: 0.9rem; }
    .ha-map-pin:hover { transform: rotate(-45deg) scale(1.1); background: #4338ca; }
    .ha-map-pin.selected { background: #10b981; box-shadow: 0 4px 12px rgba(16, 185, 129, 0.35); }

    /* Reviews Section */
    .review-item { border-bottom: 1px solid #f1f5f9; padding: 12px 0; }
    .review-item:last-child { border-bottom: none; }
    .star-selector { display: flex; flex-direction: row-reverse; justify-content: flex-end; gap: 4px; }
    .star-selector input { display: none; }
    .star-selector label { cursor: pointer; color: #cbd5e1; font-size: 1.25rem; transition: color 0.2s; }
    .star-selector label:hover, .star-selector label:hover ~ label, .star-selector input:checked ~ label { color: #f59e0b; }
</style>
@endpush

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-slate-900 font-outfit">Locations & Networks</h1>
            <p class="text-xs text-slate-550 mt-1">Discover advertising screens and add them to your campaign booking.</p>
        </div>
    </div>

    <div class="network-container">
        <!-- Left Sidebar: Location Panel -->
        <aside class="floating-panel">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3 mb-3">
                <div>
                    <span class="text-[9px] font-bold text-indigo-600 uppercase tracking-widest">Coimbatore</span>
                    <h4 class="font-bold text-slate-900 text-sm mt-0.5">Found <span id="locations-count">0</span> slots</h4>
                </div>
                <button class="px-3 py-1.5 bg-slate-100 hover:bg-slate-200 rounded-lg text-[10px] font-bold text-slate-700 flex items-center gap-1.5 transition-all" onclick="document.getElementById('filters-collapse').classList.toggle('hidden')">
                    <i class="bi bi-sliders"></i> Filters
                </button>
            </div>

            <!-- Collapsible Filters Box -->
            <div id="filters-collapse" class="hidden bg-slate-50 border border-slate-100 rounded-xl p-3 mb-3">
                <div class="grid grid-cols-2 gap-2">
                    <div>
                        <label class="text-[9px] font-bold text-slate-500 uppercase tracking-wider mb-1 block">Category</label>
                        <select id="category-filter-select" class="w-full text-[10px] p-2 border border-slate-200 rounded-lg bg-white">
                            <option value="">All Categories</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="text-[9px] font-bold text-slate-500 uppercase tracking-wider mb-1 block">City</label>
                        <select id="city-filter-select" class="w-full text-[10px] p-2 border border-slate-200 rounded-lg bg-white">
                            <option value="">All Cities</option>
                            @foreach($cities as $city)
                                <option value="{{ $city }}">{{ $city }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <!-- Scrollable Cards Box -->
            <div class="cards-container space-y-2" id="location-cards-box"></div>
        </aside>

        <!-- Floating Search Box on Map -->
        <div class="map-search-bar">
            <i class="bi bi-search"></i>
            <input type="text" placeholder="Search location, venue, category..." id="network-search-input">
        </div>

        <!-- Floating Selected Summary Bar -->
        <div class="selected-summary-bar" id="selected-summary-bar">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-lg bg-indigo-100 flex items-center justify-center text-indigo-600">
                    <i class="bi bi-cart-check-fill"></i>
                </div>
                <div>
                    <span class="block text-[10px] font-bold text-slate-500 uppercase">Selected</span>
                    <span class="block text-sm font-bold text-slate-900" id="selected-count-text">0 locations</span>
                </div>
            </div>
            <button class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-xs font-bold transition-all shadow-md" id="proceed-campaign-btn">
                Proceed to Campaign
            </button>
        </div>

        <!-- Leaflet Map Canvas -->
        <div id="networkMap" class="network-map-canvas"></div>

        <!-- Right Side details Drawer -->
        <div class="details-drawer" id="details-drawer">
            <div class="drawer-header">
                <button class="drawer-close-btn" id="drawer-close-btn"><i class="bi bi-x-lg"></i></button>
                <div class="pr-8">
                    <div class="flex justify-between items-start">
                        <div>
                            <span class="text-[9px] font-bold text-indigo-600 uppercase tracking-widest" id="drawer-category">Category</span>
                            <h2 class="font-bold text-slate-900 text-lg mt-0.5 flex items-center gap-2">
                                <span id="drawer-name">Location Name</span>
                                <button id="favorite-btn" class="text-slate-300 hover:text-rose-500 transition-colors" title="Toggle Favorite" onclick="toggleFavorite()">
                                    <i class="bi bi-heart-fill"></i>
                                </button>
                            </h2>
                        </div>
                    </div>
                    <div class="flex items-center gap-2 mt-1">
                        <div class="text-amber-500 text-[10px] flex gap-0.5" id="drawer-rating-stars">
                            <i class="bi bi-star"></i><i class="bi bi-star"></i><i class="bi bi-star"></i><i class="bi bi-star"></i><i class="bi bi-star"></i>
                        </div>
                        <span class="text-slate-500 font-semibold text-[9px]" id="drawer-rating-text">0 (0 reviews)</span>
                    </div>
                </div>
            </div>

            <div class="drawer-body">
                <div class="drawer-image-wrapper">
                    <img src="" id="drawer-image" alt="Venue Cover">
                </div>

                <div class="space-y-4">
                    <div>
                        <span class="block text-[9px] text-slate-400 font-bold uppercase tracking-wider mb-1">Address</span>
                        <p class="text-slate-800 text-[11px] leading-relaxed" id="drawer-address"></p>
                    </div>
                    <div>
                        <span class="block text-[9px] text-slate-400 font-bold uppercase tracking-wider mb-1">Description</span>
                        <p class="text-slate-600 text-[11px] leading-relaxed" id="drawer-description"></p>
                    </div>

                    <div class="drawer-meta-grid">
                        <div class="drawer-meta-item">
                            <span class="block text-[8px] text-slate-400 font-bold uppercase">Screen</span>
                            <span class="block text-xs font-bold text-slate-800 mt-0.5" id="drawer-screen-details">-</span>
                        </div>
                        <div class="drawer-meta-item">
                            <span class="block text-[8px] text-slate-400 font-bold uppercase">Monthly Footfall</span>
                            <span class="block text-xs font-bold text-indigo-600 mt-0.5 font-mono" id="drawer-impressions">0</span>
                        </div>
                    </div>
                    <div class="flex gap-2" id="drawer-media-support"></div>
                </div>

                <!-- Reviews Section -->
                <div class="mt-8 pt-6 border-t border-slate-100">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="font-bold text-slate-900 text-sm">Reviews</h3>
                        @auth
                        <button onclick="document.getElementById('write-review-form').classList.toggle('hidden')" class="text-xs font-bold text-indigo-600 hover:text-indigo-700">Write a Review</button>
                        @endauth
                    </div>

                    <!-- Write Review Form -->
                    <form id="write-review-form" class="hidden mb-6 bg-slate-50 p-4 rounded-xl border border-slate-200" onsubmit="submitReview(event)">
                        <div class="mb-3">
                            <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Rating</label>
                            <div class="star-selector" id="review-rating-selector">
                                <input type="radio" id="star5" name="rating" value="5" required /><label for="star5" class="bi bi-star-fill"></label>
                                <input type="radio" id="star4" name="rating" value="4" /><label for="star4" class="bi bi-star-fill"></label>
                                <input type="radio" id="star3" name="rating" value="3" /><label for="star3" class="bi bi-star-fill"></label>
                                <input type="radio" id="star2" name="rating" value="2" /><label for="star2" class="bi bi-star-fill"></label>
                                <input type="radio" id="star1" name="rating" value="1" /><label for="star1" class="bi bi-star-fill"></label>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Review</label>
                            <textarea id="review-text" name="review" rows="3" required class="w-full text-xs p-2 border border-slate-200 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="Share your experience..."></textarea>
                        </div>
                        <div class="flex justify-end">
                            <button type="submit" class="px-4 py-1.5 bg-indigo-600 text-white rounded-lg text-xs font-bold hover:bg-indigo-700 transition-colors" id="submit-review-btn">Submit Review</button>
                        </div>
                    </form>

                    <div id="drawer-reviews-list" class="space-y-2 text-sm">
                        <!-- Reviews injected via JS -->
                        <div class="text-xs text-slate-400 text-center py-4">Loading reviews...</div>
                    </div>
                </div>
            </div>

            <div class="drawer-footer space-y-3">
                <div class="flex items-center justify-between">
                    <div>
                        <span class="block text-[9px] text-slate-400 font-bold uppercase">Base Price</span>
                        <span class="text-lg font-bold text-slate-900 font-mono" id="drawer-rate">₹0</span>
                        <span class="text-[10px] text-slate-400">/ day</span>
                    </div>
                    <span class="text-[9px] text-slate-500 bg-slate-100 px-2 py-1 rounded-full font-bold" id="drawer-repeats">Daily repeats</span>
                </div>
                
                <div id="drawer-actions">
                    <!-- Buttons injected via JS -->
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const coimbatoreCenter = [11.0168, 76.9558];
        const map = L.map('networkMap', { zoomControl: true }).setView(coimbatoreCenter, 12);

        L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', { maxZoom: 19 }).addTo(map);

        let markersDict = {};
        let allLocations = [];
        let selectedLocations = new Set(); // Store selected location IDs
        let currentDrawerLocation = null;

        const searchInput = document.getElementById('network-search-input');
        const categorySelect = document.getElementById('category-filter-select');
        const citySelect = document.getElementById('city-filter-select');
        const cardsBox = document.getElementById('location-cards-box');
        const detailsDrawer = document.getElementById('details-drawer');
        const summaryBar = document.getElementById('selected-summary-bar');
        const summaryCountText = document.getElementById('selected-count-text');
        const proceedBtn = document.getElementById('proceed-campaign-btn');

        document.getElementById('drawer-close-btn').addEventListener('click', function() {
            detailsDrawer.classList.remove('open');
            document.querySelectorAll('.location-card').forEach(card => card.classList.remove('highlighted'));
            currentDrawerLocation = null;
        });

        function fetchNetworkNodes() {
            axios.get('/api/network/locations', {
                params: {
                    search: searchInput.value,
                    category_id: categorySelect.value,
                    city: citySelect.value
                }
            }).then(response => {
                allLocations = response.data;
                document.getElementById('locations-count').textContent = allLocations.length;
                
                Object.values(markersDict).forEach(marker => map.removeLayer(marker));
                markersDict = {};

                allLocations.forEach(location => {
                    const isSelected = selectedLocations.has(location.id);
                    const pinHtml = `
                        <div class="ha-marker-container">
                            <div class="ha-map-pin ${isSelected ? 'selected' : ''}">
                                <i class="bi ${location.category.icon ?? 'bi-display'}"></i>
                            </div>
                        </div>
                    `;

                    const customIcon = L.divIcon({ html: pinHtml, className: 'custom-map-marker', iconSize: [36, 36], iconAnchor: [18, 36] });
                    const marker = L.marker([location.latitude, location.longitude], { icon: customIcon }).addTo(map);
                    markersDict[location.id] = marker;

                    marker.on('click', function() {
                        map.setView([location.latitude, location.longitude], 14, { animate: true });
                        showLocationDetails(location);
                        highlightSidebarCard(location.id);
                    });
                });

                renderCardsList();
            });
        }

        function renderCardsList() {
            cardsBox.innerHTML = '';
            if (allLocations.length === 0) {
                cardsBox.innerHTML = '<div class="text-center py-5 text-slate-400 text-xs">No locations found.</div>';
                return;
            }

            allLocations.forEach(location => {
                const rate = location.price_per_day || Math.max(50, Math.round(parseInt(location.daily_footfall.replace(/,/g, '')) / 100));
                const isSelected = selectedLocations.has(location.id);

                const cardDiv = document.createElement('div');
                cardDiv.className = `location-card ${isSelected ? 'border-emerald-300 bg-emerald-50/20' : ''}`;
                cardDiv.id = `card-${location.id}`;
                cardDiv.innerHTML = `
                    <div class="location-card-image"><img src="${location.primary_image}"></div>
                    <div class="location-card-content">
                        <div>
                            <div class="flex justify-between items-start">
                                <span class="text-[9px] font-bold text-slate-400 uppercase tracking-wider">${location.category.name}</span>
                                ${location.is_favorited ? '<i class="bi bi-heart-fill text-rose-500 text-[10px]"></i>' : ''}
                            </div>
                            <h6 class="font-bold text-slate-800 text-xs">${location.name}</h6>
                            <div class="flex items-center gap-1 mt-0.5">
                                <i class="bi bi-star-fill text-amber-500 text-[8px]"></i>
                                <span class="text-[9px] text-slate-500 font-medium">${parseFloat(location.average_rating || 0).toFixed(1)}</span>
                                <span class="text-[9px] text-slate-400 ml-1">· ${location.city}</span>
                            </div>
                        </div>
                        <div class="mt-1 flex items-center justify-between">
                            <div><span class="text-[11px] font-bold text-slate-900 font-mono">₹${rate}</span><span class="text-[9px] text-slate-400">/day</span></div>
                            ${isSelected ? '<span class="text-[9px] font-bold text-emerald-600 bg-emerald-100 px-1.5 py-0.5 rounded">Selected</span>' : ''}
                        </div>
                    </div>
                `;

                cardDiv.addEventListener('click', function() {
                    highlightSidebarCard(location.id);
                    map.setView([location.latitude, location.longitude], 14, { animate: true });
                    showLocationDetails(location);
                });

                cardsBox.appendChild(cardDiv);
            });
        }

        function showLocationDetails(location) {
            currentDrawerLocation = location;
            const rate = location.price_per_day || Math.max(50, Math.round(parseInt(location.daily_footfall.replace(/,/g, '')) / 100));

            document.getElementById('drawer-category').textContent = location.category.name;
            document.getElementById('drawer-name').textContent = location.name;
            
            // Render Rating Stars
            let starsHtml = '';
            const avgRating = parseFloat(location.average_rating || 0);
            for(let i=1; i<=5; i++) {
                if(avgRating >= i) starsHtml += '<i class="bi bi-star-fill"></i>';
                else if(avgRating >= i - 0.5) starsHtml += '<i class="bi bi-star-half"></i>';
                else starsHtml += '<i class="bi bi-star"></i>';
            }
            document.getElementById('drawer-rating-stars').innerHTML = starsHtml;
            document.getElementById('drawer-rating-text').textContent = `${avgRating.toFixed(1)} (${location.reviews_count} reviews)`;
            
            // Favorite status
            const favBtn = document.getElementById('favorite-btn');
            if (location.is_favorited) {
                favBtn.classList.replace('text-slate-300', 'text-rose-500');
            } else {
                favBtn.classList.replace('text-rose-500', 'text-slate-300');
            }

            document.getElementById('drawer-image').src = location.primary_image;
            document.getElementById('drawer-address').innerHTML = `<i class="bi bi-geo-alt text-indigo-600"></i> ${location.address}, ${location.city}`;
            document.getElementById('drawer-description').textContent = location.description || 'Premium indoor advertising display screens.';
            document.getElementById('drawer-screen-details').innerHTML = `${location.screen_size ? location.screen_size + ' inches' : '-'} <br><span class="text-[9px] text-slate-500 font-normal">${location.screen_orientation || ''}</span>`;
            document.getElementById('drawer-impressions').textContent = location.audience_count || '0';
            document.getElementById('drawer-rate').textContent = `₹${rate}`;
            
            document.getElementById('write-review-form').classList.add('hidden');
            
            renderDrawerActions();
            loadReviews(location.id);
            detailsDrawer.classList.add('open');
        }

        function renderDrawerActions() {
            if (!currentDrawerLocation) return;
            const isSelected = selectedLocations.has(currentDrawerLocation.id);
            const container = document.getElementById('drawer-actions');
            
            if (isSelected) {
                container.innerHTML = `
                    <div class="flex items-center gap-2">
                        <button class="flex-grow py-2.5 bg-rose-50 hover:bg-rose-100 text-rose-600 rounded-lg text-xs font-bold transition-all border border-rose-200" onclick="toggleLocationSelection(${currentDrawerLocation.id})">
                            <i class="bi bi-dash-circle"></i> Remove
                        </button>
                        <button class="flex-grow py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-xs font-bold transition-all shadow-md" onclick="addAnotherLocation()">
                            Add Another <i class="bi bi-arrow-right"></i>
                        </button>
                    </div>
                `;
            } else {
                container.innerHTML = `
                    <button class="w-full py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-xs font-bold flex items-center justify-center gap-2 transition-all shadow-md" onclick="toggleLocationSelection(${currentDrawerLocation.id})">
                        <i class="bi bi-plus-circle"></i> Add to Booking
                    </button>
                `;
            }
        }

        window.toggleLocationSelection = function(id) {
            if (selectedLocations.has(id)) {
                selectedLocations.delete(id);
            } else {
                selectedLocations.add(id);
            }
            updateSummaryAndMap();
            renderDrawerActions();
            renderCardsList(); // re-render to show/hide "Selected" badges
        };

        window.addAnotherLocation = function() {
            detailsDrawer.classList.remove('open');
            document.querySelectorAll('.location-card').forEach(card => card.classList.remove('highlighted'));
            currentDrawerLocation = null;
        };

        function updateSummaryAndMap() {
            const count = selectedLocations.size;
            summaryCountText.textContent = `${count} location${count !== 1 ? 's' : ''}`;
            
            if (count > 0) {
                summaryBar.classList.add('show');
            } else {
                summaryBar.classList.remove('show');
            }

            // Update Map Pins color
            allLocations.forEach(location => {
                const marker = markersDict[location.id];
                if (marker) {
                    const iconElement = marker.getElement().querySelector('.ha-map-pin');
                    if (selectedLocations.has(location.id)) {
                        iconElement.classList.add('selected');
                    } else {
                        iconElement.classList.remove('selected');
                    }
                }
            });
        }
        
        window.toggleFavorite = function() {
            if (!currentDrawerLocation) return;
            const locId = currentDrawerLocation.id;
            const btn = document.getElementById('favorite-btn');
            const originalColor = btn.className;
            
            btn.classList.replace('text-rose-500', 'text-slate-300'); // Optimistic UI
            if(originalColor.includes('text-slate-300')) {
                btn.classList.replace('text-slate-300', 'text-rose-500');
            }

            axios.post(`/api/network/locations/${locId}/favorite`)
                .then(response => {
                    const isFav = response.data.is_favorited;
                    if (isFav) {
                        btn.classList.replace('text-slate-300', 'text-rose-500');
                    } else {
                        btn.classList.replace('text-rose-500', 'text-slate-300');
                    }
                    // Update current data so UI stays in sync
                    currentDrawerLocation.is_favorited = isFav;
                    const locInArray = allLocations.find(l => l.id === locId);
                    if(locInArray) locInArray.is_favorited = isFav;
                    renderCardsList(); // Refresh sidebar heart icon
                })
                .catch(error => {
                    btn.className = originalColor; // Revert
                    if (error.response && error.response.status === 401) {
                        alert("Please login to add favorites.");
                    }
                });
        };

        window.submitReview = function(e) {
            e.preventDefault();
            if (!currentDrawerLocation) return;
            const form = e.target;
            const formData = new FormData(form);
            const btn = document.getElementById('submit-review-btn');
            const originalBtnText = btn.textContent;
            
            btn.textContent = 'Submitting...';
            btn.disabled = true;

            axios.post(`/api/network/locations/${currentDrawerLocation.id}/reviews`, Object.fromEntries(formData))
                .then(response => {
                    form.reset();
                    form.classList.add('hidden');
                    // Update stats
                    currentDrawerLocation.average_rating = response.data.new_average;
                    currentDrawerLocation.reviews_count = response.data.new_count;
                    const locInArray = allLocations.find(l => l.id === currentDrawerLocation.id);
                    if(locInArray) {
                        locInArray.average_rating = response.data.new_average;
                        locInArray.reviews_count = response.data.new_count;
                    }
                    
                    showLocationDetails(currentDrawerLocation); // re-render drawer header
                    renderCardsList(); // update sidebar rating
                })
                .catch(error => {
                    alert(error.response?.data?.message || 'Error submitting review');
                })
                .finally(() => {
                    btn.textContent = originalBtnText;
                    btn.disabled = false;
                });
        };
        
        function loadReviews(locId) {
            const listEl = document.getElementById('drawer-reviews-list');
            listEl.innerHTML = '<div class="text-xs text-slate-400 text-center py-4">Loading reviews...</div>';
            
            axios.get(`/api/network/locations/${locId}/reviews`)
                .then(response => {
                    const reviews = response.data.reviews;
                    if (reviews.length === 0) {
                        listEl.innerHTML = '<div class="text-xs text-slate-400 text-center py-4 bg-slate-50 rounded-xl">No reviews yet.</div>';
                        return;
                    }
                    
                    let html = '';
                    reviews.forEach(review => {
                        let stars = '';
                        for(let i=1; i<=5; i++) {
                            stars += `<i class="bi ${i <= review.rating ? 'bi-star-fill text-amber-500' : 'bi-star text-slate-300'}"></i>`;
                        }
                        
                        // format date
                        const d = new Date(review.created_at);
                        const dateStr = d.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
                        
                        html += `
                        <div class="review-item">
                            <div class="flex justify-between items-start mb-1">
                                <span class="font-bold text-slate-800 text-[11px]">${review.user.name}</span>
                                <span class="text-[9px] text-slate-400">${dateStr}</span>
                            </div>
                            <div class="text-[9px] flex gap-0.5 mb-1">${stars}</div>
                            <p class="text-[11px] text-slate-600 leading-relaxed">${review.review}</p>
                        </div>
                        `;
                    });
                    listEl.innerHTML = html;
                })
                .catch(() => {
                    listEl.innerHTML = '<div class="text-xs text-rose-500 text-center py-4">Failed to load reviews.</div>';
                });
        }

        proceedBtn.addEventListener('click', function() {
            if (selectedLocations.size === 0) return;
            const params = new URLSearchParams();
            selectedLocations.forEach(id => params.append('locations[]', id));
            window.location.href = `{{ route('advertiser.my-requests.create') }}?${params.toString()}`;
        });

        function highlightSidebarCard(id) {
            document.querySelectorAll('.location-card').forEach(c => c.classList.remove('highlighted'));
            const card = document.getElementById(`card-${id}`);
            if (card) {
                card.classList.add('highlighted');
                card.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
            }
        }

        searchInput.addEventListener('input', debounce(fetchNetworkNodes, 300));
        categorySelect.addEventListener('change', fetchNetworkNodes);
        citySelect.addEventListener('change', fetchNetworkNodes);

        fetchNetworkNodes();

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
