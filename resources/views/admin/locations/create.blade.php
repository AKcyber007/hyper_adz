@extends('admin.layouts.app', [
    'title' => 'Create Location | Hyper Adz Admin'
])

@section('content')
<div class="max-w-4xl space-y-6">
    <!-- Header -->
    <div class="flex items-center gap-3">
        <a href="{{ route('admin.locations.index') }}" class="w-9 h-9 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-600 dark:bg-slate-800 dark:hover:bg-slate-700 dark:text-slate-350 flex items-center justify-center transition-all" title="Back to Listing">
            <i class="bi bi-arrow-left"></i>
        </a>
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white">Add New Location</h1>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-0.5">Register a new screen advertising location in the network.</p>
        </div>
    </div>

    <!-- Create Form -->
    <form method="POST" action="{{ route('admin.locations.store') }}" enctype="multipart/form-data" class="space-y-6">
        @csrf

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Left 2 Cols: Form Fields -->
            <div class="lg:col-span-2 space-y-6">
                <!-- General Info Card -->
                <div class="bg-white dark:bg-slate-800 rounded-3xl p-6 border border-slate-100 dark:border-slate-700/50 shadow-sm space-y-4">
                    <h3 class="font-bold text-slate-800 dark:text-slate-100 text-base border-b border-slate-100 dark:border-slate-750 pb-3 flex items-center gap-2">
                        <i class="bi bi-info-circle text-[#1155CC]"></i> Basic Details
                    </h3>

                    <!-- Name -->
                    <div class="space-y-1.5">
                        <label for="name" class="text-xs font-bold text-slate-500 dark:text-slate-400">Location Name <span class="text-rose-500">*</span></label>
                        <input type="text" name="name" id="name" value="{{ old('name') }}" placeholder="e.g. Brookefields Mall" required class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl text-sm text-slate-700 dark:text-slate-250 placeholder-slate-400 focus:outline-none focus:border-[#1155CC] @error('name') border-rose-550 @enderror">
                        @error('name') <span class="text-xs text-rose-600 block mt-1">{{ $message }}</span> @enderror
                    </div>

                    <!-- Business Name -->
                    <div class="space-y-1.5">
                        <label for="business_name" class="text-xs font-bold text-slate-500 dark:text-slate-400">Business / Brand Name</label>
                        <input type="text" name="business_name" id="business_name" value="{{ old('business_name') }}" placeholder="e.g. Hyper Adz Local" class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl text-sm text-slate-700 dark:text-slate-250 placeholder-slate-400 focus:outline-none focus:border-[#1155CC]">
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Category -->
                        <div class="space-y-1.5">
                            <label for="category_id" class="text-xs font-bold text-slate-500 dark:text-slate-400">Category <span class="text-rose-500">*</span></label>
                            <select name="category_id" id="category_id" required class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl text-sm text-slate-700 dark:text-slate-250 focus:outline-none focus:border-[#1155CC] @error('category_id') border-rose-550 @enderror">
                                <option value="">Select Category</option>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                                @endforeach
                            </select>
                            @error('category_id') <span class="text-xs text-rose-600 block mt-1">{{ $message }}</span> @enderror
                        </div>

                        <!-- Status -->
                        <div class="space-y-1.5">
                            <label for="status" class="text-xs font-bold text-slate-500 dark:text-slate-400">Status <span class="text-rose-500">*</span></label>
                            <select name="status" id="status" required class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl text-sm text-slate-700 dark:text-slate-250 focus:outline-none focus:border-[#1155CC]">
                                <option value="active" {{ old('status', 'active') === 'active' ? 'selected' : '' }}>Active</option>
                                <option value="inactive" {{ old('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                                <option value="maintenance" {{ old('status') === 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Address & Logistics Card -->
                <div class="bg-white dark:bg-slate-800 rounded-3xl p-6 border border-slate-100 dark:border-slate-700/50 shadow-sm space-y-4">
                    <h3 class="font-bold text-slate-800 dark:text-slate-100 text-base border-b border-slate-100 dark:border-slate-750 pb-3 flex items-center gap-2">
                        <i class="bi bi-geo-alt text-[#1155CC]"></i> Address & Coordinates
                    </h3>

                    <!-- Map Search -->
                    <div class="space-y-1.5">
                        <label class="text-xs font-bold text-slate-500 dark:text-slate-400">Search Location</label>
                        <div class="flex gap-2">
                            <input type="text" id="mapSearchInput" placeholder="Search by name or address..." class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl text-sm text-slate-700 dark:text-slate-250 placeholder-slate-400 focus:outline-none focus:border-[#1155CC]">
                            <button type="button" id="mapSearchBtn" class="px-4 py-2 bg-[#1155CC] text-white font-bold rounded-xl text-sm hover:bg-blue-700">Search</button>
                        </div>
                        <ul id="searchResults" class="hidden mt-1 max-h-40 overflow-y-auto bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl shadow-lg z-50"></ul>
                    </div>

                    <!-- Coordinates Map Picker -->
                    <div class="space-y-2 mt-4 relative z-10">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold text-slate-500 dark:text-slate-400 flex items-center gap-1.5">
                                <i class="bi bi-geo-fill text-danger"></i> Pin Location <span class="text-rose-500">*</span>
                            </span>
                            <button type="button" id="toggle-manual-coords" class="text-[11px] font-bold text-[#1155CC] hover:underline">
                                <i class="bi bi-lock-fill"></i> Override Manually
                            </button>
                        </div>
                        
                        <div id="pickerMap" class="w-full h-64 rounded-2xl border border-slate-250 dark:border-slate-750 bg-slate-100 overflow-hidden relative z-0"></div>
                    </div>

                    <!-- Address -->
                    <div class="space-y-1.5">
                        <label for="address" class="text-xs font-bold text-slate-500 dark:text-slate-400">Street Address <span class="text-rose-500">*</span></label>
                        <input type="text" name="address" id="address" value="{{ old('address') }}" required class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl text-sm text-slate-700 dark:text-slate-250 placeholder-slate-400 focus:outline-none focus:border-[#1155CC] @error('address') border-rose-550 @enderror">
                        @error('address') <span class="text-xs text-rose-600 block mt-1">{{ $message }}</span> @enderror
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <!-- City -->
                        <div class="space-y-1.5">
                            <label for="city" class="text-xs font-bold text-slate-500 dark:text-slate-400">City <span class="text-rose-500">*</span></label>
                            <input type="text" name="city" id="city" value="{{ old('city') }}" required class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl text-sm text-slate-700 dark:text-slate-250 focus:outline-none focus:border-[#1155CC] @error('city') border-rose-550 @enderror">
                            @error('city') <span class="text-xs text-rose-600 block mt-1">{{ $message }}</span> @enderror
                        </div>

                        <!-- State -->
                        <div class="space-y-1.5">
                            <label for="state" class="text-xs font-bold text-slate-500 dark:text-slate-400">State <span class="text-rose-500">*</span></label>
                            <input type="text" name="state" id="state" value="{{ old('state') }}" required class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl text-sm text-slate-700 dark:text-slate-250 focus:outline-none focus:border-[#1155CC]">
                        </div>

                        <!-- Postal Code -->
                        <div class="space-y-1.5">
                            <label for="postal_code" class="text-xs font-bold text-slate-500 dark:text-slate-400">Postal Code <span class="text-rose-500">*</span></label>
                            <input type="text" name="postal_code" id="postal_code" value="{{ old('postal_code') }}" required class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl text-sm text-slate-700 dark:text-slate-250 focus:outline-none focus:border-[#1155CC] @error('postal_code') border-rose-550 @enderror">
                            @error('postal_code') <span class="text-xs text-rose-600 block mt-1">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Latitude -->
                        <div class="space-y-1.5">
                            <label for="latitude" class="text-xs font-bold text-slate-500 dark:text-slate-400">Latitude</label>
                            <input type="text" name="latitude" id="latitude" value="{{ old('latitude') }}" readonly required class="w-full px-4 py-2.5 bg-slate-100 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl text-sm text-slate-500 dark:text-slate-450 focus:outline-none @error('latitude') border-rose-550 @enderror">
                            @error('latitude') <span class="text-xs text-rose-600 block mt-1">{{ $message }}</span> @enderror
                        </div>

                        <!-- Longitude -->
                        <div class="space-y-1.5">
                            <label for="longitude" class="text-xs font-bold text-slate-500 dark:text-slate-400">Longitude</label>
                            <input type="text" name="longitude" id="longitude" value="{{ old('longitude') }}" readonly required class="w-full px-4 py-2.5 bg-slate-100 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl text-sm text-slate-500 dark:text-slate-450 focus:outline-none @error('longitude') border-rose-550 @enderror">
                            @error('longitude') <span class="text-xs text-rose-600 block mt-1">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>

                <!-- Commercial & Audience Info -->
                <div class="bg-white dark:bg-slate-800 rounded-3xl p-6 border border-slate-100 dark:border-slate-700/50 shadow-sm space-y-4">
                    <h3 class="font-bold text-slate-800 dark:text-slate-100 text-base border-b border-slate-100 dark:border-slate-750 pb-3 flex items-center gap-2">
                        <i class="bi bi-people-fill text-[#1155CC]"></i> Audience & Commercials
                    </h3>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="space-y-1.5">
                            <label for="price_per_day" class="text-xs font-bold text-slate-500 dark:text-slate-400">Price Per Day (₹) <span class="text-rose-500">*</span></label>
                            <input type="number" step="0.01" name="price_per_day" id="price_per_day" value="{{ old('price_per_day', 0) }}" required class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl text-sm text-slate-700 dark:text-slate-250 focus:outline-none focus:border-[#1155CC]">
                        </div>
                        
                        <div class="space-y-1.5">
                            <label for="audience_count" class="text-xs font-bold text-slate-500 dark:text-slate-400">Avg Monthly Audience</label>
                            <input type="number" name="audience_count" id="audience_count" value="{{ old('audience_count', 0) }}" required class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl text-sm text-slate-700 dark:text-slate-250 focus:outline-none focus:border-[#1155CC]">
                        </div>
                        
                        <div class="space-y-1.5">
                            <label for="repeats_per_day" class="text-xs font-bold text-slate-500 dark:text-slate-400">Repeats Per Day</label>
                            <input type="number" name="repeats_per_day" id="repeats_per_day" value="{{ old('repeats_per_day', 0) }}" required class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl text-sm text-slate-700 dark:text-slate-250 focus:outline-none focus:border-[#1155CC]">
                        </div>
                    </div>

                    <div class="space-y-2 mt-4">
                        <label class="text-xs font-bold text-slate-500 dark:text-slate-400 block">Audience Type</label>
                        <div class="flex flex-wrap gap-3">
                            @foreach(['Male', 'Female', 'Family', 'Kids', 'Students', 'Professionals', 'Mixed Audience'] as $type)
                            <label class="flex items-center gap-2">
                                <input type="checkbox" name="audience_type[]" value="{{ $type }}" class="rounded text-[#1155CC] focus:ring-[#1155CC]">
                                <span class="text-sm text-slate-600 dark:text-slate-300">{{ $type }}</span>
                            </label>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- Operating Info & Screen Specs -->
                <div class="bg-white dark:bg-slate-800 rounded-3xl p-6 border border-slate-100 dark:border-slate-700/50 shadow-sm space-y-4">
                    <h3 class="font-bold text-slate-800 dark:text-slate-100 text-base border-b border-slate-100 dark:border-slate-750 pb-3 flex items-center gap-2">
                        <i class="bi bi-display text-[#1155CC]"></i> Operating & Screen Specs
                    </h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="space-y-1.5">
                            <label for="opening_time" class="text-xs font-bold text-slate-500 dark:text-slate-400">Opening Time</label>
                            <input type="time" name="opening_time" id="opening_time" value="{{ old('opening_time') }}" class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl text-sm text-slate-700 dark:text-slate-250 focus:outline-none focus:border-[#1155CC]">
                        </div>
                        <div class="space-y-1.5">
                            <label for="closing_time" class="text-xs font-bold text-slate-500 dark:text-slate-400">Closing Time</label>
                            <input type="time" name="closing_time" id="closing_time" value="{{ old('closing_time') }}" class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl text-sm text-slate-700 dark:text-slate-250 focus:outline-none focus:border-[#1155CC]">
                        </div>
                    </div>

                    <div class="space-y-2 mt-4">
                        <label class="text-xs font-bold text-slate-500 dark:text-slate-400 block">Operating Days</label>
                        <div class="flex flex-wrap gap-3">
                            @foreach(['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'] as $day)
                            <label class="flex items-center gap-2">
                                <input type="checkbox" name="operating_days[]" value="{{ $day }}" class="rounded text-[#1155CC] focus:ring-[#1155CC]" checked>
                                <span class="text-sm text-slate-600 dark:text-slate-300">{{ $day }}</span>
                            </label>
                            @endforeach
                        </div>
                    </div>

                    <hr class="border-slate-100 dark:border-slate-700 my-4">

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="space-y-1.5">
                            <label for="screen_size" class="text-xs font-bold text-slate-500 dark:text-slate-400">Screen Size</label>
                            <input type="text" name="screen_size" id="screen_size" placeholder="e.g. 55 inch" value="{{ old('screen_size') }}" class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl text-sm text-slate-700 dark:text-slate-250 focus:outline-none focus:border-[#1155CC]">
                        </div>
                        <div class="space-y-1.5">
                            <label for="screen_orientation" class="text-xs font-bold text-slate-500 dark:text-slate-400">Orientation</label>
                            <select name="screen_orientation" id="screen_orientation" class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl text-sm text-slate-700 dark:text-slate-250 focus:outline-none focus:border-[#1155CC]">
                                <option value="Landscape">Landscape</option>
                                <option value="Portrait">Portrait</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="flex gap-6 mt-4">
                        <label class="flex items-center gap-2">
                            <input type="checkbox" name="video_supported" value="1" class="rounded text-[#1155CC] focus:ring-[#1155CC]">
                            <span class="text-sm font-bold text-slate-700 dark:text-slate-300">Video Supported</span>
                        </label>
                        <label class="flex items-center gap-2">
                            <input type="checkbox" name="audio_supported" value="1" class="rounded text-[#1155CC] focus:ring-[#1155CC]">
                            <span class="text-sm font-bold text-slate-700 dark:text-slate-300">Audio Supported</span>
                        </label>
                    </div>
                </div>

                <!-- Additional Details -->
                <div class="bg-white dark:bg-slate-800 rounded-3xl p-6 border border-slate-100 dark:border-slate-700/50 shadow-sm space-y-4">
                    <h3 class="font-bold text-slate-800 dark:text-slate-100 text-base border-b border-slate-100 dark:border-slate-750 pb-3 flex items-center gap-2">
                        <i class="bi bi-card-text text-[#1155CC]"></i> Location Details
                    </h3>

                    <!-- Description -->
                    <div class="space-y-1.5">
                        <label for="description" class="text-xs font-bold text-slate-500 dark:text-slate-400">Description</label>
                        <textarea name="description" id="description" rows="3" placeholder="Describe the location venue..." class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl text-sm text-slate-700 dark:text-slate-250 placeholder-slate-400 focus:outline-none focus:border-[#1155CC]">{{ old('description') }}</textarea>
                    </div>

                    <!-- Nearby Places -->
                    <div class="space-y-1.5">
                        <label for="nearby_places" class="text-xs font-bold text-slate-500 dark:text-slate-400">Nearby Landmarks / Businesses</label>
                        <textarea name="nearby_places" id="nearby_places" rows="2" placeholder="e.g. Central Station, KFC, Starbucks..." class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl text-sm text-slate-700 dark:text-slate-250 placeholder-slate-400 focus:outline-none focus:border-[#1155CC]">{{ old('nearby_places') }}</textarea>
                    </div>
                </div>
            </div>

            <!-- Right Column: Images Upload & Metrics -->
            <div class="space-y-6">
                <!-- Location Partner Assign -->
                <div class="bg-white dark:bg-slate-800 rounded-3xl p-6 border border-slate-100 dark:border-slate-700/50 shadow-sm space-y-4">
                    <h3 class="font-bold text-slate-800 dark:text-slate-100 text-base border-b border-slate-100 dark:border-slate-750 pb-3 flex items-center gap-2">
                        <i class="bi bi-person-badge text-[#1155CC]"></i> Ownership
                    </h3>
                    <div class="space-y-1.5">
                        <label class="text-xs font-bold text-slate-500 dark:text-slate-400">Assigned Location Partner</label>
                        <select name="location_partner_id" class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl text-sm text-slate-700 focus:outline-none focus:border-[#1155CC]">
                            <option value="">No Partner (Admin Managed)</option>
                            @foreach(\App\Models\LocationPartnerProfile::all() as $partner)
                                <option value="{{ $partner->id }}">{{ $partner->company_name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- Images Upload Card -->
                <div class="bg-white dark:bg-slate-800 rounded-3xl p-6 border border-slate-100 dark:border-slate-700/50 shadow-sm space-y-4">
                    <h3 class="font-bold text-slate-800 dark:text-slate-100 text-base border-b border-slate-100 dark:border-slate-750 pb-3 flex items-center gap-2">
                        <i class="bi bi-images text-[#1155CC]"></i> Media (Photos)
                    </h3>

                    <!-- Image input -->
                    <div class="space-y-2">
                        <label class="text-xs font-bold text-slate-500 dark:text-slate-400 block">Select Multiple Images</label>
                        <div class="w-full py-6 bg-slate-50 dark:bg-slate-900 border-2 border-dashed border-slate-200 dark:border-slate-800 rounded-2xl flex flex-col items-center justify-center gap-2 relative hover:border-[#1155CC]/50 transition-all cursor-pointer">
                            <i class="bi bi-cloud-arrow-up text-2xl text-slate-400"></i>
                            <span class="text-xs text-slate-500 dark:text-slate-400 font-semibold">Choose image files</span>
                            <span class="text-[10px] text-slate-400">Max file size: 5MB (JPG, PNG)</span>
                            <input type="file" name="images[]" id="images" multiple accept="image/*" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer">
                        </div>
                    </div>

                    <!-- Images Preview list (Javascript filled) -->
                    <div id="image-previews-container" class="grid grid-cols-3 gap-2 mt-4"></div>
                </div>

                <!-- Form Submit Card -->
                <div class="bg-white dark:bg-slate-800 rounded-3xl p-6 border border-slate-100 dark:border-slate-700/50 shadow-sm flex items-center justify-between gap-4">
                    <a href="{{ route('admin.locations.index') }}" class="flex-1 py-2.5 border border-slate-200 hover:bg-slate-50 dark:border-slate-700 dark:hover:bg-slate-900 text-slate-600 dark:text-slate-400 text-center rounded-xl text-sm font-semibold transition-all">
                        Cancel
                    </a>
                    <button type="submit" class="flex-1 py-2.5 bg-[#1155CC] hover:bg-blue-700 text-white rounded-xl text-sm font-semibold transition-all shadow-md shadow-blue-500/10">
                        Create Location
                    </button>
                </div>
            </div>
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
        // Coimbatore Coords
        const initialCenter = [20.5937, 78.9629]; // India Center
        const defaultZoom = 4;

        // Init Picker Map
        const map = L.map('pickerMap', {
            zoomControl: true
        }).setView(initialCenter, defaultZoom);

        L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright" target="_blank">OpenStreetMap</a> contributors'
        }).addTo(map);

        // Marker instance holder
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

        // Map Click Listener
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

        searchBtn.addEventListener('click', function() {
            const query = searchInput.value;
            if(!query) return;

            fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(query)}&addressdetails=1`)
                .then(res => res.json())
                .then(data => {
                    searchResults.innerHTML = '';
                    if(data.length === 0) {
                        searchResults.innerHTML = '<li class="p-3 text-sm text-slate-500">No results found</li>';
                    } else {
                        data.forEach(item => {
                            const li = document.createElement('li');
                            li.className = 'p-3 hover:bg-slate-50 cursor-pointer border-b border-slate-100 text-sm text-slate-700 transition-colors';
                            li.textContent = item.display_name;
                            li.addEventListener('click', () => {
                                // Auto populate
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

        // Toggle Manual Coordinates Lock Override
        let manualOverride = false;
        const toggleBtn = document.getElementById('toggle-manual-coords');
        const latInput = document.getElementById('latitude');
        const lngInput = document.getElementById('longitude');

        toggleBtn.addEventListener('click', function() {
            manualOverride = !manualOverride;
            if (manualOverride) {
                latInput.removeAttribute('readonly');
                lngInput.removeAttribute('readonly');
                latInput.classList.remove('bg-slate-100', 'text-slate-500');
                lngInput.classList.remove('bg-slate-100', 'text-slate-500');
                latInput.classList.add('bg-slate-50', 'text-slate-700');
                lngInput.classList.add('bg-slate-50', 'text-slate-700');
                toggleBtn.innerHTML = '<i class="bi bi-unlock-fill"></i> Locked Coordinates';
            } else {
                latInput.setAttribute('readonly', 'true');
                lngInput.setAttribute('readonly', 'true');
                latInput.classList.add('bg-slate-100', 'text-slate-500');
                lngInput.classList.add('bg-slate-100', 'text-slate-500');
                latInput.classList.remove('bg-slate-50', 'text-slate-700');
                lngInput.classList.remove('bg-slate-50', 'text-slate-700');
                toggleBtn.innerHTML = '<i class="bi bi-lock-fill"></i> Override Manually';
            }
        });

        const updateMarkerFromInputs = () => {
            const latVal = parseFloat(latInput.value);
            const lngVal = parseFloat(lngInput.value);
            if (!isNaN(latVal) && !isNaN(lngVal)) {
                setMarker(latVal, lngVal);
            }
        };

        latInput.addEventListener('input', updateMarkerFromInputs);
        lngInput.addEventListener('input', updateMarkerFromInputs);

        // Images Preview Handler
        const imagesInput = document.getElementById('images');
        const previewContainer = document.getElementById('image-previews-container');

        imagesInput.addEventListener('change', function() {
            previewContainer.innerHTML = '';
            const files = Array.from(this.files);

            files.forEach((file) => {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const div = document.createElement('div');
                    div.className = 'relative w-full aspect-square bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl overflow-hidden group shadow-sm';
                    div.innerHTML = `
                        <img src="${e.target.result}" class="w-full h-full object-cover">
                        <div class="absolute inset-0 bg-slate-900/60 opacity-0 group-hover:opacity-100 flex items-center justify-center transition-all">
                            <span class="text-[9px] font-bold text-white uppercase tracking-wider bg-[#1155CC] px-2 py-0.5 rounded-full">Preview</span>
                        </div>
                    `;
                    previewContainer.appendChild(div);
                };
                reader.readAsDataURL(file);
            });
        });
    });
</script>
@endpush
