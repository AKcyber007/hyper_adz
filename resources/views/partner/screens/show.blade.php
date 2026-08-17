@extends('layouts.partner')

@section('title', 'Screen Details')

@section('content')
<div class="max-w-6xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-3">
            <a href="{{ route('partner.screens.index') }}" class="w-8 h-8 rounded-lg bg-white border border-slate-200 flex items-center justify-center text-slate-600 hover:text-slate-900 transition-all">
                <i class="bi bi-arrow-left"></i>
            </a>
            <div>
                <h2 class="text-xl font-extrabold tracking-tight text-slate-900">{{ $screen->name }}</h2>
                <p class="text-xs text-slate-500 mt-0.5">Screen Code: <span class="font-mono font-bold">{{ $screen->screen_code }}</span></p>
            </div>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('partner.screens.edit', $screen->id) }}" class="px-4 py-2 bg-slate-100 hover:bg-slate-750 text-xs font-bold text-slate-600 border border-slate-200 rounded-xl transition-all">
                <i class="bi bi-pencil me-1.5"></i> Edit Screen
            </a>
        </div>
    </div>

    <!-- Rejection Banner -->
    @if($screen->status === 'rejected')
        <div class="bg-rose-500/10 border border-rose-500/20 rounded-2xl p-5 space-y-2">
            <div class="flex items-center gap-2 text-rose-455 text-xs font-bold">
                <i class="bi bi-x-circle-fill text-lg"></i>
                <span>Screen Registration Rejected</span>
            </div>
            <p class="text-xs text-slate-600 pl-7 leading-relaxed">
                <strong>Reason:</strong> {{ $screen->rejection_reason ?? 'No detailed feedback provided by admin.' }}
            </p>
            <div class="text-[10px] text-slate-500 pl-7 flex items-center gap-4">
                <span><strong>Rejected By:</strong> {{ $screen->rejectedByUser ? $screen->rejectedByUser->name : 'Admin' }}</span>
                <span><strong>Date:</strong> {{ $screen->rejected_at ? $screen->rejected_at->format('d-M-Y H:i') : '' }}</span>
            </div>
        </div>
    @elseif($screen->status === 'pending')
        <div class="bg-amber-500/10 border border-amber-500/20 rounded-2xl p-5 flex items-center gap-3 text-amber-400 text-xs font-semibold">
            <i class="bi bi-clock-fill text-lg"></i>
            <div>
                <span>This screen registration request is currently pending admin validation.</span>
                <p class="text-[10px] text-slate-500 font-normal mt-0.5">We will verify technical resolution parameters and location binding shortly.</p>
            </div>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Left: Technical Parameters -->
        <div class="space-y-6 lg:col-span-2">
            <div class="bg-white border border-slate-200 rounded-[32px] p-6 sm:p-8 space-y-6">
                <h3 class="text-sm font-bold text-slate-900 border-b border-slate-200 pb-4 flex items-center gap-2">
                    <i class="bi bi-cpu-fill text-blue-450"></i> Technical Specifications
                </h3>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 text-xs">
                    <div class="space-y-1">
                        <span class="block text-[10px] text-slate-455 font-bold uppercase tracking-wider">Parent Location</span>
                        <a href="{{ route('partner.locations.show', $screen->location_id) }}" class="block text-blue-400 hover:text-blue-300 font-semibold transition-colors">{{ $screen->location ? $screen->location->name : 'N/A' }}</a>
                    </div>
                    <div class="space-y-1">
                        <span class="block text-[10px] text-slate-455 font-bold uppercase tracking-wider">Screen Type</span>
                        <span class="block text-slate-700 font-semibold">{{ $screen->type ? $screen->type->name : 'N/A' }}</span>
                    </div>
                    <div class="space-y-1">
                        <span class="block text-[10px] text-slate-455 font-bold uppercase tracking-wider">Orientation</span>
                        <span class="block text-slate-700 font-semibold">{{ $screen->orientation }}</span>
                    </div>
                    <div class="space-y-1">
                        <span class="block text-[10px] text-slate-455 font-bold uppercase tracking-wider">Resolution</span>
                        <span class="block text-slate-700 font-semibold font-mono">{{ $screen->resolution ?: 'N/A' }}</span>
                    </div>
                    <div class="space-y-1">
                        <span class="block text-[10px] text-slate-455 font-bold uppercase tracking-wider">Physical Dimensions</span>
                        <span class="block text-slate-700 font-semibold">{{ $screen->screen_width ?: '—' }}” (W) x {{ $screen->screen_height ?: '—' }}” (H)</span>
                    </div>
                    <div class="space-y-1">
                        <span class="block text-[10px] text-slate-455 font-bold uppercase tracking-wider">Daily Impressions (Est.)</span>
                        <span class="block text-slate-700 font-semibold">{{ number_format($screen->daily_impressions) }} views</span>
                    </div>
                    <div class="space-y-1">
                        <span class="block text-[10px] text-slate-455 font-bold uppercase tracking-wider">Supported Formats</span>
                        <span class="block text-slate-700 font-semibold font-mono">{{ $screen->supported_formats }}</span>
                    </div>
                    <div class="space-y-1">
                        <span class="block text-[10px] text-slate-455 font-bold uppercase tracking-wider">Max Video Duration</span>
                        <span class="block text-slate-700 font-semibold">{{ $screen->max_video_duration }} seconds</span>
                    </div>
                    @if($screen->screen_identifier)
                        <div class="space-y-1">
                            <span class="block text-[10px] text-slate-455 font-bold uppercase tracking-wider">CMS Screen Identifier</span>
                            <span class="block text-slate-700 font-mono font-semibold">{{ $screen->screen_identifier }}</span>
                        </div>
                    @endif
                    @if($screen->description)
                        <div class="space-y-1 col-span-2">
                            <span class="block text-[10px] text-slate-455 font-bold uppercase tracking-wider">Placement Details</span>
                            <span class="block text-slate-600 leading-relaxed">{{ $screen->description }}</span>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Right: Screen health monitoring & Images -->
        <div class="space-y-6">
            <!-- Health Monitoring Status Card -->
            <div class="bg-white border border-slate-200 rounded-[32px] p-6 space-y-5">
                <h3 class="text-sm font-bold text-slate-900 border-b border-slate-200 pb-4 flex items-center gap-2">
                    <i class="bi bi-activity text-emerald-450"></i> Health Monitoring
                </h3>
                <div class="space-y-4 text-xs">
                    <div class="flex justify-between items-center">
                        <span class="text-slate-500">Device Status:</span>
                        <x-status-badge :status="$screen->status" />
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-slate-500">Device Availability:</span>
                        <span class="px-2 py-0.5 bg-slate-100 border border-slate-200 text-slate-600 rounded font-semibold">{{ ucfirst($screen->availability_status) }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-500">Operating Hours:</span>
                        <span class="text-slate-700 font-semibold text-right">{{ $screen->operating_hours ?: 'N/A' }}</span>
                    </div>
                </div>
            </div>

            <!-- Image Gallery -->
            <div class="bg-white border border-slate-200 rounded-[32px] p-6 space-y-5">
                <h3 class="text-sm font-bold text-slate-900 border-b border-slate-200 pb-4 flex items-center gap-2">
                    <i class="bi bi-images text-blue-455"></i> Screen Media Gallery
                </h3>

                @if($screen->images->isEmpty())
                    <div class="py-8 text-center bg-slate-100 border border-slate-200 rounded-2xl">
                        <i class="bi bi-image text-slate-600 text-2xl"></i>
                        <p class="text-xxs text-slate-500 mt-1">No images uploaded.</p>
                    </div>
                @else
                    <div class="grid grid-cols-2 gap-3">
                        @foreach($screen->images as $img)
                            <div class="relative rounded-xl overflow-hidden border border-slate-200 group cursor-pointer" onclick="window.open('{{ Storage::url($img->image_path) }}', '_blank')">
                                <img src="{{ Storage::url($img->image_path) }}" class="w-full h-20 object-cover group-hover:scale-105 transition-transform duration-300">
                                @if($img->is_primary)
                                    <span class="absolute top-1.5 left-1.5 px-1.5 py-0.5 bg-blue-600/90 text-[8px] font-bold uppercase rounded text-slate-900 tracking-wider">Primary</span>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
