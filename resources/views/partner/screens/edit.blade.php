@extends('layouts.partner')

@section('title', 'Edit Screen')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="flex items-center gap-3">
        <a href="{{ route('partner.screens.index') }}" class="w-8 h-8 rounded-lg bg-white border border-slate-200 flex items-center justify-center text-slate-355 hover:text-slate-900 transition-all">
            <i class="bi bi-arrow-left"></i>
        </a>
        <div>
            <h2 class="text-xl font-extrabold tracking-tight text-slate-900">Edit Screen: {{ $screen->name }}</h2>
            <p class="text-xs text-slate-500 mt-0.5">Modify parameters, upload media, or adjust active health states.</p>
        </div>
    </div>

    <form method="POST" action="{{ route('partner.screens.update', $screen->id) }}" enctype="multipart/form-data" class="bg-white border border-slate-200 rounded-[32px] p-6 sm:p-8 space-y-6">
        @csrf
        @method('PUT')

        @if($screen->status === 'rejected')
            <div class="bg-rose-500/10 border border-rose-500/20 rounded-2xl p-5 space-y-2">
                <div class="flex items-center gap-2 text-rose-455 text-xs font-bold">
                    <i class="bi bi-x-circle-fill text-lg"></i>
                    <span>This Screen registration request was Rejected</span>
                </div>
                <p class="text-xs text-slate-600 pl-7 leading-relaxed">
                    <strong>Reason:</strong> {{ $screen->rejection_reason ?? 'No feedback provided.' }}
                </p>
                <p class="text-[10px] text-slate-500 pl-7">
                    Rejected on {{ $screen->rejected_at ? $screen->rejected_at->format('d-M-Y H:i') : '' }} by Admin. Saving modifications will automatically resubmit it for verification.
                </p>
            </div>
        @endif

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
            <!-- Parent Location -->
            <div class="space-y-1.5 col-span-2">
                <label for="location_id" class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider">Parent Location</label>
                <select id="location_id" name="location_id" required class="w-full px-4 py-2.5 bg-white border border-slate-200 rounded-xl text-xs text-slate-600 focus:outline-none focus:border-blue-600 transition-all">
                    @foreach($locations as $loc)
                        <option value="{{ $loc->id }}" {{ old('location_id', $screen->location_id) == $loc->id ? 'selected' : '' }}>{{ $loc->name }} ({{ $loc->location_code }})</option>
                    @endforeach
                </select>
                @error('location_id') <p class="text-xxs text-rose-455 font-bold mt-1">{{ $message }}</p> @enderror
            </div>

            <!-- Screen Name -->
            <div class="space-y-1.5 col-span-2">
                <label for="name" class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider">Screen Name / Label</label>
                <input type="text" id="name" name="name" value="{{ old('name', $screen->name) }}" required class="w-full px-4 py-2.5 bg-white border border-slate-200 rounded-xl text-xs text-slate-205 focus:outline-none focus:border-blue-600 focus:ring-1 focus:ring-blue-600 transition-all">
                @error('name') <p class="text-xxs text-rose-455 font-bold mt-1">{{ $message }}</p> @enderror
            </div>

            <!-- Screen Identifier -->
            <div class="space-y-1.5">
                <label for="screen_identifier" class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider">Screen Identifier (Optional)</label>
                <input type="text" id="screen_identifier" name="screen_identifier" value="{{ old('screen_identifier', $screen->screen_identifier) }}" class="w-full px-4 py-2.5 bg-white border border-slate-200 rounded-xl text-xs text-slate-205 focus:outline-none focus:border-blue-600 focus:ring-1 focus:ring-blue-600 transition-all">
                @error('screen_identifier') <p class="text-xxs text-rose-455 font-bold mt-1">{{ $message }}</p> @enderror
            </div>

            <!-- Screen Type -->
            <div class="space-y-1.5">
                <label for="screen_type_id" class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider">Screen Type</label>
                <select id="screen_type_id" name="screen_type_id" required class="w-full px-4 py-2.5 bg-white border border-slate-200 rounded-xl text-xs text-slate-600 focus:outline-none focus:border-blue-600 transition-all">
                    @foreach($screenTypes as $st)
                        <option value="{{ $st->id }}" {{ old('screen_type_id', $screen->screen_type_id) == $st->id ? 'selected' : '' }}>{{ $st->name }}</option>
                    @endforeach
                </select>
                @error('screen_type_id') <p class="text-xxs text-rose-455 font-bold mt-1">{{ $message }}</p> @enderror
            </div>

            <!-- Health Status / Operational State -->
            <div class="space-y-1.5">
                <label for="status" class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider">Screen Health / State</label>
                @if(in_array($screen->status, ['pending', 'rejected']))
                    <div class="px-4 py-2.5 bg-white/60 border border-slate-200 rounded-xl text-xs text-slate-500 font-mono">
                        {{ ucfirst($screen->status) }} (Locked until approved)
                    </div>
                @else
                    <select id="status" name="status" required class="w-full px-4 py-2.5 bg-white border border-slate-200 rounded-xl text-xs text-slate-600 focus:outline-none focus:border-blue-600 transition-all">
                        <option value="active" {{ old('status', $screen->status) == 'active' ? 'selected' : '' }}>Active / Online</option>
                        <option value="offline" {{ old('status', $screen->status) == 'offline' ? 'selected' : '' }}>Offline</option>
                        <option value="maintenance" {{ old('status', $screen->status) == 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                    </select>
                @endif
                @error('status') <p class="text-xxs text-rose-455 font-bold mt-1">{{ $message }}</p> @enderror
            </div>

            <!-- Orientation -->
            <div class="space-y-1.5">
                <label for="orientation" class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider">Orientation</label>
                <select id="orientation" name="orientation" required class="w-full px-4 py-2.5 bg-white border border-slate-200 rounded-xl text-xs text-slate-600 focus:outline-none focus:border-blue-600 transition-all">
                    <option value="Landscape" {{ old('orientation', $screen->orientation) == 'Landscape' ? 'selected' : '' }}>Landscape (16:9)</option>
                    <option value="Portrait" {{ old('orientation', $screen->orientation) == 'Portrait' ? 'selected' : '' }}>Portrait (9:16)</option>
                </select>
                @error('orientation') <p class="text-xxs text-rose-455 font-bold mt-1">{{ $message }}</p> @enderror
            </div>

            <!-- Resolution -->
            <div class="space-y-1.5">
                <label for="resolution" class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider">Resolution</label>
                <input type="text" id="resolution" name="resolution" value="{{ old('resolution', $screen->resolution) }}" class="w-full px-4 py-2.5 bg-white border border-slate-200 rounded-xl text-xs text-slate-205 focus:outline-none focus:border-blue-600 focus:ring-1 focus:ring-blue-600 transition-all">
                @error('resolution') <p class="text-xxs text-rose-455 font-bold mt-1">{{ $message }}</p> @enderror
            </div>

            <!-- Width -->
            <div class="space-y-1.5">
                <label for="screen_width" class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider">Physical Width (in inches)</label>
                <input type="number" id="screen_width" name="screen_width" value="{{ old('screen_width', $screen->screen_width) }}" class="w-full px-4 py-2.5 bg-white border border-slate-200 rounded-xl text-xs text-slate-205 focus:outline-none focus:border-blue-600 focus:ring-1 focus:ring-blue-600 transition-all">
                @error('screen_width') <p class="text-xxs text-rose-455 font-bold mt-1">{{ $message }}</p> @enderror
            </div>

            <!-- Height -->
            <div class="space-y-1.5">
                <label for="screen_height" class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider">Physical Height (in inches)</label>
                <input type="number" id="screen_height" name="screen_height" value="{{ old('screen_height', $screen->screen_height) }}" class="w-full px-4 py-2.5 bg-white border border-slate-200 rounded-xl text-xs text-slate-205 focus:outline-none focus:border-blue-600 focus:ring-1 focus:ring-blue-600 transition-all">
                @error('screen_height') <p class="text-xxs text-rose-455 font-bold mt-1">{{ $message }}</p> @enderror
            </div>

            <!-- Daily Impressions -->
            <div class="space-y-1.5">
                <label for="daily_impressions" class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider">Daily Impressions (Est.)</label>
                <input type="number" id="daily_impressions" name="daily_impressions" value="{{ old('daily_impressions', $screen->daily_impressions) }}" required min="0" class="w-full px-4 py-2.5 bg-white border border-slate-200 rounded-xl text-xs text-slate-205 focus:outline-none focus:border-blue-600 focus:ring-1 focus:ring-blue-600 transition-all">
                @error('daily_impressions') <p class="text-xxs text-rose-455 font-bold mt-1">{{ $message }}</p> @enderror
            </div>

            <!-- Operating Hours -->
            <div class="space-y-1.5">
                <label for="operating_hours" class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider">Operating Hours</label>
                <input type="text" id="operating_hours" name="operating_hours" value="{{ old('operating_hours', $screen->operating_hours) }}" class="w-full px-4 py-2.5 bg-white border border-slate-200 rounded-xl text-xs text-slate-205 focus:outline-none focus:border-blue-600 focus:ring-1 focus:ring-blue-600 transition-all">
                @error('operating_hours') <p class="text-xxs text-rose-455 font-bold mt-1">{{ $message }}</p> @enderror
            </div>

            <!-- Formats -->
            <div class="space-y-1.5">
                <label for="supported_formats" class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider">Supported Formats</label>
                <input type="text" id="supported_formats" name="supported_formats" value="{{ old('supported_formats', $screen->supported_formats) }}" required class="w-full px-4 py-2.5 bg-white border border-slate-200 rounded-xl text-xs text-slate-205 focus:outline-none focus:border-blue-600 focus:ring-1 focus:ring-blue-600 transition-all">
                @error('supported_formats') <p class="text-xxs text-rose-455 font-bold mt-1">{{ $message }}</p> @enderror
            </div>

            <!-- Video Duration -->
            <div class="space-y-1.5">
                <label for="max_video_duration" class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider">Max Video Duration (sec)</label>
                <input type="number" id="max_video_duration" name="max_video_duration" value="{{ old('max_video_duration', $screen->max_video_duration) }}" required min="0" class="w-full px-4 py-2.5 bg-white border border-slate-200 rounded-xl text-xs text-slate-205 focus:outline-none focus:border-blue-600 focus:ring-1 focus:ring-blue-600 transition-all">
                @error('max_video_duration') <p class="text-xxs text-rose-455 font-bold mt-1">{{ $message }}</p> @enderror
            </div>

            <!-- Description -->
            <div class="space-y-1.5 col-span-2">
                <label for="description" class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider">Screen Placement Details</label>
                <textarea id="description" name="description" rows="3" class="w-full px-4 py-2.5 bg-white border border-slate-200 rounded-xl text-xs text-slate-205 focus:outline-none focus:border-blue-600 focus:ring-1 focus:ring-blue-600 transition-all">{{ old('description', $screen->description) }}</textarea>
                @error('description') <p class="text-xxs text-rose-455 font-bold mt-1">{{ $message }}</p> @enderror
            </div>

            <!-- Manage Screen Images -->
            @if($screen->images->isNotEmpty())
                <div class="space-y-3 col-span-2">
                    <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider">Manage Screen Images</label>
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                        @foreach($screen->images as $img)
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

            <!-- Image Upload -->
            <div class="space-y-1.5 col-span-2">
                <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider">Add More Images</label>
                <input type="file" name="images[]" multiple accept="image/*" class="w-full text-xs text-slate-500 bg-white border border-slate-200 rounded-xl px-4 py-2.5 focus:outline-none focus:border-blue-600 transition-all">
                @error('images') <p class="text-xxs text-rose-455 font-bold mt-1">{{ $message }}</p> @enderror
            </div>
        </div>

        <div class="flex items-center justify-end gap-3 pt-6 border-t border-slate-200">
            <a href="{{ route('partner.screens.index') }}" class="px-5 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-750 text-xs font-bold text-slate-600 transition-all">Cancel</a>
            <button type="submit" class="px-5 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-500 text-xs font-bold text-slate-900 transition-all shadow-lg shadow-blue-500/10">Save Updates</button>
        </div>
    </form>
</div>
@endsection
