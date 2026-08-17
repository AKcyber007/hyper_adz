@extends('admin.layouts.app', [
    'title' => 'Locations Map Grid | Hyper Adz Admin'
])

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white flex items-center gap-2.5">
                <i class="bi bi-map-fill text-[#1155CC]"></i> Network Map Grid
            </h1>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Admin interactive map visualizer. Search and select any location on the map to create new locations, or click pins to edit.</p>
        </div>
        <div class="flex gap-2">
            <button onclick="openManualCreateModal()" class="px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-sm font-semibold transition-all flex items-center gap-2 shadow-md">
                <i class="bi bi-geo-alt"></i> Manual Location Creation
            </button>
            <button onclick="triggerSearchMode()" class="px-4 py-2.5 bg-[#1155CC] hover:bg-blue-700 text-white rounded-xl text-sm font-semibold transition-all flex items-center gap-2 shadow-md">
                <i class="bi bi-plus-lg"></i> Add New Location
            </button>
            <a href="{{ route('admin.locations.index') }}" class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 dark:bg-slate-800 dark:hover:bg-slate-700 dark:text-slate-350 rounded-xl text-sm font-semibold transition-all flex items-center gap-2 border border-slate-200/50 dark:border-slate-700">
                <i class="bi bi-list-task"></i> View List Inventory
            </a>
        </div>
    </div>

    <!-- Alert Messaging -->
    <div id="map-alert" class="hidden p-4 rounded-2xl text-xs font-semibold flex items-center gap-2 shadow-sm border mb-4"></div>

    <!-- Map Canvas Card -->
    <div class="bg-white dark:bg-slate-800 rounded-3xl p-4 border border-slate-100 dark:border-slate-700/50 shadow-sm relative overflow-hidden" style="height: 700px;">
        <!-- Nominatim Search Overlay -->
        <div class="absolute top-8 left-8 z-[1000] w-96 flex flex-col gap-2">
            <div class="bg-white dark:bg-slate-900 p-2 rounded-2xl shadow-xl border border-slate-200/80 dark:border-slate-700 flex items-center gap-2">
                <i class="bi bi-search text-slate-400 ms-2"></i>
                <input type="text" id="map-search-input" placeholder="Search location or address..." class="w-full bg-transparent border-0 outline-none focus:ring-0 text-sm text-slate-700 dark:text-slate-200 py-1">
                <button onclick="performMapSearch()" class="px-3 py-1.5 bg-[#1155CC] text-white text-xs font-bold rounded-xl hover:bg-blue-600 transition-all">Search</button>
            </div>
            <div id="search-results-dropdown" class="hidden bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-2xl shadow-2xl max-h-60 overflow-y-auto flex flex-col divide-y divide-slate-100 dark:divide-slate-800"></div>
        </div>

        <div id="adminMap" style="height: 100%; width: 100%; border-radius: 20px; z-index: 10;" class="overflow-hidden border border-slate-200/50 dark:border-slate-700"></div>

        <!-- Float Map Legend -->
        <div class="absolute bottom-8 left-8 bg-white/95 dark:bg-slate-900/95 backdrop-blur-md px-4 py-3 rounded-2xl border border-slate-200/50 dark:border-slate-750 shadow-md flex flex-col gap-2 z-20 text-xs">
            <span class="font-bold text-slate-700 dark:text-slate-300 border-b pb-1.5 mb-0.5">Status Legend</span>
            <div class="flex items-center gap-2">
                <span class="w-3 h-3 bg-[#1155CC] rounded-full border border-white"></span>
                <span class="text-slate-600 dark:text-slate-400 font-medium">Active (Operational)</span>
            </div>
            <div class="flex items-center gap-2">
                <span class="w-3 h-3 bg-[#F59E0B] rounded-full border border-white"></span>
                <span class="text-slate-600 dark:text-slate-400 font-medium">Maintenance (Down)</span>
            </div>
            <div class="flex items-center gap-2">
                <span class="w-3 h-3 bg-[#64748B] rounded-full border border-white"></span>
                <span class="text-slate-600 dark:text-slate-400 font-medium">Inactive (Off Grid)</span>
            </div>
        </div>
    </div>
</div>

<!-- Sliding modal for Add / Edit Location -->
<div id="locationModal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-[9999] hidden flex items-center justify-center p-4 overflow-y-auto">
    <div class="bg-white dark:bg-slate-900 rounded-[32px] w-full max-w-2xl overflow-hidden shadow-2xl border border-slate-100 dark:border-slate-800 flex flex-col max-h-[90vh]">
        <!-- Modal Header -->
        <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100 dark:border-slate-800">
            <h3 class="text-lg font-bold text-slate-900 dark:text-white" id="modal-title">Create New Location</h3>
            <button onclick="closeModal()" class="w-8 h-8 rounded-full bg-slate-100 dark:bg-slate-800 text-slate-500 hover:text-slate-700 dark:hover:text-white flex items-center justify-center">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>

        <!-- Modal Body (Form scrollable) -->
        <form id="location-map-form" class="overflow-y-auto flex-grow p-6 space-y-4" enctype="multipart/form-data">
            @csrf
            <input type="hidden" id="form-location-id" name="id">
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Location Name -->
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">Location Name *</label>
                    <input type="text" id="form-name" name="name" required class="w-full bg-slate-50 dark:bg-slate-800 border-slate-200 dark:border-slate-700 rounded-xl px-4 py-2.5 text-sm text-slate-800 dark:text-white">
                </div>

                <!-- Business Name -->
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">Business / Brand Name</label>
                    <input type="text" id="form-business-name" name="business_name" class="w-full bg-slate-50 dark:bg-slate-800 border-slate-200 dark:border-slate-700 rounded-xl px-4 py-2.5 text-sm text-slate-800 dark:text-white">
                </div>

                <!-- Category -->
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">Category *</label>
                    <select id="form-category" name="category_id" required class="w-full bg-slate-50 dark:bg-slate-800 border-slate-200 dark:border-slate-700 rounded-xl px-4 py-2.5 text-sm text-slate-800 dark:text-white">
                        <option value="">Select Category</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Status -->
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">Status *</label>
                    <select id="form-status" name="status" required class="w-full bg-slate-50 dark:bg-slate-800 border-slate-200 dark:border-slate-700 rounded-xl px-4 py-2.5 text-sm text-slate-800 dark:text-white">
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                        <option value="maintenance">Maintenance</option>
                    </select>
                </div>

                <!-- Price Per Day -->
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">Advertising Price / day (₹) *</label>
                    <input type="number" step="0.01" min="0" id="form-price" name="price_per_day" required class="w-full bg-slate-50 dark:bg-slate-800 border-slate-200 dark:border-slate-700 rounded-xl px-4 py-2.5 text-sm text-slate-800 dark:text-white font-mono">
                </div>

                <!-- Owner Partner -->
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">Assigned Location Partner *</label>
                    <select id="form-partner" name="location_partner_id" class="w-full bg-slate-50 dark:bg-slate-800 border-slate-200 dark:border-slate-700 rounded-xl px-4 py-2.5 text-sm text-slate-800 dark:text-white">
                        <option value="">No Partner (Admin Managed)</option>
                        @foreach($partners as $partner)
                            <option value="{{ $partner->id }}">{{ $partner->company_name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Operating Info & Specs -->
            <div class="border-t border-slate-100 dark:border-slate-800 pt-4 space-y-4">
                <h4 class="text-sm font-bold text-slate-800 dark:text-white">Specs & Details</h4>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">Monthly Audience</label>
                        <input type="number" id="form-audience" name="audience_count" class="w-full bg-slate-50 dark:bg-slate-800 border-slate-200 dark:border-slate-700 rounded-xl px-4 py-2.5 text-sm text-slate-800 dark:text-white">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">Repeats Per Day</label>
                        <input type="number" id="form-repeats" name="repeats_per_day" class="w-full bg-slate-50 dark:bg-slate-800 border-slate-200 dark:border-slate-700 rounded-xl px-4 py-2.5 text-sm text-slate-800 dark:text-white">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">Screen Size</label>
                        <input type="text" id="form-screen-size" name="screen_size" placeholder="e.g. 55 inch" class="w-full bg-slate-50 dark:bg-slate-800 border-slate-200 dark:border-slate-700 rounded-xl px-4 py-2.5 text-sm text-slate-800 dark:text-white">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">Orientation</label>
                        <select id="form-orientation" name="screen_orientation" class="w-full bg-slate-50 dark:bg-slate-800 border-slate-200 dark:border-slate-700 rounded-xl px-4 py-2.5 text-sm text-slate-800 dark:text-white">
                            <option value="">Select Orientation</option>
                            <option value="Landscape">Landscape</option>
                            <option value="Portrait">Portrait</option>
                        </select>
                    </div>
                </div>
                
                <div class="flex gap-6 mt-2">
                    <label class="flex items-center gap-2">
                        <input type="checkbox" id="form-video" name="video_supported" value="1" class="rounded text-[#1155CC] focus:ring-[#1155CC]">
                        <span class="text-xs font-bold text-slate-700 dark:text-slate-300">Video Supported</span>
                    </label>
                    <label class="flex items-center gap-2">
                        <input type="checkbox" id="form-audio" name="audio_supported" value="1" class="rounded text-[#1155CC] focus:ring-[#1155CC]">
                        <span class="text-xs font-bold text-slate-700 dark:text-slate-300">Audio Supported</span>
                    </label>
                </div>
            </div>

            <!-- Address and Geolocation details -->
            <div class="border-t border-slate-100 dark:border-slate-800 pt-4 space-y-4">
                <h4 class="text-sm font-bold text-slate-800 dark:text-white">Address Details (Set via Map Click)</h4>
                
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">Street Address *</label>
                    <input type="text" id="form-address" name="address" required class="w-full bg-slate-50 dark:bg-slate-800 border-slate-200 dark:border-slate-700 rounded-xl px-4 py-2.5 text-sm text-slate-800 dark:text-white">
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">City *</label>
                        <input type="text" id="form-city" name="city" required class="w-full bg-slate-50 dark:bg-slate-800 border-slate-200 dark:border-slate-700 rounded-xl px-4 py-2.5 text-sm text-slate-800 dark:text-white">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">State *</label>
                        <input type="text" id="form-state" name="state" required class="w-full bg-slate-50 dark:bg-slate-800 border-slate-200 dark:border-slate-700 rounded-xl px-4 py-2.5 text-sm text-slate-800 dark:text-white">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">Postal Code *</label>
                        <input type="text" id="form-zip" name="postal_code" required class="w-full bg-slate-50 dark:bg-slate-800 border-slate-200 dark:border-slate-700 rounded-xl px-4 py-2.5 text-sm text-slate-800 dark:text-white">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">Latitude *</label>
                        <input type="number" step="any" id="form-lat" name="latitude" required onchange="updatePinFromInputs()" class="w-full bg-slate-50 dark:bg-slate-800 border-slate-200 dark:border-slate-700 rounded-xl px-4 py-2.5 text-sm text-slate-800 dark:text-white font-mono">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">Longitude *</label>
                        <input type="number" step="any" id="form-lng" name="longitude" required onchange="updatePinFromInputs()" class="w-full bg-slate-50 dark:bg-slate-800 border-slate-200 dark:border-slate-700 rounded-xl px-4 py-2.5 text-sm text-slate-800 dark:text-white font-mono">
                    </div>
                </div>
                <div class="mt-2 text-right">
                    <button type="button" onclick="locateOnMap()" class="px-4 py-2 bg-slate-800 text-white dark:bg-slate-700 hover:bg-slate-700 dark:hover:bg-slate-600 rounded-xl text-xs font-bold transition-all shadow-sm">
                        <i class="bi bi-crosshair"></i> Locate on Map
                    </button>
                </div>
            </div>

            <!-- Description -->
            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">Description</label>
                <textarea id="form-description" name="description" rows="3" class="w-full bg-slate-50 dark:bg-slate-800 border-slate-200 dark:border-slate-700 rounded-xl px-4 py-2.5 text-sm text-slate-800 dark:text-white"></textarea>
            </div>

            <!-- Photos upload -->
            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">Upload Location Photos</label>
                <input type="file" id="form-images" name="images[]" multiple accept="image/*" class="w-full text-xs text-slate-500 dark:text-slate-400 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-slate-150 dark:file:bg-slate-800 file:text-[#1155CC] dark:file:text-blue-400 hover:file:bg-slate-200 dark:hover:file:bg-slate-750">
                <span class="block text-[10px] text-slate-400 mt-1">Select multiple image files. Max size 5MB per file.</span>
            </div>

            <!-- Edit photos preview/delete -->
            <div id="image-edit-gallery" class="hidden border-t border-slate-100 dark:border-slate-800 pt-4">
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">Existing Photos (Check to Delete)</label>
                <div id="modal-gallery-grid" class="grid grid-cols-4 gap-3"></div>
            </div>
        </form>

        <!-- Modal Footer -->
        <div class="px-6 py-4 border-t border-slate-100 dark:border-slate-800 flex items-center justify-end gap-3 bg-slate-50 dark:bg-slate-900/50">
            <button onclick="closeModal()" type="button" class="px-4 py-2 bg-slate-200 hover:bg-slate-300 text-slate-700 dark:bg-slate-855 dark:hover:bg-slate-800 dark:text-slate-300 rounded-xl text-xs font-bold">Cancel</button>
            <button onclick="submitLocationForm()" id="submit-btn" type="button" class="px-5 py-2.5 bg-[#1155CC] hover:bg-blue-600 text-white rounded-xl text-xs font-bold shadow-md shadow-blue-500/10">Save Location</button>
        </div>
    </div>
</div>
@endsection

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<style>
    /* Ensure the map canvas fills its container */
    #adminMap { height: 100%; width: 100%; }
    .custom-map-marker { background: transparent; border: none; }
    .leaflet-container { border-radius: 20px; }
</style>
@endpush

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script>
    let map;
    let markers = [];
    let tempMarker = null;

    document.addEventListener('DOMContentLoaded', function () {
        const coimbatoreCenter = [11.0168, 76.9558];
        const defaultZoom = 12;

        // Initialize Map
        map = L.map('adminMap', {
            zoomControl: true
        }).setView(coimbatoreCenter, defaultZoom);

        // Load OSM Tiles
        L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright" target="_blank">OpenStreetMap</a> contributors'
        }).addTo(map);

        // Load All Locations
        fetchLocations();

        // Click Map Listener to drop marker and trigger create
        map.on('click', function(e) {
            handleMapClick(e.latlng.lat, e.latlng.lng);
        });

        // Search enter listener
        document.getElementById('map-search-input').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                performMapSearch();
            }
        });
    });

    // Fetch and render locations
    function fetchLocations() {
        axios.get('/api/network/locations?status=all')
            .then(response => {
                const locations = response.data;
                
                // Clear old markers
                markers.forEach(m => map.removeLayer(m));
                markers = [];

                locations.forEach(location => {
                    const pinColor = location.status === 'active' ? '#1155CC' : 
                                     (location.status === 'maintenance' ? '#F59E0B' : '#64748B');

                    const pinHtml = `
                        <div class="ha-marker-container">
                            <div class="ha-pin-pulse ${location.status}"></div>
                            <div class="ha-map-pin ${location.status}">
                                <i class="bi ${location.category.icon ?? 'bi-display'}"></i>
                            </div>
                        </div>
                    `;

                    const customIcon = L.divIcon({
                        html: pinHtml,
                        className: 'custom-map-marker',
                        iconSize: [32, 32],
                        iconAnchor: [16, 32],
                        popupAnchor: [0, -32]
                    });

                    const marker = L.marker([location.latitude, location.longitude], { icon: customIcon })
                        .addTo(map);

                    markers.push(marker);

                    // Marker click popup
                    const popupContent = `
                        <div style="width: 200px; font-family: 'Plus Jakarta Sans', sans-serif; padding: 4px;">
                            <div style="width: 100%; height: 90px; border-radius: 8px; overflow: hidden; margin-bottom: 8px; background: #e2e8f0;">
                                <img src="${location.primary_image}" style="width: 100%; height: 100%; object-fit: cover;">
                            </div>
                            <h6 class="fw-bold mb-1" style="color: #0A1628; margin: 0; font-family: 'Sora', sans-serif; font-size: 0.95rem;">${location.name}</h6>
                            <p class="text-xs text-muted mb-1" style="margin: 0;"><i class="bi bi-tag"></i> ${location.category.name} | ₹${location.price_per_day}/day</p>
                            <p class="text-xs text-slate-500 mb-2" style="margin: 0;"><i class="bi bi-geo-alt"></i> ${location.city}</p>
                            <button onclick="editLocationFromMap(${JSON.stringify(location).replace(/"/g, '&quot;')})" style="display: block; width: 100%; text-align: center; background: #1155CC; border: none; color: #ffffff; padding: 6px; border-radius: 6px; font-size: 0.75rem; font-weight: bold; cursor: pointer; transition: all 0.2s;">
                                <i class="bi bi-pencil-square"></i> Edit Details
                            </button>
                        </div>
                    `;
                    marker.bindPopup(popupContent);
                });
            })
            .catch(error => {
                console.error('Error fetching admin map locations:', error);
            });
    }

    // Drop marker on click and trigger modal
    function handleMapClick(lat, lng) {
        if (tempMarker) {
            map.removeLayer(tempMarker);
        }

        tempMarker = L.marker([lat, lng], { draggable: true }).addTo(map);
        tempMarker.on('dragend', function(e) {
            const pos = e.target.getLatLng();
            geocodeCoordinates(pos.lat, pos.lng);
        });

        geocodeCoordinates(lat, lng);
    }

    // Update marker location when inputs change
    function updatePinFromInputs() {
        const lat = parseFloat(document.getElementById('form-lat').value);
        const lng = parseFloat(document.getElementById('form-lng').value);

        if (isNaN(lat) || isNaN(lng)) return;

        if (tempMarker) {
            tempMarker.setLatLng([lat, lng]);
        } else {
            tempMarker = L.marker([lat, lng], { draggable: true }).addTo(map);
            tempMarker.on('dragend', function(e) {
                const pos = e.target.getLatLng();
                geocodeCoordinates(pos.lat, pos.lng);
            });
        }
        
        map.setView([lat, lng], 15, { animate: true });
        
        // Optionally reverse geocode based on manual coordinate entry
        // We comment this out to prevent it overwriting custom manual addresses
        // geocodeCoordinates(lat, lng);
    }

    // Geocode to get address details
    function geocodeCoordinates(lat, lng) {
        document.getElementById('location-map-form').reset();
        document.getElementById('form-lat').value = lat.toFixed(6);
        document.getElementById('form-lng').value = lng.toFixed(6);

        axios.get(`https://nominatim.openstreetmap.org/reverse?lat=${lat}&lon=${lng}&format=json&addressdetails=1`)
            .then(response => {
                const result = response.data;
                if (result && result.address) {
                    const addr = result.address;
                    const road = addr.road || addr.suburb || addr.neighbourhood || '';
                    const house = addr.house_number || '';
                    const street = [house, road].filter(Boolean).join(', ') || result.display_name;

                    document.getElementById('form-address').value = street;
                    document.getElementById('form-city').value = addr.city || addr.town || addr.village || addr.county || 'Coimbatore';
                    document.getElementById('form-state').value = addr.state || 'Tamil Nadu';
                    document.getElementById('form-zip').value = addr.postcode || '';

                    openCreateModal(lat, lng);
                }
            })
            .catch(err => {
                console.error('Reverse geocode error:', err);
                openCreateModal(lat, lng);
            });
    }

    // Open Modal for Create
    function openCreateModal(lat, lng) {
        document.getElementById('form-location-id').value = '';
        document.getElementById('modal-title').textContent = 'Create New Location';
        document.getElementById('form-lat').value = lat.toFixed(6);
        document.getElementById('form-lng').value = lng.toFixed(6);
        document.getElementById('image-edit-gallery').classList.add('hidden');
        
        document.getElementById('locationModal').classList.remove('hidden');
    }

    // Open Modal for Manual Create
    function openManualCreateModal() {
        document.getElementById('location-map-form').reset();
        document.getElementById('form-location-id').value = '';
        document.getElementById('modal-title').textContent = 'Create New Location Manually';
        document.getElementById('image-edit-gallery').classList.add('hidden');
        
        document.getElementById('locationModal').classList.remove('hidden');
    }

    // Locate manually entered coordinates on map and reverse geocode
    function locateOnMap() {
        const lat = parseFloat(document.getElementById('form-lat').value);
        const lng = parseFloat(document.getElementById('form-lng').value);

        if (isNaN(lat) || isNaN(lng)) {
            showAlert('error', 'Please enter valid Latitude and Longitude.');
            return;
        }

        if (tempMarker) {
            map.removeLayer(tempMarker);
        }

        tempMarker = L.marker([lat, lng], { draggable: true }).addTo(map);
        tempMarker.on('dragend', function(e) {
            const pos = e.target.getLatLng();
            geocodeCoordinates(pos.lat, pos.lng);
        });

        map.setView([lat, lng], 15, { animate: true });

        // Temporarily hide modal so admin can verify the map location
        document.getElementById('locationModal').classList.add('hidden');
        setTimeout(() => {
            document.getElementById('locationModal').classList.remove('hidden');
        }, 1500);

        // Fetch Address
        axios.get(`https://nominatim.openstreetmap.org/reverse?lat=${lat}&lon=${lng}&format=json&addressdetails=1`)
            .then(response => {
                const result = response.data;
                if (result && result.address) {
                    const addr = result.address;
                    const road = addr.road || addr.suburb || addr.neighbourhood || '';
                    const house = addr.house_number || '';
                    const street = [house, road].filter(Boolean).join(', ') || result.display_name;

                    document.getElementById('form-address').value = street;
                    document.getElementById('form-city').value = addr.city || addr.town || addr.village || addr.county || 'Coimbatore';
                    document.getElementById('form-state').value = addr.state || 'Tamil Nadu';
                    document.getElementById('form-zip').value = addr.postcode || '';
                }
            })
            .catch(err => {
                console.error('Reverse geocode error:', err);
            });
    }

    // Open Modal for Edit
    function editLocationFromMap(location) {
        document.getElementById('location-map-form').reset();
        
        document.getElementById('modal-title').textContent = `Edit Location: ${location.name}`;
        document.getElementById('form-location-id').value = location.id;
        document.getElementById('form-name').value = location.name;
        document.getElementById('form-business-name').value = location.business_name || '';
        document.getElementById('form-price').value = location.price_per_day;
        document.getElementById('form-category').value = location.category.id || '';
        document.getElementById('form-status').value = location.status;
        
        document.getElementById('form-audience').value = location.audience_count || '';
        document.getElementById('form-repeats').value = location.repeats_per_day || '';
        document.getElementById('form-screen-size').value = location.screen_size || '';
        document.getElementById('form-orientation').value = location.screen_orientation || '';
        document.getElementById('form-video').checked = location.video_supported ? true : false;
        document.getElementById('form-audio').checked = location.audio_supported ? true : false;

        document.getElementById('form-partner').value = location.location_partner_id || '';
        document.getElementById('form-address').value = location.address;
        document.getElementById('form-city').value = location.city;
        document.getElementById('form-state').value = location.state || 'Tamil Nadu';
        document.getElementById('form-zip').value = location.postal_code;
        document.getElementById('form-lat').value = parseFloat(location.latitude).toFixed(6);
        document.getElementById('form-lng').value = parseFloat(location.longitude).toFixed(6);
        document.getElementById('form-description').value = location.description || '';

        // Load existing images
        if (location.id) {
            const galleryGrid = document.getElementById('modal-gallery-grid');
            galleryGrid.innerHTML = '';
            
            axios.get(`/api/network/locations?status=all`)
                .then(res => {
                    const matched = res.data.find(l => l.id === location.id);
                    if (matched && matched.primary_image) {
                        document.getElementById('image-edit-gallery').classList.remove('hidden');
                        galleryGrid.innerHTML = `
                            <div class="relative group border rounded-xl overflow-hidden bg-slate-50">
                                <img src="${matched.primary_image}" class="w-full h-16 object-cover">
                            </div>
                        `;
                    }
                });
        }

        document.getElementById('locationModal').classList.remove('hidden');
    }

    // Modal close
    function closeModal() {
        document.getElementById('locationModal').classList.add('hidden');
        if (tempMarker) {
            map.removeLayer(tempMarker);
            tempMarker = null;
        }
    }

    // Search Address via Nominatim
    function performMapSearch() {
        const input = document.getElementById('map-search-input').value;
        if (!input) return;

        const dropdown = document.getElementById('search-results-dropdown');
        dropdown.innerHTML = '<div class="p-3 text-xs text-slate-450 text-center"><i class="bi bi-hourglass-split animate-spin"></i> Searching...</div>';
        dropdown.classList.remove('hidden');

        axios.get(`https://nominatim.openstreetmap.org/search?q=${encodeURIComponent(input)}&format=json&limit=5&addressdetails=1`)
            .then(res => {
                dropdown.innerHTML = '';
                const results = res.data;
                if (results.length === 0) {
                    dropdown.innerHTML = '<div class="p-3 text-xs text-slate-450 text-center">No locations found. Click map directly.</div>';
                    return;
                }

                results.forEach(res => {
                    const row = document.createElement('button');
                    row.className = 'w-full text-left p-3 hover:bg-slate-50 dark:hover:bg-slate-800 text-xs text-slate-700 dark:text-slate-200 transition-all flex flex-col gap-0.5 border-none bg-transparent';
                    row.innerHTML = `
                        <strong class="text-slate-800 dark:text-white truncate">${res.display_name}</strong>
                        <span class="text-[10px] text-slate-450 font-mono">${parseFloat(res.lat).toFixed(4)}, ${parseFloat(res.lon).toFixed(4)}</span>
                    `;
                    row.addEventListener('click', () => {
                        dropdown.classList.add('hidden');
                        const lat = parseFloat(res.lat);
                        const lon = parseFloat(res.lon);
                        map.setView([lat, lon], 15, { animate: true });
                        handleMapClick(lat, lon);
                    });
                    dropdown.appendChild(row);
                });
            })
            .catch(err => {
                console.error(err);
                dropdown.classList.add('hidden');
            });
    }

    // Trigger Add Location search mode
    function triggerSearchMode() {
        document.getElementById('map-search-input').focus();
        showAlert('info', 'Search for an address in the top-left box, or click directly on the map to add a location.');
    }

    // Form submit via AJAX
    function submitLocationForm() {
        const form = document.getElementById('location-map-form');

        if (!form.reportValidity()) {
            return;
        }

        const id = document.getElementById('form-location-id').value;
        const url = id ? `/admin/locations/map/${id}/update` : '/admin/locations/map/store';

        const formData = new FormData(form);

        document.getElementById('submit-btn').disabled = true;
        document.getElementById('submit-btn').innerHTML = '<i class="bi bi-hourglass-split animate-spin"></i> Saving...';

        axios.post(url, formData, {
            headers: {
                'Content-Type': 'multipart/form-data',
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(res => {
            document.getElementById('submit-btn').disabled = false;
            document.getElementById('submit-btn').textContent = 'Save Location';
            
            if (res.data.success) {
                showAlert('success', res.data.message);
                closeModal();
                fetchLocations();
            } else {
                showAlert('error', res.data.message);
            }
        })
        .catch(err => {
            document.getElementById('submit-btn').disabled = false;
            document.getElementById('submit-btn').textContent = 'Save Location';
            console.error(err);
            let msg = 'Error validating form input.';
            if (err.response && err.response.data) {
                if (err.response.data.errors) {
                    msg = Object.values(err.response.data.errors).map(e => e.join(', ')).join('<br>');
                } else if (err.response.data.message) {
                    msg = err.response.data.message;
                }
            }
            showAlert('error', msg);
        });
    }

    // Alert helper
    function showAlert(type, message) {
        const alert = document.getElementById('map-alert');
        alert.className = `p-4 rounded-2xl text-xs font-semibold flex items-center gap-2 shadow-sm border mb-4`;
        
        if (type === 'success') {
            alert.classList.add('bg-emerald-50', 'border-emerald-100', 'text-emerald-700');
            alert.innerHTML = `<i class="bi bi-check-circle-fill"></i> ${message}`;
        } else if (type === 'error') {
            alert.classList.add('bg-rose-50', 'border-rose-100', 'text-rose-700');
            alert.innerHTML = `<i class="bi bi-exclamation-circle-fill"></i> ${message}`;
        } else {
            alert.classList.add('bg-blue-50', 'border-blue-100', 'text-blue-700');
            alert.innerHTML = `<i class="bi bi-info-circle-fill"></i> ${message}`;
        }

        alert.classList.remove('hidden');
        setTimeout(() => alert.classList.add('hidden'), 5000);
    }
</script>
@endpush
