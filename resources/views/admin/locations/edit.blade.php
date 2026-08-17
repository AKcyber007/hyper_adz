@extends('admin.layouts.app', [
    'title' => 'Edit Location | Hyper Adz Admin'
])

@section('content')
<div class="max-w-4xl space-y-6">
    <!-- Header -->
    <div class="flex items-center gap-3">
        <a href="{{ route('admin.locations.index') }}" class="w-9 h-9 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-600 dark:bg-slate-800 dark:hover:bg-slate-700 dark:text-slate-350 flex items-center justify-center transition-all" title="Back to Listing">
            <i class="bi bi-arrow-left"></i>
        </a>
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white flex items-center gap-2">
                Edit Location: <span class="text-[#1155CC] font-mono text-lg font-semibold bg-blue-50/50 dark:bg-slate-900 px-2 py-0.5 rounded border border-blue-100/20">{{ $location->location_code }}</span>
            </h1>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-0.5">Modify properties, upload media files, and update coordinate markers on the network grid.</p>
        </div>
    </div>

    <!-- Edit Form -->
    <form method="POST" action="{{ route('admin.locations.update', $location->id) }}" enctype="multipart/form-data" class="space-y-6">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Left Column: Form Fields -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Basic details -->
                <div class="bg-white dark:bg-slate-800 rounded-3xl p-6 border border-slate-100 dark:border-slate-700/50 shadow-sm space-y-4">
                    <h3 class="font-bold text-slate-800 dark:text-slate-100 text-base border-b border-slate-100 dark:border-slate-750 pb-3 flex items-center gap-2">
                        <i class="bi bi-info-circle text-[#1155CC]"></i> Basic Details
                    </h3>

                    <!-- Name -->
                    <div class="space-y-1.5">
                        <label for="name" class="text-xs font-bold text-slate-500 dark:text-slate-400">Location Name <span class="text-rose-500">*</span></label>
                        <input type="text" name="name" id="name" value="{{ old('name', $location->name) }}" required class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl text-sm text-slate-700 dark:text-slate-250 focus:outline-none focus:border-[#1155CC]">
                        @error('name') <span class="text-xs text-rose-600 block mt-1">{{ $message }}</span> @enderror
                    </div>

                    <!-- Business Name -->
                    <div class="space-y-1.5">
                        <label for="business_name" class="text-xs font-bold text-slate-500 dark:text-slate-400">Business / Brand Name</label>
                        <input type="text" name="business_name" id="business_name" value="{{ old('business_name', $location->business_name) }}" placeholder="e.g. Hyper Adz Local" class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl text-sm text-slate-700 dark:text-slate-250 placeholder-slate-400 focus:outline-none focus:border-[#1155CC]">
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Category -->
                        <div class="space-y-1.5">
                            <label for="category_id" class="text-xs font-bold text-slate-500 dark:text-slate-400">Category <span class="text-rose-500">*</span></label>
                            <select name="category_id" id="category_id" required class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl text-sm text-slate-700 dark:text-slate-250 focus:outline-none focus:border-[#1155CC]">
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}" {{ old('category_id', $location->category_id) == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                                @endforeach
                            </select>
                            @error('category_id') <span class="text-xs text-rose-600 block mt-1">{{ $message }}</span> @enderror
                        </div>

                        <!-- Status -->
                        <div class="space-y-1.5">
                            <label for="status" class="text-xs font-bold text-slate-500 dark:text-slate-400">Status <span class="text-rose-500">*</span></label>
                            <select name="status" id="status" required class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl text-sm text-slate-700 dark:text-slate-250 focus:outline-none focus:border-[#1155CC]">
                                <option value="active" {{ old('status', $location->status) === 'active' ? 'selected' : '' }}>Active</option>
                                <option value="inactive" {{ old('status', $location->status) === 'inactive' ? 'selected' : '' }}>Inactive</option>
                                <option value="maintenance" {{ old('status', $location->status) === 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Address & Coordinates picker -->
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
                        <input type="text" name="address" id="address" value="{{ old('address', $location->address) }}" required class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl text-sm text-slate-700 dark:text-slate-250 focus:outline-none focus:border-[#1155CC]">
                        @error('address') <span class="text-xs text-rose-600 block mt-1">{{ $message }}</span> @enderror
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <!-- City -->
                        <div class="space-y-1.5">
                            <label for="city" class="text-xs font-bold text-slate-500 dark:text-slate-400">City <span class="text-rose-500">*</span></label>
                            <input type="text" name="city" id="city" value="{{ old('city', $location->city) }}" required class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl text-sm text-slate-700 dark:text-slate-250 focus:outline-none focus:border-[#1155CC]">
                            @error('city') <span class="text-xs text-rose-600 block mt-1">{{ $message }}</span> @enderror
                        </div>

                        <!-- State -->
                        <div class="space-y-1.5">
                            <label for="state" class="text-xs font-bold text-slate-500 dark:text-slate-400">State <span class="text-rose-500">*</span></label>
                            <input type="text" name="state" id="state" value="{{ old('state', $location->state) }}" required class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl text-sm text-slate-700 dark:text-slate-250 focus:outline-none focus:border-[#1155CC]">
                        </div>

                        <!-- Postal Code -->
                        <div class="space-y-1.5">
                            <label for="postal_code" class="text-xs font-bold text-slate-500 dark:text-slate-400">Postal Code <span class="text-rose-500">*</span></label>
                            <input type="text" name="postal_code" id="postal_code" value="{{ old('postal_code', $location->postal_code) }}" required class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl text-sm text-slate-700 dark:text-slate-250 focus:outline-none focus:border-[#1155CC]">
                            @error('postal_code') <span class="text-xs text-rose-600 block mt-1">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Latitude -->
                        <div class="space-y-1.5">
                            <label for="latitude" class="text-xs font-bold text-slate-500 dark:text-slate-400">Latitude</label>
                            <input type="text" name="latitude" id="latitude" value="{{ old('latitude', $location->latitude) }}" readonly required class="w-full px-4 py-2.5 bg-slate-100 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl text-sm text-slate-500 focus:outline-none">
                        </div>

                        <!-- Longitude -->
                        <div class="space-y-1.5">
                            <label for="longitude" class="text-xs font-bold text-slate-500 dark:text-slate-400">Longitude</label>
                            <input type="text" name="longitude" id="longitude" value="{{ old('longitude', $location->longitude) }}" readonly required class="w-full px-4 py-2.5 bg-slate-100 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl text-sm text-slate-500 focus:outline-none">
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
                            <input type="number" step="0.01" name="price_per_day" id="price_per_day" value="{{ old('price_per_day', $location->price_per_day ?? 0) }}" required class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl text-sm text-slate-700 dark:text-slate-250 focus:outline-none focus:border-[#1155CC]">
                        </div>
                        
                        <div class="space-y-1.5">
                            <label for="audience_count" class="text-xs font-bold text-slate-500 dark:text-slate-400">Avg Monthly Audience</label>
                            <input type="number" name="audience_count" id="audience_count" value="{{ old('audience_count', $location->audience_count) }}" required class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl text-sm text-slate-700 dark:text-slate-250 focus:outline-none focus:border-[#1155CC]">
                        </div>
                        
                        <div class="space-y-1.5">
                            <label for="repeats_per_day" class="text-xs font-bold text-slate-500 dark:text-slate-400">Repeats Per Day</label>
                            <input type="number" name="repeats_per_day" id="repeats_per_day" value="{{ old('repeats_per_day', $location->repeats_per_day) }}" required class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl text-sm text-slate-700 dark:text-slate-250 focus:outline-none focus:border-[#1155CC]">
                        </div>
                    </div>

                    <div class="space-y-2 mt-4">
                        <label class="text-xs font-bold text-slate-500 dark:text-slate-400 block">Audience Type</label>
                        <div class="flex flex-wrap gap-3">
                            @php
                                $selectedAudiences = old('audience_type', is_array($location->audience_type) ? $location->audience_type : json_decode($location->audience_type ?? '[]', true) ?? []);
                            @endphp
                            @foreach(['Male', 'Female', 'Family', 'Kids', 'Students', 'Professionals', 'Mixed Audience'] as $type)
                            <label class="flex items-center gap-2">
                                <input type="checkbox" name="audience_type[]" value="{{ $type }}" class="rounded text-[#1155CC] focus:ring-[#1155CC]" {{ in_array($type, $selectedAudiences) ? 'checked' : '' }}>
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
                            <input type="time" name="opening_time" id="opening_time" value="{{ old('opening_time', $location->opening_time ? \Carbon\Carbon::parse($location->opening_time)->format('H:i') : '') }}" class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl text-sm text-slate-700 dark:text-slate-250 focus:outline-none focus:border-[#1155CC]">
                        </div>
                        <div class="space-y-1.5">
                            <label for="closing_time" class="text-xs font-bold text-slate-500 dark:text-slate-400">Closing Time</label>
                            <input type="time" name="closing_time" id="closing_time" value="{{ old('closing_time', $location->closing_time ? \Carbon\Carbon::parse($location->closing_time)->format('H:i') : '') }}" class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl text-sm text-slate-700 dark:text-slate-250 focus:outline-none focus:border-[#1155CC]">
                        </div>
                    </div>

                    <div class="space-y-2 mt-4">
                        <label class="text-xs font-bold text-slate-500 dark:text-slate-400 block">Operating Days</label>
                        <div class="flex flex-wrap gap-3">
                            @php
                                $selectedDays = old('operating_days', is_array($location->operating_days) ? $location->operating_days : json_decode($location->operating_days ?? '[]', true) ?? []);
                            @endphp
                            @foreach(['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'] as $day)
                            <label class="flex items-center gap-2">
                                <input type="checkbox" name="operating_days[]" value="{{ $day }}" class="rounded text-[#1155CC] focus:ring-[#1155CC]" {{ empty($selectedDays) || in_array($day, $selectedDays) ? 'checked' : '' }}>
                                <span class="text-sm text-slate-600 dark:text-slate-300">{{ $day }}</span>
                            </label>
                            @endforeach
                        </div>
                    </div>

                    <hr class="border-slate-100 dark:border-slate-700 my-4">

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="space-y-1.5">
                            <label for="screen_size" class="text-xs font-bold text-slate-500 dark:text-slate-400">Screen Size</label>
                            <input type="text" name="screen_size" id="screen_size" placeholder="e.g. 55 inch" value="{{ old('screen_size', $location->screen_size) }}" class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl text-sm text-slate-700 dark:text-slate-250 focus:outline-none focus:border-[#1155CC]">
                        </div>
                        <div class="space-y-1.5">
                            <label for="screen_orientation" class="text-xs font-bold text-slate-500 dark:text-slate-400">Orientation</label>
                            <select name="screen_orientation" id="screen_orientation" class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl text-sm text-slate-700 dark:text-slate-250 focus:outline-none focus:border-[#1155CC]">
                                <option value="Landscape" {{ old('screen_orientation', $location->screen_orientation) == 'Landscape' ? 'selected' : '' }}>Landscape</option>
                                <option value="Portrait" {{ old('screen_orientation', $location->screen_orientation) == 'Portrait' ? 'selected' : '' }}>Portrait</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="flex gap-6 mt-4">
                        <label class="flex items-center gap-2">
                            <input type="hidden" name="video_supported" value="0">
                            <input type="checkbox" name="video_supported" value="1" class="rounded text-[#1155CC] focus:ring-[#1155CC]" {{ old('video_supported', $location->video_supported) ? 'checked' : '' }}>
                            <span class="text-sm font-bold text-slate-700 dark:text-slate-300">Video Supported</span>
                        </label>
                        <label class="flex items-center gap-2">
                            <input type="hidden" name="audio_supported" value="0">
                            <input type="checkbox" name="audio_supported" value="1" class="rounded text-[#1155CC] focus:ring-[#1155CC]" {{ old('audio_supported', $location->audio_supported) ? 'checked' : '' }}>
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
                        <textarea name="description" id="description" rows="3" placeholder="Describe the location venue..." class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl text-sm text-slate-700 dark:text-slate-250 placeholder-slate-400 focus:outline-none focus:border-[#1155CC]">{{ old('description', $location->description) }}</textarea>
                    </div>

                    <!-- Nearby Places -->
                    <div class="space-y-1.5">
                        <label for="nearby_places" class="text-xs font-bold text-slate-500 dark:text-slate-400">Nearby Landmarks / Businesses</label>
                        <textarea name="nearby_places" id="nearby_places" rows="2" placeholder="e.g. Central Station, KFC, Starbucks..." class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl text-sm text-slate-700 dark:text-slate-250 placeholder-slate-400 focus:outline-none focus:border-[#1155CC]">{{ old('nearby_places', $location->nearby_places) }}</textarea>
                    </div>
                </div>
            </div>

            <!-- Right Column: Images Manager & Submissions -->
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
                                <option value="{{ $partner->id }}" {{ old('location_partner_id', $location->location_partner_id) == $partner->id ? 'selected' : '' }}>{{ $partner->company_name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- Logistics Metrics -->
                <div class="bg-white dark:bg-slate-800 rounded-3xl p-6 border border-slate-100 dark:border-slate-700/50 shadow-sm space-y-4">
                    <h3 class="font-bold text-slate-800 dark:text-slate-100 text-base border-b border-slate-100 dark:border-slate-750 pb-3 flex items-center gap-2">
                        <i class="bi bi-bar-chart-fill text-[#1155CC]"></i> Legacy Metrics
                    </h3>

                    <!-- Daily Footfall -->
                    <div class="space-y-1.5">
                        <label for="daily_footfall" class="text-xs font-bold text-slate-500 dark:text-slate-400">Daily Footfall (Legacy) <span class="text-rose-500">*</span></label>
                        <input type="number" name="daily_footfall" id="daily_footfall" value="{{ old('daily_footfall', $location->daily_footfall) }}" min="0" required class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl text-sm text-slate-700 dark:text-slate-250 focus:outline-none focus:border-[#1155CC]">
                        @error('daily_footfall') <span class="text-xs text-rose-600 block mt-1">{{ $message }}</span> @enderror
                    </div>

                    <!-- Operating Hours -->
                    <div class="space-y-1.5">
                        <label for="operating_hours" class="text-xs font-bold text-slate-500 dark:text-slate-400">Operating Hours (Legacy)</label>
                        <input type="text" name="operating_hours" id="operating_hours" value="{{ old('operating_hours', $location->operating_hours) }}" class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl text-sm text-slate-700 dark:text-slate-250 focus:outline-none focus:border-[#1155CC]">
                    </div>
                </div>

                <!-- Existing Images management -->
                <div class="bg-white dark:bg-slate-800 rounded-3xl p-6 border border-slate-100 dark:border-slate-700/50 shadow-sm space-y-4">
                    <h3 class="font-bold text-slate-800 dark:text-slate-100 text-base border-b border-slate-100 dark:border-slate-750 pb-3 flex items-center gap-2">
                        <i class="bi bi-images text-[#1155CC]"></i> Manage Images
                    </h3>

                    @if($location->images->isNotEmpty())
                        <div class="space-y-3">
                            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Uploaded Photos (Select Primary / Delete)</span>
                            <div class="grid grid-cols-2 gap-3">
                                @foreach($location->images as $img)
                                    <div class="relative bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl overflow-hidden shadow-sm flex flex-col group">
                                        <div class="w-full aspect-video overflow-hidden">
                                            <img src="{{ $img->url }}" class="w-full h-full object-cover">
                                        </div>
                                        <div class="p-2 bg-slate-50/50 dark:bg-slate-900/50 border-t border-slate-100 dark:border-slate-850 flex items-center justify-between gap-2">
                                            <label class="flex items-center gap-1.5 text-xs text-slate-600 dark:text-slate-400 cursor-pointer">
                                                <input type="radio" name="primary_image_id" value="{{ $img->id }}" {{ $img->is_primary ? 'checked' : '' }} class="text-[#1155CC] focus:ring-[#1155CC]">
                                                <span>Primary</span>
                                            </label>
                                            <label class="flex items-center gap-1 text-xs text-rose-500 hover:text-rose-600 cursor-pointer">
                                                <input type="checkbox" name="delete_images[]" value="{{ $img->id }}" class="text-rose-600 focus:ring-rose-500 rounded">
                                                <span>Delete</span>
                                            </label>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @else
                        <p class="text-xs text-slate-400 text-center py-2">No images uploaded for this location.</p>
                    @endif

                    <!-- Select additional images -->
                    <div class="space-y-2 border-t border-slate-100 dark:border-slate-800 pt-4">
                        <label class="text-xs font-bold text-slate-500 dark:text-slate-400 block">Add More Images</label>
                        <div class="w-full py-4 bg-slate-50 dark:bg-slate-900 border border-dashed border-slate-250 dark:border-slate-800 rounded-xl flex flex-col items-center justify-center gap-1 relative hover:border-[#1155CC]/55 transition-all cursor-pointer">
                            <i class="bi bi-cloud-arrow-up text-xl text-slate-400"></i>
                            <span class="text-[11px] text-slate-500 font-semibold">Choose new files</span>
                            <input type="file" name="images[]" id="images" multiple accept="image/*" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer">
                        </div>
                        <div id="image-previews-container" class="grid grid-cols-3 gap-2 mt-2"></div>
                    </div>
                </div>

                <!-- Submit controls -->
                <div class="bg-white dark:bg-slate-800 rounded-3xl p-6 border border-slate-100 dark:border-slate-700/50 shadow-sm flex items-center justify-between gap-4">
                    <a href="{{ route('admin.locations.index') }}" class="flex-1 py-2.5 border border-slate-200 hover:bg-slate-50 dark:border-slate-700 dark:hover:bg-slate-900 text-slate-600 dark:text-slate-400 text-center rounded-xl text-sm font-semibold transition-all">
                        Cancel
                    </a>
                    <button type="submit" class="flex-1 py-2.5 bg-[#1155CC] hover:bg-blue-700 text-white rounded-xl text-sm font-semibold transition-all shadow-md shadow-blue-500/10">
                        Save Changes
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
        // Pre-loaded Coords
        const initialLat = {{ $location->latitude ?? 20.5937 }};
        const initialLng = {{ $location->longitude ?? 78.9629 }};
        const locationLatLng = [initialLat, initialLng];

        // Init Map
        const map = L.map('pickerMap', {
            zoomControl: true
        }).setView(locationLatLng, 15);

        L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright" target="_blank">OpenStreetMap</a> contributors'
        }).addTo(map);

        // Load Pre-existing Location Pin Marker
        let selectedMarker = L.marker(locationLatLng, {
            draggable: true
        }).addTo(map);

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

        // Drag marker handler
        selectedMarker.on('dragend', function(evt) {
            const markerLat = evt.target.getLatLng().lat.toFixed(8);
            const markerLng = evt.target.getLatLng().lng.toFixed(8);
            document.getElementById('latitude').value = markerLat;
            document.getElementById('longitude').value = markerLng;
        });

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
                            searchResults.innerHTML = '<li class="p-3 text-sm text-slate-500">No results found</li>';
                        } else {
                            data.forEach(item => {
                                const li = document.createElement('li');
                                li.className = 'p-3 hover:bg-slate-50 cursor-pointer border-b border-slate-100 text-sm text-slate-700 transition-colors';
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

        // Images Preview handler for new images select
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
                            <span class="text-[9px] font-bold text-white uppercase tracking-wider bg-[#1155CC] px-2 py-0.5 rounded-full">New Preview</span>
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
