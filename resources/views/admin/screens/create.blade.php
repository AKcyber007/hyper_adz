@extends('admin.layouts.app', [
    'title' => 'Add Screen | Hyper Adz Admin'
])

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex items-center gap-3">
        <a href="{{ route('admin.screens.index') }}" class="w-10 h-10 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 dark:bg-slate-800 dark:hover:bg-slate-700 dark:text-slate-300 flex items-center justify-center transition-all">
            <i class="bi bi-arrow-left text-lg"></i>
        </a>
        <div>
            <h1 class="text-xl font-bold tracking-tight text-slate-900 dark:text-white">Add New Screen</h1>
            <p class="text-xs text-slate-500 mt-0.5">Register a new screen and link it to an advertising location.</p>
        </div>
    </div>

    <!-- Error Block -->
    @if ($errors->any())
        <div class="p-4 bg-red-50 border border-red-100 dark:bg-red-950/10 dark:border-red-900/30 text-red-650 dark:text-red-400 rounded-2xl text-xs space-y-1">
            <div class="font-bold flex items-center gap-1.5 mb-1 text-sm">
                <i class="bi bi-exclamation-triangle-fill"></i> Please fix the validation errors:
            </div>
            <ul class="list-disc pl-4 space-y-0.5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Form Container -->
    <div class="bg-white dark:bg-slate-800 rounded-3xl border border-slate-100 dark:border-slate-700/50 shadow-sm overflow-hidden p-6 sm:p-8">
        <form method="POST" action="{{ route('admin.screens.store') }}" enctype="multipart/form-data" class="space-y-6">
            @csrf

            <!-- Section 1: Core Linkings -->
            <div>
                <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider border-b border-slate-100 dark:border-slate-750 pb-2 mb-4">Core Assignments</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                    <!-- Location Selector -->
                    <div class="space-y-1.5">
                        <label class="text-xs font-bold text-slate-500 dark:text-slate-400 flex items-center gap-1">
                            Location <span class="text-red-500">*</span>
                        </label>
                        <select name="location_id" required class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-850 rounded-xl text-sm text-slate-700 dark:text-slate-200 focus:outline-none focus:border-[#1155CC] transition-all">
                            <option value="">Select Location</option>
                            @foreach($locations as $loc)
                                <option value="{{ $loc->id }}" {{ old('location_id') == $loc->id ? 'selected' : '' }}>{{ $loc->name }} ({{ $loc->city }})</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Screen Type -->
                    <div class="space-y-1.5">
                        <label class="text-xs font-bold text-slate-500 dark:text-slate-400 flex items-center gap-1">
                            Screen Type <span class="text-red-500">*</span>
                        </label>
                        <select name="screen_type_id" required class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-850 rounded-xl text-sm text-slate-700 dark:text-slate-200 focus:outline-none focus:border-[#1155CC] transition-all">
                            <option value="">Select Type</option>
                            @foreach($screenTypes as $type)
                                <option value="{{ $type->id }}" {{ old('screen_type_id') == $type->id ? 'selected' : '' }}>{{ $type->name }}</option>
                            @endforeach
                        </select>
                    </div>

                </div>
            </div>

            <!-- Section 2: General Details -->
            <div>
                <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider border-b border-slate-100 dark:border-slate-750 pb-2 mb-4 font-semibold">General Information</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <!-- Screen Name -->
                    <div class="space-y-1.5">
                        <label class="text-xs font-bold text-slate-500 dark:text-slate-400">Screen Name <span class="text-red-500">*</span></label>
                        <input type="text" name="name" value="{{ old('name') }}" required placeholder="e.g. Lobby Vertical Screen 01" class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-850 rounded-xl text-sm text-slate-700 dark:text-slate-200 focus:outline-none focus:border-[#1155CC] transition-all">
                    </div>

                    <!-- Advertiser Identifier Code -->
                    <div class="space-y-1.5">
                        <label class="text-xs font-bold text-slate-500 dark:text-slate-400">Advertiser-Facing Identifier</label>
                        <input type="text" name="screen_identifier" value="{{ old('screen_identifier') }}" placeholder="e.g. BFM-LED-01" class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-850 rounded-xl text-sm text-slate-700 dark:text-slate-200 focus:outline-none focus:border-[#1155CC] transition-all">
                        <span class="block text-[10px] text-slate-400">Unique, business-friendly code visible to advertisers.</span>
                    </div>
                </div>
            </div>

            <!-- Section 3: Tech & Media Specifications -->
            <div>
                <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider border-b border-slate-100 dark:border-slate-750 pb-2 mb-4">Technical & Media Specifications</h3>
                <div class="grid grid-cols-1 md:grid-cols-4 gap-5">
                    <!-- Orientation -->
                    <div class="space-y-1.5">
                        <label class="text-xs font-bold text-slate-500 dark:text-slate-400">Orientation <span class="text-red-500">*</span></label>
                        <select name="orientation" required class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-850 rounded-xl text-sm text-slate-700 dark:text-slate-200 focus:outline-none focus:border-[#1155CC] transition-all">
                            <option value="Landscape" {{ old('orientation') === 'Landscape' ? 'selected' : '' }}>Landscape</option>
                            <option value="Portrait" {{ old('orientation') === 'Portrait' ? 'selected' : '' }}>Portrait</option>
                            <option value="Square" {{ old('orientation') === 'Square' ? 'selected' : '' }}>Square</option>
                        </select>
                    </div>

                    <!-- Resolution -->
                    <div class="space-y-1.5">
                        <label class="text-xs font-bold text-slate-500 dark:text-slate-400">Resolution</label>
                        <input type="text" name="resolution" value="{{ old('resolution', '1920x1080') }}" placeholder="e.g. 1920x1080" class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-850 rounded-xl text-sm text-slate-700 dark:text-slate-200 focus:outline-none focus:border-[#1155CC] transition-all">
                    </div>

                    <!-- Width (cm) -->
                    <div class="space-y-1.5">
                        <label class="text-xs font-bold text-slate-500 dark:text-slate-400">Physical Width (cm)</label>
                        <input type="number" name="screen_width" value="{{ old('screen_width') }}" min="0" placeholder="Width" class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-850 rounded-xl text-sm text-slate-700 dark:text-slate-200 focus:outline-none focus:border-[#1155CC] transition-all">
                    </div>

                    <!-- Height (cm) -->
                    <div class="space-y-1.5">
                        <label class="text-xs font-bold text-slate-500 dark:text-slate-400">Physical Height (cm)</label>
                        <input type="number" name="screen_height" value="{{ old('screen_height') }}" min="0" placeholder="Height" class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-850 rounded-xl text-sm text-slate-700 dark:text-slate-200 focus:outline-none focus:border-[#1155CC] transition-all">
                    </div>

                    <!-- Supported formats -->
                    <div class="space-y-1.5 md:col-span-2">
                        <label class="text-xs font-bold text-slate-500 dark:text-slate-400">Supported Formats <span class="text-red-500">*</span></label>
                        <input type="text" name="supported_formats" value="{{ old('supported_formats', 'MP4,JPG,PNG') }}" required placeholder="e.g. MP4,JPG,PNG" class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-850 rounded-xl text-sm text-slate-700 dark:text-slate-200 focus:outline-none focus:border-[#1155CC] transition-all">
                        <span class="block text-[10px] text-slate-400">Comma-separated media extension codes.</span>
                    </div>

                    <!-- Max video duration -->
                    <div class="space-y-1.5 md:col-span-2">
                        <label class="text-xs font-bold text-slate-500 dark:text-slate-400">Max Video Duration (seconds)</label>
                        <input type="number" name="max_video_duration" value="{{ old('max_video_duration', 15) }}" min="0" placeholder="e.g. 15" class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-850 rounded-xl text-sm text-slate-700 dark:text-slate-200 focus:outline-none focus:border-[#1155CC] transition-all">
                        <span class="block text-[10px] text-slate-400">Leave blank or set to 0 for image-only static displays.</span>
                    </div>
                </div>
            </div>

            <!-- Section 4: Operational Metrics -->
            <div>
                <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider border-b border-slate-100 dark:border-slate-750 pb-2 mb-4">Operations</h3>
                <div class="grid grid-cols-1 md:grid-cols-4 gap-5">
                    <!-- Daily Impressions -->
                    <div class="space-y-1.5">
                        <label class="text-xs font-bold text-slate-500 dark:text-slate-400">Daily Impressions <span class="text-red-500">*</span></label>
                        <input type="number" name="daily_impressions" value="{{ old('daily_impressions', 5000) }}" required min="0" placeholder="Impressions count" class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-850 rounded-xl text-sm text-slate-700 dark:text-slate-200 focus:outline-none focus:border-[#1155CC] transition-all">
                    </div>

                    <!-- Operating Hours -->
                    <div class="space-y-1.5">
                        <label class="text-xs font-bold text-slate-500 dark:text-slate-400">Operating Hours</label>
                        <input type="text" name="operating_hours" value="{{ old('operating_hours', '10:00 AM - 10:00 PM') }}" placeholder="Hours" class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-850 rounded-xl text-sm text-slate-700 dark:text-slate-200 focus:outline-none focus:border-[#1155CC] transition-all">
                    </div>

                    <!-- Status -->
                    <div class="space-y-1.5">
                        <label class="text-xs font-bold text-slate-500 dark:text-slate-400">Status <span class="text-red-500">*</span></label>
                        <select name="status" required class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-850 rounded-xl text-sm text-slate-700 dark:text-slate-200 focus:outline-none focus:border-[#1155CC] transition-all">
                            <option value="active" {{ old('status', 'active') === 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ old('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                            <option value="maintenance" {{ old('status') === 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                        </select>
                    </div>

                    <!-- Availability Status -->
                    <div class="space-y-1.5">
                        <label class="text-xs font-bold text-slate-500 dark:text-slate-400">Availability Status <span class="text-red-500">*</span></label>
                        <select name="availability_status" required class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-850 rounded-xl text-sm text-slate-700 dark:text-slate-200 focus:outline-none focus:border-[#1155CC] transition-all">
                            <option value="available" {{ old('availability_status', 'available') === 'available' ? 'selected' : '' }}>Available</option>
                            <option value="occupied" {{ old('availability_status') === 'occupied' ? 'selected' : '' }}>Occupied</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Description -->
            <div class="space-y-1.5">
                <label class="text-xs font-bold text-slate-500 dark:text-slate-400">Screen Description</label>
                <textarea name="description" rows="3" placeholder="Describe the physical location details, exposure, visibility patterns..." class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-850 rounded-xl text-sm text-slate-700 dark:text-slate-200 focus:outline-none focus:border-[#1155CC] transition-all">{{ old('description') }}</textarea>
            </div>

            <!-- Section 5: Photos -->
            <div>
                <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider border-b border-slate-100 dark:border-slate-750 pb-2 mb-4">Screen Photos</h3>
                <div class="space-y-3">
                    <div class="flex items-center justify-center w-full">
                        <label class="flex flex-col items-center justify-center w-full h-36 border-2 border-dashed border-slate-200 hover:border-[#1155CC] dark:border-slate-700 dark:hover:border-slate-650 rounded-2xl cursor-pointer bg-slate-50/50 dark:bg-slate-900/30 hover:bg-slate-50 dark:hover:bg-slate-900 transition-all">
                            <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                <i class="bi bi-cloud-arrow-up text-2xl text-slate-400"></i>
                                <p class="mb-1 text-sm font-semibold text-slate-600 dark:text-slate-400">Click to upload photos</p>
                                <p class="text-xs text-slate-400">Upload multiple JPG, PNG images (Max 5MB each)</p>
                            </div>
                            <input type="file" name="images[]" id="imageInput" multiple class="hidden" accept="image/*">
                        </label>
                    </div>
                    <!-- Previews grid -->
                    <div id="imagePreviews" class="grid grid-cols-2 sm:grid-cols-4 lg:grid-cols-6 gap-3"></div>
                </div>
            </div>

            <!-- Action buttons -->
            <div class="flex items-center justify-end gap-3 border-t border-slate-100 dark:border-slate-750 pt-6 mt-8">
                <a href="{{ route('admin.screens.index') }}" class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-750 dark:bg-slate-700 dark:hover:bg-slate-650 dark:text-slate-250 rounded-xl text-sm font-semibold transition-all">
                    Cancel
                </a>
                <button type="submit" class="px-6 py-2.5 bg-[#1155CC] hover:bg-blue-700 text-white rounded-xl text-sm font-semibold transition-all shadow-md shadow-blue-500/10 flex items-center gap-2">
                    <i class="bi bi-check-lg"></i> Save Screen
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    document.getElementById('imageInput').addEventListener('change', function(event) {
        const previewContainer = document.getElementById('imagePreviews');
        previewContainer.innerHTML = '';
        
        const files = event.target.files;
        if(files) {
            Array.from(files).forEach((file, index) => {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const card = document.createElement('div');
                    card.className = 'relative w-full aspect-video rounded-xl overflow-hidden border border-slate-200 dark:border-slate-800 bg-slate-100 dark:bg-slate-900 group shadow-sm';
                    
                    const img = document.createElement('img');
                    img.src = e.target.result;
                    img.className = 'w-full h-full object-cover';
                    
                    const badge = document.createElement('span');
                    badge.className = 'absolute top-1.5 left-1.5 bg-[#1155CC] text-white text-[9px] font-bold px-1.5 py-0.5 rounded shadow';
                    badge.textContent = index === 0 ? 'PRIMARY' : `Image ${index + 1}`;
                    
                    card.appendChild(img);
                    card.appendChild(badge);
                    previewContainer.appendChild(card);
                };
                reader.readAsDataURL(file);
            });
        }
    });
</script>
@endsection
