@extends('layouts.partner')

@section('title', 'Edit Location')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="flex items-center gap-3">
        <a href="{{ route('partner.locations.index') }}" class="w-8 h-8 rounded-lg bg-white border border-slate-200 flex items-center justify-center text-slate-600 hover:text-slate-900 transition-all">
            <i class="bi bi-arrow-left"></i>
        </a>
        <div>
            <h2 class="text-xl font-extrabold tracking-tight text-slate-900">Edit Location: {{ $location->name }}</h2>
            <p class="text-xs text-slate-500 mt-0.5">Modify parameters or submit rejected locations back for review.</p>
        </div>
    </div>

    <form method="POST" action="{{ route('partner.locations.update', $location->id) }}" enctype="multipart/form-data" class="bg-white border border-slate-200 rounded-[32px] p-6 sm:p-8 space-y-8">
        @csrf
        @method('PUT')

        @if($location->status === 'rejected')
            <div class="bg-rose-500/10 border border-rose-500/20 rounded-2xl p-4 space-y-2">
                <div class="flex items-center gap-2 text-rose-400 text-xs font-bold">
                    <i class="bi bi-x-circle-fill text-sm"></i>
                    <span>This request was Rejected</span>
                </div>
                <p class="text-[11px] text-slate-600 pl-6 leading-relaxed">
                    <strong>Reason:</strong> {{ $location->rejection_reason ?? 'No feedback provided.' }}
                </p>
                <p class="text-[10px] text-slate-500 pl-6">
                    Rejected on {{ $location->rejected_at ? $location->rejected_at->format('d-M-Y H:i') : '' }} by Admin. Saving updates will automatically resubmit it for verification.
                </p>
            </div>
        @endif

        <!-- Basic Details Section -->
        <div class="space-y-4">
            <h3 class="font-bold text-slate-800 text-sm border-b border-slate-100 pb-2">Basic Details</h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <!-- Location Name -->
                <div class="space-y-1.5 col-span-2 sm:col-span-1">
                    <label for="name" class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider">Venue / Location Name <span class="text-rose-500">*</span></label>
                    <input type="text" id="name" name="name" value="{{ old('name', $location->name) }}" required class="w-full px-4 py-2.5 bg-white border border-slate-200 rounded-xl text-xs text-slate-900 focus:outline-none focus:border-blue-600 focus:ring-1 focus:ring-blue-600 transition-all">
                    @error('name') <p class="text-xxs text-rose-455 font-bold mt-1">{{ $message }}</p> @enderror
                </div>
                
                <!-- Business Name -->
                <div class="space-y-1.5 col-span-2 sm:col-span-1">
                    <label for="business_name" class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider">Business / Brand Name</label>
                    <input type="text" id="business_name" name="business_name" value="{{ old('business_name', $location->business_name) }}" class="w-full px-4 py-2.5 bg-white border border-slate-200 rounded-xl text-xs text-slate-900 focus:outline-none focus:border-blue-600 focus:ring-1 focus:ring-blue-600 transition-all">
                </div>

                <!-- Category (Readonly in Edit) -->
                <div class="space-y-1.5 col-span-2 opacity-70">
                    <label for="category_id_display" class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider">Category</label>
                    <select id="category_id_display" disabled class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-500 focus:outline-none cursor-not-allowed">
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ $location->category_id == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        <!-- Address & Coordinates Section -->
        <div class="space-y-4">
            <h3 class="font-bold text-slate-800 text-sm border-b border-slate-100 pb-2">Address & Coordinates</h3>

            <!-- Address (Readonly) -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 mt-4">
                <div class="space-y-1.5 col-span-2 opacity-70">
                    <label for="address" class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider">Full Address</label>
                    <textarea id="address" readonly rows="2" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-500 focus:outline-none cursor-not-allowed">{{ $location->address }}</textarea>
                </div>

                <div class="space-y-1.5 opacity-70">
                    <label for="city" class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider">City</label>
                    <input type="text" id="city" readonly value="{{ $location->city }}" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-500 focus:outline-none cursor-not-allowed">
                </div>

                <div class="space-y-1.5 opacity-70">
                    <label for="state" class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider">State</label>
                    <input type="text" id="state" readonly value="{{ $location->state }}" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-500 focus:outline-none cursor-not-allowed">
                </div>

                <div class="space-y-1.5 opacity-70">
                    <label for="postal_code" class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider">Postal Code</label>
                    <input type="text" id="postal_code" readonly value="{{ $location->postal_code }}" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-500 focus:outline-none cursor-not-allowed">
                </div>
            </div>
            
            <p class="text-[10px] text-slate-500"><i class="bi bi-info-circle text-blue-500"></i> Address and coordinates cannot be changed after creation. Please contact Admin for major address changes.</p>
        </div>

        <!-- Commercials & Audience -->
        <div class="space-y-4">
            <h3 class="font-bold text-slate-800 text-sm border-b border-slate-100 pb-2">Commercials & Audience</h3>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
                <!-- Price -->
                <div class="space-y-1.5">
                    <label for="price_per_day" class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider">Price Per Day (₹) <span class="text-rose-500">*</span></label>
                    <input type="number" step="0.01" id="price_per_day" name="price_per_day" value="{{ old('price_per_day', $location->price_per_day) }}" required min="0" class="w-full px-4 py-2.5 bg-white border border-slate-200 rounded-xl text-xs text-slate-900 focus:outline-none focus:border-blue-600 focus:ring-1 focus:ring-blue-600 transition-all">
                </div>

                <!-- Audience Count -->
                <div class="space-y-1.5">
                    <label for="audience_count" class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider">Monthly Audience</label>
                    <input type="number" id="audience_count" name="audience_count" value="{{ old('audience_count', $location->audience_count) }}" min="0" class="w-full px-4 py-2.5 bg-white border border-slate-200 rounded-xl text-xs text-slate-900 focus:outline-none focus:border-blue-600 focus:ring-1 focus:ring-blue-600 transition-all">
                </div>

                <!-- Status -->
                <div class="space-y-1.5">
                    <label for="status" class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider">Location Status</label>
                    <select id="status" name="status" class="w-full px-4 py-2.5 bg-white border border-slate-200 rounded-xl text-xs text-slate-900 focus:outline-none focus:border-blue-600 transition-all">
                        <option value="active" {{ old('status', $location->status) === 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ old('status', $location->status) === 'inactive' ? 'selected' : '' }}>Inactive</option>
                        <option value="maintenance" {{ old('status', $location->status) === 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                    </select>
                </div>
            </div>

            <div class="space-y-2 mt-4">
                <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider">Audience Types</label>
                <div class="flex flex-wrap gap-3">
                    @php
                        $selectedAudiences = old('audience_type', is_array($location->audience_type) ? $location->audience_type : json_decode($location->audience_type ?? '[]', true) ?? []);
                    @endphp
                    @foreach(['Male', 'Female', 'Family', 'Kids', 'Students', 'Professionals', 'Mixed Audience'] as $type)
                    <label class="flex items-center gap-2">
                        <input type="checkbox" name="audience_type[]" value="{{ $type }}" class="rounded text-blue-600 focus:ring-blue-600" {{ in_array($type, $selectedAudiences) ? 'checked' : '' }}>
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
                    <input type="text" id="operating_hours" name="operating_hours" value="{{ old('operating_hours', $location->operating_hours) }}" class="w-full px-4 py-2.5 bg-white border border-slate-200 rounded-xl text-xs text-slate-900 focus:outline-none focus:border-blue-600 focus:ring-1 focus:ring-blue-600 transition-all">
                </div>
                
                <div class="space-y-1.5">
                    <label for="repeats_per_day" class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider">Ad Repeats Per Day</label>
                    <input type="number" id="repeats_per_day" name="repeats_per_day" value="{{ old('repeats_per_day', $location->repeats_per_day) }}" min="0" class="w-full px-4 py-2.5 bg-white border border-slate-200 rounded-xl text-xs text-slate-900 focus:outline-none focus:border-blue-600 focus:ring-1 focus:ring-blue-600 transition-all">
                </div>

                <div class="space-y-1.5">
                    <label for="opening_time" class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider">Opening Time</label>
                    <input type="time" id="opening_time" name="opening_time" value="{{ old('opening_time', $location->opening_time ? \Carbon\Carbon::parse($location->opening_time)->format('H:i') : '') }}" class="w-full px-4 py-2.5 bg-white border border-slate-200 rounded-xl text-xs text-slate-900 focus:outline-none focus:border-blue-600 focus:ring-1 focus:ring-blue-600 transition-all">
                </div>
                
                <div class="space-y-1.5">
                    <label for="closing_time" class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider">Closing Time</label>
                    <input type="time" id="closing_time" name="closing_time" value="{{ old('closing_time', $location->closing_time ? \Carbon\Carbon::parse($location->closing_time)->format('H:i') : '') }}" class="w-full px-4 py-2.5 bg-white border border-slate-200 rounded-xl text-xs text-slate-900 focus:outline-none focus:border-blue-600 focus:ring-1 focus:ring-blue-600 transition-all">
                </div>
            </div>

            <div class="space-y-2 mt-4">
                <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider">Operating Days</label>
                <div class="flex flex-wrap gap-3">
                    @php
                        $selectedDays = old('operating_days', is_array($location->operating_days) ? $location->operating_days : json_decode($location->operating_days ?? '[]', true) ?? []);
                    @endphp
                    @foreach(['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'] as $day)
                    <label class="flex items-center gap-2">
                        <input type="checkbox" name="operating_days[]" value="{{ $day }}" class="rounded text-blue-600 focus:ring-blue-600" {{ empty($selectedDays) || in_array($day, $selectedDays) ? 'checked' : '' }}>
                        <span class="text-xs text-slate-700">{{ $day }}</span>
                    </label>
                    @endforeach
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 mt-4">
                <div class="space-y-1.5">
                    <label for="screen_size" class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider">Screen Size (e.g. 55 inch)</label>
                    <input type="text" id="screen_size" name="screen_size" value="{{ old('screen_size', $location->screen_size) }}" class="w-full px-4 py-2.5 bg-white border border-slate-200 rounded-xl text-xs text-slate-900 focus:outline-none focus:border-blue-600 transition-all">
                </div>
                
                <div class="space-y-1.5">
                    <label for="screen_orientation" class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider">Screen Orientation</label>
                    <select id="screen_orientation" name="screen_orientation" class="w-full px-4 py-2.5 bg-white border border-slate-200 rounded-xl text-xs text-slate-900 focus:outline-none focus:border-blue-600 transition-all">
                        <option value="Landscape" {{ old('screen_orientation', $location->screen_orientation) == 'Landscape' ? 'selected' : '' }}>Landscape</option>
                        <option value="Portrait" {{ old('screen_orientation', $location->screen_orientation) == 'Portrait' ? 'selected' : '' }}>Portrait</option>
                    </select>
                </div>
            </div>
            
            <div class="flex gap-6 mt-2">
                <label class="flex items-center gap-2">
                    <input type="hidden" name="video_supported" value="0">
                    <input type="checkbox" name="video_supported" value="1" class="rounded text-blue-600 focus:ring-blue-600" {{ old('video_supported', $location->video_supported) ? 'checked' : '' }}>
                    <span class="text-xs font-bold text-slate-700">Video Supported</span>
                </label>
                <label class="flex items-center gap-2">
                    <input type="hidden" name="audio_supported" value="0">
                    <input type="checkbox" name="audio_supported" value="1" class="rounded text-blue-600 focus:ring-blue-600" {{ old('audio_supported', $location->audio_supported) ? 'checked' : '' }}>
                    <span class="text-xs font-bold text-slate-700">Audio Supported</span>
                </label>
            </div>
        </div>

        <!-- Location Descriptions -->
        <div class="space-y-4">
            <h3 class="font-bold text-slate-800 text-sm border-b border-slate-100 pb-2">Additional Info</h3>
            
            <div class="space-y-1.5">
                <label for="description" class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider">Venue Description</label>
                <textarea id="description" name="description" rows="2" class="w-full px-4 py-2.5 bg-white border border-slate-200 rounded-xl text-xs text-slate-900 focus:outline-none focus:border-blue-600 focus:ring-1 focus:ring-blue-600 transition-all">{{ old('description', $location->description) }}</textarea>
            </div>
            
            <div class="space-y-1.5">
                <label for="nearby_places" class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider">Nearby Landmarks / Businesses</label>
                <textarea id="nearby_places" name="nearby_places" rows="2" class="w-full px-4 py-2.5 bg-white border border-slate-200 rounded-xl text-xs text-slate-900 focus:outline-none focus:border-blue-600 focus:ring-1 focus:ring-blue-600 transition-all">{{ old('nearby_places', $location->nearby_places) }}</textarea>
            </div>
        </div>

        <!-- Image Upload -->
        <div class="space-y-4">
            <h3 class="font-bold text-slate-800 text-sm border-b border-slate-100 pb-2">Media</h3>
            
            @if($location->images->isNotEmpty())
                <div class="space-y-3">
                    <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider">Manage Current Images</label>
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                        @foreach($location->images as $img)
                            <div class="relative bg-white border border-slate-200 rounded-xl p-2 text-center group">
                                <img src="{{ Storage::url($img->image_path) }}" class="w-full h-24 object-cover rounded-lg">
                                <div class="mt-2 flex items-center justify-between text-[10px]">
                                    <label class="flex items-center gap-1 cursor-pointer">
                                        <input type="radio" name="primary_image" value="{{ $img->id }}" {{ $img->is_primary ? 'checked' : '' }} class="accent-blue-600">
                                        <span>Primary</span>
                                    </label>
                                    <label class="flex items-center gap-1 text-rose-450 cursor-pointer">
                                        <input type="checkbox" name="delete_images[]" value="{{ $img->id }}" class="accent-rose-600">
                                        <span>Delete</span>
                                    </label>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <div class="space-y-1.5">
                <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider">Add More Images</label>
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
            <button type="submit" class="px-5 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-500 text-xs font-bold text-white transition-all shadow-lg shadow-blue-500/20">Send Update Request</button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Images Preview handler
        const imagesInput = document.getElementById('images');
        const previewContainer = document.getElementById('image-previews-container');

        if(imagesInput) {
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
        }
    });
</script>
@endpush
