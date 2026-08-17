@extends('layouts.partner')

@section('title', 'Add Location')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="flex items-center gap-3">
        <a href="{{ route('partner.locations.index') }}" class="w-8 h-8 rounded-lg bg-white border border-slate-200 flex items-center justify-center text-slate-600 hover:text-slate-900 transition-all">
            <i class="bi bi-arrow-left"></i>
        </a>
        <div>
            <h2 class="text-xl font-extrabold tracking-tight text-slate-900">Add New Location</h2>
            <p class="text-xs text-slate-500 mt-0.5">Submit details to register your venue for approval.</p>
        </div>
    </div>

    <form method="POST" action="{{ route('partner.locations.store') }}" enctype="multipart/form-data" class="bg-white border border-slate-200 rounded-[32px] p-6 sm:p-8 space-y-8">
        @csrf

        <!-- Basic Details Section -->
        <div class="space-y-4">
            <h3 class="font-bold text-slate-800 text-sm border-b border-slate-100 pb-2">Basic Details</h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <!-- Location Name -->
                <div class="space-y-1.5 col-span-2 sm:col-span-1">
                    <label for="name" class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider">Venue / Location Name <span class="text-rose-500">*</span></label>
                    <input type="text" id="name" name="name" value="{{ old('name') }}" required placeholder="e.g. Phoenix Marketcity Mall" class="w-full px-4 py-2.5 bg-white border border-slate-200 rounded-xl text-xs text-slate-900 focus:outline-none focus:border-blue-600 focus:ring-1 focus:ring-blue-600 transition-all">
                    @error('name') <p class="text-xxs text-rose-455 font-bold mt-1">{{ $message }}</p> @enderror
                </div>
                
                <!-- Business Name -->
                <div class="space-y-1.5 col-span-2 sm:col-span-1">
                    <label for="business_name" class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider">Business / Brand Name</label>
                    <input type="text" id="business_name" name="business_name" value="{{ old('business_name') }}" placeholder="e.g. Phoenix Malls" class="w-full px-4 py-2.5 bg-white border border-slate-200 rounded-xl text-xs text-slate-900 focus:outline-none focus:border-blue-600 focus:ring-1 focus:ring-blue-600 transition-all">
                </div>

                <!-- Category -->
                <div class="space-y-1.5 col-span-2">
                    <label for="category_id" class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider">Category <span class="text-rose-500">*</span></label>
                    <select id="category_id" name="category_id" required class="w-full px-4 py-2.5 bg-white border border-slate-200 rounded-xl text-xs text-slate-900 focus:outline-none focus:border-blue-600 transition-all">
                        <option value="">Select Category</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                        @endforeach
                    </select>
                    @error('category_id') <p class="text-xxs text-rose-455 font-bold mt-1">{{ $message }}</p> @enderror
                </div>
            </div>
        </div>

        <!-- Address & Coordinates Section -->
        <div class="space-y-4">
            <h3 class="font-bold text-slate-800 text-sm border-b border-slate-100 pb-2">Address & Coordinates</h3>
            
            <!-- Map Search -->
            <div class="space-y-1.5 relative z-50">
                <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider">Search Map to Pin Location</label>
                <div class="flex gap-2">
                    <input type="text" id="mapSearchInput" placeholder="Search by name or address..." class="w-full px-4 py-2.5 bg-white border border-slate-200 rounded-xl text-xs text-slate-900 focus:outline-none focus:border-blue-600 focus:ring-1 focus:ring-blue-600 transition-all">
                    <button type="button" id="mapSearchBtn" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold rounded-xl text-xs transition-colors border border-slate-200">Search</button>
                </div>
                <ul id="searchResults" class="hidden absolute top-full left-0 w-full mt-1 max-h-40 overflow-y-auto bg-white border border-slate-200 rounded-xl shadow-lg z-50"></ul>
            </div>

            <!-- Map -->
            <div class="space-y-2 relative z-0">
                <div id="pickerMap" class="w-full h-64 rounded-xl border border-slate-200 bg-slate-50 overflow-hidden relative z-0"></div>
                <div class="flex justify-between items-center mt-1">
                    <span class="text-[10px] text-slate-500"><i class="bi bi-info-circle"></i> Drag marker or search to auto-fill address</span>
                    <button type="button" id="toggle-manual-coords" class="text-[10px] font-bold text-blue-600 hover:underline">Override Manually</button>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 mt-4">
                <!-- Address -->
                <div class="space-y-1.5 col-span-2">
                    <label for="address" class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider">Full Address <span class="text-rose-500">*</span></label>
                    <textarea id="address" name="address" required rows="2" class="w-full px-4 py-2.5 bg-white border border-slate-200 rounded-xl text-xs text-slate-900 focus:outline-none focus:border-blue-600 focus:ring-1 focus:ring-blue-600 transition-all">{{ old('address') }}</textarea>
                    @error('address') <p class="text-xxs text-rose-455 font-bold mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- City -->
                <div class="space-y-1.5">
                    <label for="city" class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider">City <span class="text-rose-500">*</span></label>
                    <input type="text" id="city" name="city" value="{{ old('city') }}" required class="w-full px-4 py-2.5 bg-white border border-slate-200 rounded-xl text-xs text-slate-900 focus:outline-none focus:border-blue-600 focus:ring-1 focus:ring-blue-600 transition-all">
                </div>

                <!-- State -->
                <div class="space-y-1.5">
                    <label for="state" class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider">State <span class="text-rose-500">*</span></label>
                    <input type="text" id="state" name="state" value="{{ old('state') }}" required class="w-full px-4 py-2.5 bg-white border border-slate-200 rounded-xl text-xs text-slate-900 focus:outline-none focus:border-blue-600 focus:ring-1 focus:ring-blue-600 transition-all">
                </div>

                <!-- Postal Code -->
                <div class="space-y-1.5">
                    <label for="postal_code" class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider">Postal Code <span class="text-rose-500">*</span></label>
                    <input type="text" id="postal_code" name="postal_code" value="{{ old('postal_code') }}" required class="w-full px-4 py-2.5 bg-white border border-slate-200 rounded-xl text-xs text-slate-900 focus:outline-none focus:border-blue-600 focus:ring-1 focus:ring-blue-600 transition-all">
                </div>
                
                <!-- Latitude -->
                <div class="space-y-1.5">
                    <label for="latitude" class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider">Latitude <span class="text-rose-500">*</span></label>
                    <input type="text" id="latitude" name="latitude" value="{{ old('latitude') }}" readonly required class="w-full px-4 py-2.5 bg-slate-50 text-slate-500 border border-slate-200 rounded-xl text-xs focus:outline-none transition-all">
                </div>

                <!-- Longitude -->
                <div class="space-y-1.5">
                    <label for="longitude" class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider">Longitude <span class="text-rose-500">*</span></label>
                    <input type="text" id="longitude" name="longitude" value="{{ old('longitude') }}" readonly required class="w-full px-4 py-2.5 bg-slate-50 text-slate-500 border border-slate-200 rounded-xl text-xs focus:outline-none transition-all">
                </div>
            </div>
        </div>

        <!-- Commercials & Audience -->
        <div class="space-y-4">
            <h3 class="font-bold text-slate-800 text-sm border-b border-slate-100 pb-2">Commercials & Audience</h3>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
                <!-- Price -->
                <div class="space-y-1.5">
                    <label for="price_per_day" class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider">Price Per Day (₹) <span class="text-rose-500">*</span></label>
                    <input type="number" step="0.01" id="price_per_day" name="price_per_day" value="{{ old('price_per_day') }}" required min="0" class="w-full px-4 py-2.5 bg-white border border-slate-200 rounded-xl text-xs text-slate-900 focus:outline-none focus:border-blue-600 focus:ring-1 focus:ring-blue-600 transition-all">
                </div>

                <!-- Audience Count -->
                <div class="space-y-1.5">
                    <label for="audience_count" class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider">Monthly Audience</label>
                    <input type="number" id="audience_count" name="audience_count" value="{{ old('audience_count') }}" min="0" class="w-full px-4 py-2.5 bg-white border border-slate-200 rounded-xl text-xs text-slate-900 focus:outline-none focus:border-blue-600 focus:ring-1 focus:ring-blue-600 transition-all">
                </div>

                <!-- Legacy Daily Footfall -->
                <div class="space-y-1.5">
                    <label for="daily_footfall" class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider">Daily Footfall (Legacy) <span class="text-rose-500">*</span></label>
                    <input type="number" id="daily_footfall" name="daily_footfall" value="{{ old('daily_footfall', 0) }}" required min="0" class="w-full px-4 py-2.5 bg-white border border-slate-200 rounded-xl text-xs text-slate-900 focus:outline-none focus:border-blue-600 focus:ring-1 focus:ring-blue-600 transition-all">
                </div>
            </div>

            <div class="space-y-2 mt-4">
                <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider">Audience Types</label>
                <div class="flex flex-wrap gap-3">
                    @foreach(['Male', 'Female', 'Family', 'Kids', 'Students', 'Professionals', 'Mixed Audience'] as $type)
                    <label class="flex items-center gap-2">
                        <input type="checkbox" name="audience_type[]" value="{{ $type }}" class="rounded text-blue-600 focus:ring-blue-600" {{ is_array(old('audience_type')) && in_array($type, old('audience_type')) ? 'checked' : '' }}>
                        <span class="text-xs text-slate-700">{{ $type }}</span>
                    </label>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Operations & Screens -->
        <div class="space-y-4">
            <h3 class="font-bold text-slate-800 text-sm border-b border-slate-100 pb-2">Operations & Screen Details</h3>
            
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <!-- Operating Hours -->
                <div class="space-y-1.5">
                    <label for="operating_hours" class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider">Operating Hours</label>
                    <input type="text" id="operating_hours" name="operating_hours" value="{{ old('operating_hours') }}" placeholder="e.g. 10:00 AM - 10:00 PM" class="w-full px-4 py-2.5 bg-white border border-slate-200 rounded-xl text-xs text-slate-900 focus:outline-none focus:border-blue-600 focus:ring-1 focus:ring-blue-600 transition-all">
                </div>
                
                <div class="space-y-1.5">
                    <label for="repeats_per_day" class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider">Ad Repeats Per Day</label>
                    <input type="number" id="repeats_per_day" name="repeats_per_day" value="{{ old('repeats_per_day') }}" min="0" class="w-full px-4 py-2.5 bg-white border border-slate-200 rounded-xl text-xs text-slate-900 focus:outline-none focus:border-blue-600 focus:ring-1 focus:ring-blue-600 transition-all">
                </div>

                <div class="space-y-1.5">
                    <label for="opening_time" class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider">Opening Time</label>
                    <input type="time" id="opening_time" name="opening_time" value="{{ old('opening_time') }}" class="w-full px-4 py-2.5 bg-white border border-slate-200 rounded-xl text-xs text-slate-900 focus:outline-none focus:border-blue-600 focus:ring-1 focus:ring-blue-600 transition-all">
                </div>
                
                <div class="space-y-1.5">
                    <label for="closing_time" class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider">Closing Time</label>
                    <input type="time" id="closing_time" name="closing_time" value="{{ old('closing_time') }}" class="w-full px-4 py-2.5 bg-white border border-slate-200 rounded-xl text-xs text-slate-900 focus:outline-none focus:border-blue-600 focus:ring-1 focus:ring-blue-600 transition-all">
                </div>
            </div>

            <div class="space-y-2 mt-4">
                <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider">Operating Days</label>
                <div class="flex flex-wrap gap-3">
                    @foreach(['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'] as $day)
                    <label class="flex items-center gap-2">
                        <input type="checkbox" name="operating_days[]" value="{{ $day }}" class="rounded text-blue-600 focus:ring-blue-600" {{ is_array(old('operating_days')) && in_array($day, old('operating_days')) ? 'checked' : (old('operating_days') === null ? 'checked' : '') }}>
                        <span class="text-xs text-slate-700">{{ $day }}</span>
                    </label>
                    @endforeach
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 mt-4">
                <div class="space-y-1.5">
                    <label for="screen_size" class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider">Screen Size (e.g. 55 inch)</label>
                    <input type="text" id="screen_size" name="screen_size" value="{{ old('screen_size') }}" class="w-full px-4 py-2.5 bg-white border border-slate-200 rounded-xl text-xs text-slate-900 focus:outline-none focus:border-blue-600 transition-all">
                </div>
                
                <div class="space-y-1.5">
                    <label for="screen_orientation" class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider">Screen Orientation</label>
                    <select id="screen_orientation" name="screen_orientation" class="w-full px-4 py-2.5 bg-white border border-slate-200 rounded-xl text-xs text-slate-900 focus:outline-none focus:border-blue-600 transition-all">
                        <option value="Landscape" {{ old('screen_orientation') == 'Landscape' ? 'selected' : '' }}>Landscape</option>
                        <option value="Portrait" {{ old('screen_orientation') == 'Portrait' ? 'selected' : '' }}>Portrait</option>
                    </select>
                </div>
            </div>
            
            <div class="flex gap-6 mt-2">
                <label class="flex items-center gap-2">
                    <input type="hidden" name="video_supported" value="0">
                    <input type="checkbox" name="video_supported" value="1" class="rounded text-blue-600 focus:ring-blue-600" {{ old('video_supported', 1) ? 'checked' : '' }}>
                    <span class="text-xs font-bold text-slate-700">Video Supported</span>
                </label>
                <label class="flex items-center gap-2">
                    <input type="hidden" name="audio_supported" value="0">
                    <input type="checkbox" name="audio_supported" value="1" class="rounded text-blue-600 focus:ring-blue-600" {{ old('audio_supported', 0) ? 'checked' : '' }}>
                    <span class="text-xs font-bold text-slate-700">Audio Supported</span>
                </label>
            </div>
        </div>

        <!-- Location Descriptions -->
        <div class="space-y-4">
            <h3 class="font-bold text-slate-800 text-sm border-b border-slate-100 pb-2">Additional Info</h3>
            
            <div class="space-y-1.5">
                <label for="description" class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider">Venue Description</label>
                <textarea id="description" name="description" rows="2" placeholder="Describe the type of visitors, premium spots..." class="w-full px-4 py-2.5 bg-white border border-slate-200 rounded-xl text-xs text-slate-900 focus:outline-none focus:border-blue-600 focus:ring-1 focus:ring-blue-600 transition-all">{{ old('description') }}</textarea>
            </div>
            
            <div class="space-y-1.5">
                <label for="nearby_places" class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider">Nearby Landmarks / Businesses</label>
                <textarea id="nearby_places" name="nearby_places" rows="2" placeholder="e.g. Central Station, KFC, Starbucks..." class="w-full px-4 py-2.5 bg-white border border-slate-200 rounded-xl text-xs text-slate-900 focus:outline-none focus:border-blue-600 focus:ring-1 focus:ring-blue-600 transition-all">{{ old('nearby_places') }}</textarea>
            </div>
        </div>

        <!-- Image Upload -->
        <div class="space-y-4">
            <h3 class="font-bold text-slate-800 text-sm border-b border-slate-100 pb-2">Media</h3>
            
            <div class="space-y-1.5">
                <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider">Upload Venue Images</label>
                <div class="w-full py-6 bg-slate-50 border border-dashed border-slate-300 rounded-xl flex flex-col items-center justify-center gap-2 relative hover:border-blue-400 transition-all cursor-pointer group">
                    <i class="bi bi-cloud-arrow-up text-3xl text-slate-300 group-hover:text-blue-500"></i>
                    <span class="text-xs text-slate-500 font-medium">Click or drag images here</span>
                    <input type="file" name="images[]" id="images" multiple accept="image/*" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer">
                </div>
                <div id="image-previews-container" class="grid grid-cols-4 gap-3 mt-3"></div>
                <p class="text-[10px] text-slate-500 mt-1">Upload premium resolution images (JPG, PNG, WEBP. Max 5MB per file).</p>
                @error('images') <p class="text-xxs text-rose-455 font-bold mt-1">{{ $message }}</p> @enderror
            </div>
        </div>

        <div class="flex items-center justify-end gap-3 pt-6 border-t border-slate-200">
            <a href="{{ route('partner.locations.index') }}" class="px-5 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-xs font-bold text-slate-600 transition-all border border-slate-200">Cancel</a>
            <button type="submit" class="px-5 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-500 text-xs font-bold text-white transition-all shadow-lg shadow-blue-500/20">Submit for Approval</button>
        </div>
    </form>
</div>
@endsection

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
@endpush

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Pre-loaded Coords
        const initialLat = {{ old('latitude') ?? 20.5937 }};
        const initialLng = {{ old('longitude') ?? 78.9629 }};
        const locationLatLng = [initialLat, initialLng];

        // Init Map
        const map = L.map('pickerMap', { zoomControl: true }).setView(locationLatLng, 5);

        L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright" target="_blank">OpenStreetMap</a> contributors'
        }).addTo(map);

        let selectedMarker = null;

        function setMarker(lat, lng) {
            const latlng = [lat, lng];
            map.setView(latlng, 15);
            if (selectedMarker) {
                selectedMarker.setLatLng(latlng);
            } else {
                selectedMarker = L.marker(latlng, { draggable: true }).addTo(map);
                selectedMarker.on('dragend', function(evt) {
                    const markerLat = evt.target.getLatLng().lat.toFixed(8);
                    const markerLng = evt.target.getLatLng().lng.toFixed(8);
                    document.getElementById('latitude').value = markerLat;
                    document.getElementById('longitude').value = markerLng;
                });
            }
        }

        // Map Click reposition
        map.on('click', function(e) {
            const lat = e.latlng.lat.toFixed(8);
            const lng = e.latlng.lng.toFixed(8);
            document.getElementById('latitude').value = lat;
            document.getElementById('longitude').value = lng;
            setMarker(lat, lng);
        });

        // Search logic using Nominatim
        const searchInput = document.getElementById('mapSearchInput');
        const searchBtn = document.getElementById('mapSearchBtn');
        const searchResults = document.getElementById('searchResults');

        if(searchBtn) {
            searchBtn.addEventListener('click', function() {
                const query = searchInput.value;
                if(!query) return;

                fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(query)}&addressdetails=1`)
                    .then(res => res.json())
                    .then(data => {
                        searchResults.innerHTML = '';
                        if(data.length === 0) {
                            searchResults.innerHTML = '<li class="p-3 text-xs text-slate-500">No results found</li>';
                        } else {
                            data.forEach(item => {
                                const li = document.createElement('li');
                                li.className = 'p-3 hover:bg-slate-50 cursor-pointer border-b border-slate-100 text-xs text-slate-700 transition-colors';
                                li.textContent = item.display_name;
                                li.addEventListener('click', () => {
                                    setMarker(item.lat, item.lon);
                                    document.getElementById('latitude').value = item.lat;
                                    document.getElementById('longitude').value = item.lon;
                                    
                                    const addr = item.address;
                                    if(addr) {
                                        document.getElementById('address').value = (addr.road || addr.suburb || addr.neighbourhood || '') + (addr.house_number ? ' ' + addr.house_number : '');
                                        document.getElementById('city').value = addr.city || addr.town || addr.village || addr.county || '';
                                        document.getElementById('state').value = addr.state || '';
                                        document.getElementById('postal_code').value = addr.postcode || '';
                                    }

                                    searchResults.classList.add('hidden');
                                    searchInput.value = item.display_name;
                                });
                                searchResults.appendChild(li);
                            });
                        }
                        searchResults.classList.remove('hidden');
                    });
            });
        }

        // Toggle Manual Coordinates Override
        let manualOverride = false;
        const toggleBtn = document.getElementById('toggle-manual-coords');
        const latInput = document.getElementById('latitude');
        const lngInput = document.getElementById('longitude');

        toggleBtn.addEventListener('click', function() {
            manualOverride = !manualOverride;
            if (manualOverride) {
                latInput.removeAttribute('readonly');
                lngInput.removeAttribute('readonly');
                latInput.classList.remove('bg-slate-50', 'text-slate-500');
                lngInput.classList.remove('bg-slate-50', 'text-slate-500');
                latInput.classList.add('bg-white', 'text-slate-900');
                lngInput.classList.add('bg-white', 'text-slate-900');
                toggleBtn.textContent = 'Lock Coordinates';
            } else {
                latInput.setAttribute('readonly', 'true');
                lngInput.setAttribute('readonly', 'true');
                latInput.classList.add('bg-slate-50', 'text-slate-500');
                lngInput.classList.add('bg-slate-50', 'text-slate-500');
                latInput.classList.remove('bg-white', 'text-slate-900');
                lngInput.classList.remove('bg-white', 'text-slate-900');
                toggleBtn.textContent = 'Override Manually';
            }
        });

        // Reposition Pin if fields manual updated
        const updateMarkerFromInputs = () => {
            const latVal = parseFloat(latInput.value);
            const lngVal = parseFloat(lngInput.value);
            if (!isNaN(latVal) && !isNaN(lngVal)) {
                setMarker(latVal, lngVal);
            }
        };

        latInput.addEventListener('input', updateMarkerFromInputs);
        lngInput.addEventListener('input', updateMarkerFromInputs);

        // Images Preview handler
        const imagesInput = document.getElementById('images');
        const previewContainer = document.getElementById('image-previews-container');

        imagesInput.addEventListener('change', function() {
            previewContainer.innerHTML = '';
            const files = Array.from(this.files);

            files.forEach((file) => {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const div = document.createElement('div');
                    div.className = 'relative w-full aspect-square bg-slate-50 border border-slate-200 rounded-lg overflow-hidden';
                    div.innerHTML = `<img src="${e.target.result}" class="w-full h-full object-cover">`;
                    previewContainer.appendChild(div);
                };
                reader.readAsDataURL(file);
            });
        });
    });
</script>
@endpush
