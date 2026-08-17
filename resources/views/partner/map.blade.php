@extends('layouts.partner')

@section('title', 'Locations Map Grid | Partner Portal')

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<style>
    #partner-map-container {
        height: 700px;
        position: relative;
    }
    #partnerMap {
        height: 100%;
        width: 100%;
        border-radius: 24px;
        background: #0f172a;
    }
</style>
@endpush

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h2 class="text-xl font-extrabold tracking-tight text-slate-900 flex items-center gap-2">
                <i class="bi bi-map text-emerald-500"></i> Locations Map Grid
            </h2>
            <p class="text-xs text-slate-500 mt-0.5">Partner interactive map. Click on the map or search an address to request a new location addition, or click your pins to request edits.</p>
        </div>
        <div class="flex gap-2">
            <button onclick="openManualRequestCreateModal()" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-bold transition-all flex items-center gap-1.5 shadow-md">
                <i class="bi bi-geo-alt"></i> Manual Location Creation
            </button>
            <button onclick="triggerSearchMode()" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-bold transition-all flex items-center gap-1.5 shadow-md">
                <i class="bi bi-plus-lg"></i> Request New Location
            </button>
            <a href="{{ route('partner.locations.index') }}" class="px-4 py-2 bg-slate-100 hover:bg-slate-750 text-slate-600 rounded-xl text-xs font-bold transition-all flex items-center gap-1.5 border border-slate-200">
                <i class="bi bi-list-task"></i> View Locations List
            </a>
        </div>
    </div>

    <!-- Feedback Alerts -->
    <div id="map-alert" class="hidden p-4 rounded-2xl text-xs font-semibold flex items-center gap-2 shadow-sm border border-slate-200 bg-slate-50 text-slate-600 mb-4"></div>

    <!-- Map Canvas Card -->
    <div class="bg-white border border-slate-200 p-4 rounded-[32px] relative overflow-hidden" id="partner-map-container">
        <!-- Nominatim Search Overlay -->
        <div class="absolute top-8 left-8 z-[1000] w-96 flex flex-col gap-2">
            <div class="bg-white/90 backdrop-blur-md p-2 rounded-2xl shadow-2xl border border-slate-200 flex items-center gap-2">
                <i class="bi bi-search text-slate-500 ms-2"></i>
                <input type="text" id="map-search-input" placeholder="Search location or address..." class="w-full bg-transparent border-0 outline-none focus:ring-0 text-xs text-slate-700 py-1">
                <button onclick="performMapSearch()" class="px-3 py-1.5 bg-emerald-650 text-slate-900 text-[10px] font-bold rounded-xl hover:bg-emerald-600 transition-all">Search</button>
            </div>
            <div id="search-results-dropdown" class="hidden bg-white border border-slate-200 rounded-2xl shadow-2xl max-h-60 overflow-y-auto flex flex-col divide-y divide-slate-800"></div>
        </div>

        <div id="partnerMap" class="shadow-2xl"></div>

        <!-- Float Map Legend -->
        <div class="absolute bottom-8 left-8 bg-white/90 backdrop-blur-md px-4 py-3 rounded-2xl border border-slate-200 shadow-md flex flex-col gap-2 z-20 text-[11px]">
            <span class="font-bold text-slate-600 border-b border-slate-200 pb-1.5 mb-0.5">Status Legend</span>
            <div class="flex items-center gap-2">
                <span class="w-3 h-3 bg-emerald-500 rounded-full border border-white/20"></span>
                <span class="text-slate-500 font-medium">Active / Approved</span>
            </div>
            <div class="flex items-center gap-2">
                <span class="w-3 h-3 bg-amber-500 rounded-full border border-white/20"></span>
                <span class="text-slate-500 font-medium">Pending Review</span>
            </div>
            <div class="flex items-center gap-2">
                <span class="w-3 h-3 bg-rose-500 rounded-full border border-white/20"></span>
                <span class="text-slate-500 font-medium">Rejected / Inactive</span>
            </div>
        </div>
    </div>
</div>

<!-- Modal for Request New / Edit Location -->
<div id="locationModal" class="fixed inset-0 bg-white/80 backdrop-blur-sm z-[9999] hidden flex items-center justify-center p-4 overflow-y-auto">
    <div class="bg-white rounded-[32px] w-full max-w-2xl overflow-hidden shadow-2xl border border-slate-200 flex flex-col max-h-[90vh]">
        <!-- Modal Header -->
        <div class="flex items-center justify-between px-6 py-4 border-b border-slate-200">
            <h3 class="text-sm font-bold text-slate-900" id="modal-title">Request Location Addition</h3>
            <button onclick="closeModal()" class="w-8 h-8 rounded-full bg-slate-100 text-slate-500 hover:text-slate-900 flex items-center justify-center border-none">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>

        <!-- Modal Body (Form scrollable) -->
        <form id="location-map-form" class="overflow-y-auto flex-grow p-6 space-y-4" enctype="multipart/form-data">
            @csrf
            <input type="hidden" id="form-location-id" name="id">
            <input type="hidden" id="form-method" name="_method" value="POST">
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Location Name -->
                <div>
                    <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1.5">Location Name *</label>
                    <input type="text" id="form-name" name="name" required class="w-full bg-white border-slate-200 focus:border-emerald-500 focus:ring-emerald-500 rounded-xl px-4 py-2.5 text-xs text-slate-900">
                </div>

                <!-- Price Per Day -->
                <div>
                    <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1.5">Advertising Price per day (₹) *</label>
                    <input type="number" step="0.01" min="0" id="form-price" name="price_per_day" required class="w-full bg-white border-slate-200 focus:border-emerald-500 focus:ring-emerald-500 rounded-xl px-4 py-2.5 text-xs text-slate-900 font-mono">
                </div>

                <!-- Category -->
                <div>
                    <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1.5">Category *</label>
                    <select id="form-category" name="category_id" required class="w-full bg-white border-slate-200 focus:border-emerald-500 focus:ring-emerald-500 rounded-xl px-4 py-2.5 text-xs text-slate-900">
                        <option value="">Select Category</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Operating Hours -->
                <div class="md:col-span-2">
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">Operating Hours</label>
                    <div class="flex items-center gap-2">
                        <input type="time" id="form-hours-open" class="flex-1 bg-white border-slate-200 rounded-xl px-4 py-2.5 text-xs text-slate-900">
                        <span class="text-slate-500 font-bold text-xs uppercase">To</span>
                        <input type="time" id="form-hours-close" class="flex-1 bg-white border-slate-200 rounded-xl px-4 py-2.5 text-xs text-slate-900">
                        <input type="hidden" id="form-hours" name="operating_hours">
                    </div>
                </div>
            </div>

            <!-- Address and Geolocation details -->
            <div class="border-t border-slate-200 pt-4 space-y-4">
                <h4 class="text-xs font-bold text-slate-900">Address Details (Set via Map Click)</h4>
                
                <div>
                    <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1.5">Street Address *</label>
                    <input type="text" id="form-address" name="address" required class="w-full bg-white border-slate-200 focus:border-emerald-500 focus:ring-emerald-500 rounded-xl px-4 py-2.5 text-xs text-slate-900">
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1.5">City *</label>
                        <input type="text" id="form-city" name="city" required class="w-full bg-white border-slate-200 focus:border-emerald-500 focus:ring-emerald-500 rounded-xl px-4 py-2.5 text-xs text-slate-900">
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1.5">State *</label>
                        <input type="text" id="form-state" name="state" required class="w-full bg-white border-slate-200 focus:border-emerald-500 focus:ring-emerald-500 rounded-xl px-4 py-2.5 text-xs text-slate-900">
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1.5">Postal Code *</label>
                        <input type="text" id="form-zip" name="postal_code" required class="w-full bg-white border-slate-200 focus:border-emerald-500 focus:ring-emerald-500 rounded-xl px-4 py-2.5 text-xs text-slate-900">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1.5">Latitude *</label>
                        <input type="number" step="any" id="form-lat" name="latitude" required onchange="updatePinFromInputs()" class="w-full bg-slate-100 border-slate-200 rounded-xl px-4 py-2.5 text-xs text-slate-600 font-mono">
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1.5">Longitude *</label>
                        <input type="number" step="any" id="form-lng" name="longitude" required onchange="updatePinFromInputs()" class="w-full bg-slate-100 border-slate-200 rounded-xl px-4 py-2.5 text-xs text-slate-600 font-mono">
                    </div>
                </div>
                <div class="mt-2 text-right">
                    <button type="button" onclick="locateOnMap()" class="px-4 py-2 bg-slate-800 text-white hover:bg-slate-700 rounded-xl text-xs font-bold transition-all shadow-sm">
                        <i class="bi bi-crosshair"></i> Locate on Map
                    </button>
                </div>
            </div>

            <!-- Description -->
            <div>
                <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1.5">Description</label>
                <textarea id="form-description" name="description" rows="3" class="w-full bg-white border-slate-200 focus:border-emerald-500 focus:ring-emerald-500 rounded-xl px-4 py-2.5 text-xs text-slate-900"></textarea>
            </div>

            <!-- Photos upload -->
            <div>
                <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1.5">Upload Location Photos</label>
                <input type="file" id="form-images" name="images[]" multiple accept="image/*" class="w-full text-xs text-slate-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-slate-100 file:text-emerald-400 hover:file:bg-slate-750">
                <span class="block text-[9px] text-slate-500 mt-1">Select multiple image files. Max size 5MB per file. Changes will require admin approval.</span>
            </div>

            <!-- Edit photos preview/delete -->
            <div id="image-edit-gallery" class="hidden border-t border-slate-200 pt-4">
                <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1.5">Existing Photos (Check to Request Delete)</label>
                <div id="modal-gallery-grid" class="grid grid-cols-4 gap-3"></div>
            </div>
        </form>

        <!-- Modal Footer -->
        <div class="px-6 py-4 border-t border-slate-200 flex items-center justify-end gap-3 bg-white/50">
            <button onclick="closeModal()" type="button" class="px-4 py-2 bg-slate-100 hover:bg-slate-700 text-slate-600 rounded-xl text-xs font-bold border-none cursor-pointer">Cancel</button>
            <button onclick="submitLocationForm()" id="submit-btn" type="button" class="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-500 text-slate-900 rounded-xl text-xs font-bold shadow-md shadow-emerald-500/10 cursor-pointer border-none">Submit Request</button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script>
    let map;
    let markers = [];
    let tempMarker = null;

    document.addEventListener('DOMContentLoaded', function () {
        const coimbatoreCenter = [11.0183, 76.9725];
        const defaultZoom = 13;

        // Initialize Map
        map = L.map('partnerMap', {
            zoomControl: true
        }).setView(coimbatoreCenter, defaultZoom);

        // OpenStreetMap Light Theme Tiles (Matches Admin)
        L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
            maxZoom: 20
        }).addTo(map);

        // Load partner locations
        fetchLocations();

        // Click Map Listener to drop marker and trigger request modal
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
        const locations = @json($locations);
        
        // Clear old markers
        markers.forEach(m => map.removeLayer(m));
        markers = [];
        
        let bounds = [];

        locations.forEach(location => {
            if (location.latitude && location.longitude) {
                const lat = parseFloat(location.latitude);
                const lng = parseFloat(location.longitude);
                bounds.push([lat, lng]);

                let markerColor = '#f59e0b'; // amber (pending)
                if (location.status === 'active' || location.status === 'approved') {
                    markerColor = '#10b981'; // emerald (active)
                } else if (location.status === 'rejected' || location.status === 'inactive') {
                    markerColor = '#f43f5e'; // rose (rejected/inactive)
                }

                // Create SVG marker icon
                const svgIcon = L.divIcon({
                    html: `<div style="
                        width: 14px;
                        height: 14px;
                        background-color: ${markerColor};
                        border: 2px solid #ffffff;
                        border-radius: 50%;
                        box-shadow: 0 0 8px ${markerColor};
                    "></div>`,
                    className: 'custom-map-marker',
                    iconSize: [14, 14],
                    iconAnchor: [7, 7]
                });

                const marker = L.marker([lat, lng], { icon: svgIcon }).addTo(map);
                markers.push(marker);

                // Popup contents
                const popupContent = `
                    <div style="font-size: 11px; padding: 4px; color: #cbd5e1; font-family: 'Outfit', sans-serif;">
                        <h4 style="font-weight: 800; font-size: 12px; margin: 0 0 6px 0; color: #ffffff;">${location.name}</h4>
                        <p style="margin: 0 0 8px 0; color: #94a3b8; font-family: monospace;">Price: ₹${location.price_per_day || 0}/day</p>
                        <p style="margin: 0 0 6px 0;"><i class="bi bi-geo-alt-fill text-emerald-500" style="margin-right: 4px;"></i>${location.city}</p>
                        <p style="margin: 0 0 6px 0;"><i class="bi bi-display" style="margin-right: 4px;"></i>Status: <strong>${location.status.toUpperCase()}</strong></p>
                        <div style="margin-top: 10px; border-top: 1px solid #334155; padding-top: 8px;">
                            <button onclick="editLocationRequest(${JSON.stringify(location).replace(/"/g, '&quot;')})" style="background: #10b981; border: none; color: #ffffff; padding: 6px 12px; border-radius: 6px; font-weight: bold; cursor: pointer; font-size: 10px; width: 100%;">
                                <i class="bi bi-pencil-square"></i> Request Updates
                            </button>
                        </div>
                    </div>
                `;
                marker.bindPopup(popupContent);
            }
        });

        if (bounds.length > 0) {
            map.fitBounds(bounds, { padding: [50, 50] });
        }
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

                    openRequestCreateModal(lat, lng);
                }
            })
            .catch(err => {
                console.error('Reverse geocode error:', err);
                openRequestCreateModal(lat, lng);
            });
    }

    // Open Modal for Create Request
    function openRequestCreateModal(lat, lng) {
        document.getElementById('form-location-id').value = '';
        document.getElementById('form-method').value = 'POST';
        document.getElementById('modal-title').textContent = 'Request Location Addition';
        document.getElementById('form-lat').value = lat.toFixed(6);
        document.getElementById('form-lng').value = lng.toFixed(6);
        document.getElementById('image-edit-gallery').classList.add('hidden');
        
        document.getElementById('locationModal').classList.remove('hidden');
    }

    // Open Modal for Manual Request Create
    function openManualRequestCreateModal() {
        document.getElementById('location-map-form').reset();
        document.getElementById('form-location-id').value = '';
        document.getElementById('form-method').value = 'POST';
        document.getElementById('modal-title').textContent = 'Request Location Addition Manually';
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

        // Temporarily hide modal so partner can verify the map location
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

    // Open Modal for Edit Request
    function editLocationRequest(location) {
        document.getElementById('location-map-form').reset();
        
        document.getElementById('modal-title').textContent = `Request Updates for: ${location.name}`;
        document.getElementById('form-location-id').value = location.id;
        document.getElementById('form-method').value = 'PUT'; // PUT method spoofing
        document.getElementById('form-name').value = location.name;
        document.getElementById('form-price').value = location.price_per_day || 0;
        document.getElementById('form-category').value = location.category_id || '';
        
        // Parse operating hours string (e.g. "09:00 AM - 10:00 PM") back to time inputs
        document.getElementById('form-hours-open').value = '';
        document.getElementById('form-hours-close').value = '';
        if (location.operating_hours && location.operating_hours.includes('-')) {
            const parts = location.operating_hours.split('-');
            const parseTime = (t) => {
                t = t.trim();
                const match = t.match(/(\d+):(\d+)\s*(AM|PM)/i);
                if (match) {
                    let h = parseInt(match[1]);
                    const m = match[2];
                    const ampm = match[3].toUpperCase();
                    if (ampm === 'PM' && h < 12) h += 12;
                    if (ampm === 'AM' && h === 12) h = 0;
                    return h.toString().padStart(2, '0') + ':' + m;
                }
                return t;
            };
            document.getElementById('form-hours-open').value = parseTime(parts[0]);
            document.getElementById('form-hours-close').value = parseTime(parts[1]);
        }
        document.getElementById('form-address').value = location.address;
        document.getElementById('form-city').value = location.city;
        document.getElementById('form-state').value = location.state || 'Tamil Nadu';
        document.getElementById('form-zip').value = location.postal_code;
        document.getElementById('form-lat').value = parseFloat(location.latitude).toFixed(6);
        document.getElementById('form-lng').value = parseFloat(location.longitude).toFixed(6);
        document.getElementById('form-description').value = location.description || '';

        // Load existing images if available
        if (location.images && location.images.length > 0) {
            document.getElementById('image-edit-gallery').classList.remove('hidden');
            const galleryGrid = document.getElementById('modal-gallery-grid');
            galleryGrid.innerHTML = '';

            location.images.forEach(img => {
                const item = document.createElement('div');
                item.className = 'relative group border border-slate-200 rounded-xl overflow-hidden bg-white flex flex-col items-center';
                item.innerHTML = `
                    <img src="/storage/${img.image_path}" class="w-full h-12 object-cover">
                    <label class="flex items-center gap-1.5 p-1 text-[9px] text-slate-500 cursor-pointer">
                        <input type="checkbox" name="delete_images[]" value="${img.id}"> Delete
                    </label>
                `;
                galleryGrid.appendChild(item);
            });
        } else {
            document.getElementById('image-edit-gallery').classList.add('hidden');
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
        dropdown.innerHTML = '<div class="p-3 text-xs text-slate-500 text-center"><i class="bi bi-hourglass-split animate-spin"></i> Searching...</div>';
        dropdown.classList.remove('hidden');

        axios.get(`https://nominatim.openstreetmap.org/search?q=${encodeURIComponent(input)}&format=json&limit=5&addressdetails=1`)
            .then(res => {
                dropdown.innerHTML = '';
                const results = res.data;
                if (results.length === 0) {
                    dropdown.innerHTML = '<div class="p-3 text-xs text-slate-500 text-center">No locations found. Click map directly.</div>';
                    return;
                }

                results.forEach(res => {
                    const row = document.createElement('button');
                    row.className = 'w-full text-left p-3 hover:bg-slate-100 text-xs text-slate-600 transition-all flex flex-col gap-0.5 border-none bg-transparent cursor-pointer';
                    row.innerHTML = `
                        <strong class="text-slate-900 truncate">${res.display_name}</strong>
                        <span class="text-[10px] text-slate-500 font-mono">${parseFloat(res.lat).toFixed(4)}, ${parseFloat(res.lon).toFixed(4)}</span>
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

    // Trigger search input focus
    function triggerSearchMode() {
        document.getElementById('map-search-input').focus();
        showAlert('info', 'Use the top-left search box to find an address, or click directly on the map.');
    }

    // Submit request via AJAX
    function submitLocationForm() {
        const form = document.getElementById('location-map-form');
        
        // Format operating hours before validation
        const openTime = document.getElementById('form-hours-open').value;
        const closeTime = document.getElementById('form-hours-close').value;
        const format12H = (t24) => {
            if (!t24) return '';
            const [h, m] = t24.split(':');
            const hh = parseInt(h, 10);
            const ampm = hh >= 12 ? 'PM' : 'AM';
            const h12 = hh % 12 || 12;
            return `${h12.toString().padStart(2, '0')}:${m} ${ampm}`;
        };
        if (openTime && closeTime) {
            document.getElementById('form-hours').value = `${format12H(openTime)} - ${format12H(closeTime)}`;
        } else {
            document.getElementById('form-hours').value = '';
        }

        if (!form.reportValidity()) {
            return;
        }

        const id = document.getElementById('form-location-id').value;
        const url = id ? `/partner/locations/${id}` : '/partner/locations';

        const formData = new FormData(form);

        document.getElementById('submit-btn').disabled = true;
        document.getElementById('submit-btn').innerHTML = '<i class="bi bi-hourglass-split animate-spin"></i> Submitting...';

        axios.post(url, formData, {
            headers: {
                'Content-Type': 'multipart/form-data',
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(res => {
            document.getElementById('submit-btn').disabled = false;
            document.getElementById('submit-btn').textContent = 'Submit Request';
            
            // Redirect to locations resource handler index
            window.location.href = "{{ route('partner.locations.index') }}";
        })
        .catch(err => {
            document.getElementById('submit-btn').disabled = false;
            document.getElementById('submit-btn').textContent = 'Submit Request';
            console.error(err);
            let msg = 'Error submitting location request.';
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
        alert.className = `p-4 rounded-2xl text-xs font-semibold flex items-center gap-2 shadow-sm border mb-4 bg-white border-slate-200 text-slate-600`;
        
        if (type === 'success') {
            alert.classList.add('border-emerald-900/30', 'text-emerald-400');
            alert.innerHTML = `<i class="bi bi-check-circle-fill text-emerald-500"></i> ${message}`;
        } else if (type === 'error') {
            alert.classList.add('border-rose-900/30', 'text-rose-400');
            alert.innerHTML = `<i class="bi bi-exclamation-circle-fill text-rose-500"></i> ${message}`;
        } else {
            alert.innerHTML = `<i class="bi bi-info-circle-fill text-blue-500"></i> ${message}`;
        }

        alert.classList.remove('hidden');
        setTimeout(() => alert.classList.add('hidden'), 5000);
    }
</script>
@endpush
