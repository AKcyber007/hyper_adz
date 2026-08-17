@extends('layouts.partner')

@section('title', 'Add Screen')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="flex items-center gap-3">
        <a href="{{ route('partner.screens.index') }}" class="w-8 h-8 rounded-lg bg-white border border-slate-200 flex items-center justify-center text-slate-600 hover:text-slate-900 transition-all">
            <i class="bi bi-arrow-left"></i>
        </a>
        <div>
            <h2 class="text-xl font-extrabold tracking-tight text-slate-900">Add New Screen</h2>
            <p class="text-xs text-slate-500 mt-0.5">Submit screen information for review and registration.</p>
        </div>
    </div>

    @if($locations->isEmpty())
        <div class="bg-white border border-slate-200 p-8 rounded-[32px] text-center space-y-4">
            <i class="bi bi-exclamation-triangle text-amber-500 text-3xl"></i>
            <h3 class="text-sm font-bold text-slate-900">No active or approved locations available</h3>
            <p class="text-xs text-slate-500 max-w-sm mx-auto">You must have an approved or active location before registering screens. Go to My Locations to request locations first.</p>
            <a href="{{ route('partner.locations.create') }}" class="inline-block px-4 py-2 bg-blue-600 hover:bg-blue-500 text-xs font-bold text-slate-900 rounded-xl transition-all">Add Location</a>
        </div>
    @else
        <form method="POST" action="{{ route('partner.screens.store') }}" enctype="multipart/form-data" class="bg-white border border-slate-200 rounded-[32px] p-6 sm:p-8 space-y-6">
            @csrf

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <!-- Parent Location -->
                <div class="space-y-1.5 col-span-2">
                    <label for="location_id" class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider">Parent Location</label>
                    <select id="location_id" name="location_id" required class="w-full px-4 py-2.5 bg-white border border-slate-200 rounded-xl text-xs text-slate-600 focus:outline-none focus:border-blue-600 transition-all">
                        <option value="">Select Location</option>
                        @foreach($locations as $loc)
                            <option value="{{ $loc->id }}" {{ old('location_id', request('location_id')) == $loc->id ? 'selected' : '' }}>{{ $loc->name }} ({{ $loc->location_code }})</option>
                        @endforeach
                    </select>
                    @error('location_id') <p class="text-xxs text-rose-455 font-bold mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Screen Name -->
                <div class="space-y-1.5 col-span-2">
                    <label for="name" class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider">Screen Name / Label</label>
                    <input type="text" id="name" name="name" value="{{ old('name') }}" required placeholder="e.g. Lobby Entrance LED Wall" class="w-full px-4 py-2.5 bg-white border border-slate-200 rounded-xl text-xs text-slate-205 focus:outline-none focus:border-blue-600 focus:ring-1 focus:ring-blue-600 transition-all">
                    @error('name') <p class="text-xxs text-rose-455 font-bold mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Screen Identifier (unique/external key) -->
                <div class="space-y-1.5">
                    <label for="screen_identifier" class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider">Screen Identifier (Optional)</label>
                    <input type="text" id="screen_identifier" name="screen_identifier" value="{{ old('screen_identifier') }}" placeholder="e.g. LOBBY-01-COORD" class="w-full px-4 py-2.5 bg-white border border-slate-200 rounded-xl text-xs text-slate-205 focus:outline-none focus:border-blue-600 focus:ring-1 focus:ring-blue-600 transition-all">
                    <p class="text-[9px] text-slate-500">For binding with remote players / CMS systems.</p>
                    @error('screen_identifier') <p class="text-xxs text-rose-455 font-bold mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Screen Type -->
                <div class="space-y-1.5">
                    <label for="screen_type_id" class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider">Screen Type</label>
                    <select id="screen_type_id" name="screen_type_id" required class="w-full px-4 py-2.5 bg-white border border-slate-200 rounded-xl text-xs text-slate-600 focus:outline-none focus:border-blue-600 transition-all">
                        <option value="">Select Screen Type</option>
                        @foreach($screenTypes as $st)
                            <option value="{{ $st->id }}" {{ old('screen_type_id') == $st->id ? 'selected' : '' }}>{{ $st->name }}</option>
                        @endforeach
                    </select>
                    @error('screen_type_id') <p class="text-xxs text-rose-455 font-bold mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Orientation -->
                <div class="space-y-1.5">
                    <label for="orientation" class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider">Orientation</label>
                    <select id="orientation" name="orientation" required class="w-full px-4 py-2.5 bg-white border border-slate-200 rounded-xl text-xs text-slate-600 focus:outline-none focus:border-blue-600 transition-all">
                        <option value="Landscape" {{ old('orientation') == 'Landscape' ? 'selected' : '' }}>Landscape (16:9)</option>
                        <option value="Portrait" {{ old('orientation') == 'Portrait' ? 'selected' : '' }}>Portrait (9:16)</option>
                    </select>
                    @error('orientation') <p class="text-xxs text-rose-455 font-bold mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Resolution -->
                <div class="space-y-1.5">
                    <label for="resolution" class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider">Resolution</label>
                    <input type="text" id="resolution" name="resolution" value="{{ old('resolution', '1920x1080') }}" placeholder="e.g. 1920x1080" class="w-full px-4 py-2.5 bg-white border border-slate-200 rounded-xl text-xs text-slate-205 focus:outline-none focus:border-blue-600 focus:ring-1 focus:ring-blue-600 transition-all">
                    @error('resolution') <p class="text-xxs text-rose-455 font-bold mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Width (px/mm) -->
                <div class="space-y-1.5">
                    <label for="screen_width" class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider">Physical Width (in inches)</label>
                    <input type="number" id="screen_width" name="screen_width" value="{{ old('screen_width') }}" placeholder="e.g. 55" class="w-full px-4 py-2.5 bg-white border border-slate-200 rounded-xl text-xs text-slate-205 focus:outline-none focus:border-blue-600 focus:ring-1 focus:ring-blue-600 transition-all">
                    @error('screen_width') <p class="text-xxs text-rose-455 font-bold mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Height -->
                <div class="space-y-1.5">
                    <label for="screen_height" class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider">Physical Height (in inches)</label>
                    <input type="number" id="screen_height" name="screen_height" value="{{ old('screen_height') }}" placeholder="e.g. 32" class="w-full px-4 py-2.5 bg-white border border-slate-200 rounded-xl text-xs text-slate-205 focus:outline-none focus:border-blue-600 focus:ring-1 focus:ring-blue-600 transition-all">
                    @error('screen_height') <p class="text-xxs text-rose-455 font-bold mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Daily Impressions -->
                <div class="space-y-1.5">
                    <label for="daily_impressions" class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider">Daily Impressions (Est.)</label>
                    <input type="number" id="daily_impressions" name="daily_impressions" value="{{ old('daily_impressions', 0) }}" required min="0" class="w-full px-4 py-2.5 bg-white border border-slate-200 rounded-xl text-xs text-slate-205 focus:outline-none focus:border-blue-600 focus:ring-1 focus:ring-blue-600 transition-all">
                    @error('daily_impressions') <p class="text-xxs text-rose-455 font-bold mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Operating Hours -->
                <div class="space-y-1.5">
                    <label for="operating_hours" class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider">Operating Hours</label>
                    <input type="text" id="operating_hours" name="operating_hours" value="{{ old('operating_hours', '10:00 AM - 10:00 PM') }}" placeholder="e.g. 10:00 AM - 10:00 PM" class="w-full px-4 py-2.5 bg-white border border-slate-200 rounded-xl text-xs text-slate-205 focus:outline-none focus:border-blue-600 focus:ring-1 focus:ring-blue-600 transition-all">
                    @error('operating_hours') <p class="text-xxs text-rose-455 font-bold mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Formats -->
                <div class="space-y-1.5">
                    <label for="supported_formats" class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider">Supported Formats</label>
                    <input type="text" id="supported_formats" name="supported_formats" value="{{ old('supported_formats', 'MP4,JPG,PNG') }}" required placeholder="e.g. MP4,JPG,PNG" class="w-full px-4 py-2.5 bg-white border border-slate-200 rounded-xl text-xs text-slate-205 focus:outline-none focus:border-blue-600 focus:ring-1 focus:ring-blue-600 transition-all">
                    @error('supported_formats') <p class="text-xxs text-rose-455 font-bold mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Video Duration Limit -->
                <div class="space-y-1.5">
                    <label for="max_video_duration" class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider">Max Video Duration (sec)</label>
                    <input type="number" id="max_video_duration" name="max_video_duration" value="{{ old('max_video_duration', 15) }}" required min="0" class="w-full px-4 py-2.5 bg-white border border-slate-200 rounded-xl text-xs text-slate-205 focus:outline-none focus:border-blue-600 focus:ring-1 focus:ring-blue-600 transition-all">
                    @error('max_video_duration') <p class="text-xxs text-rose-455 font-bold mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Description -->
                <div class="space-y-1.5 col-span-2">
                    <label for="description" class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider">Screen Placement Details</label>
                    <textarea id="description" name="description" rows="3" placeholder="Describe where the screen is installed, size visibility context, target audience demographic, etc." class="w-full px-4 py-2.5 bg-white border border-slate-200 rounded-xl text-xs text-slate-205 focus:outline-none focus:border-blue-600 focus:ring-1 focus:ring-blue-600 transition-all">{{ old('description') }}</textarea>
                    @error('description') <p class="text-xxs text-rose-455 font-bold mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Media Upload -->
                <div class="space-y-1.5 col-span-2">
                    <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider">Upload Screen Placement Images</label>
                    <input type="file" name="images[]" multiple accept="image/*" class="w-full text-xs text-slate-500 bg-white border border-slate-200 rounded-xl px-4 py-2.5 focus:outline-none focus:border-blue-600 transition-all">
                    <p class="text-[10px] text-slate-500 mt-1">Upload images of the physical screen displaying mock content or showing location context (Max 5MB per file).</p>
                    @error('images') <p class="text-xxs text-rose-455 font-bold mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="flex items-center justify-end gap-3 pt-6 border-t border-slate-200">
                <a href="{{ route('partner.screens.index') }}" class="px-5 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-750 text-xs font-bold text-slate-355 transition-all">Cancel</a>
                <button type="submit" class="px-5 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-500 text-xs font-bold text-slate-900 transition-all shadow-lg shadow-blue-500/10">Submit Screen for Review</button>
            </div>
        </form>
    @endif
</div>
@endsection
