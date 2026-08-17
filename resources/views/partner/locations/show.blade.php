@extends('layouts.partner')

@section('title', 'Location Details')

@section('content')
<div class="max-w-6xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-3">
            <a href="{{ route('partner.locations.index') }}" class="w-8 h-8 rounded-lg bg-white border border-slate-200 flex items-center justify-center text-slate-600 hover:text-slate-900 transition-all">
                <i class="bi bi-arrow-left"></i>
            </a>
            <div>
                <h2 class="text-xl font-extrabold tracking-tight text-slate-900">{{ $location->name }}</h2>
                <p class="text-xs text-slate-500 mt-0.5">Location Code: <span class="font-mono font-bold">{{ $location->location_code }}</span></p>
            </div>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('partner.locations.edit', $location->id) }}" class="px-4 py-2 bg-slate-100 hover:bg-slate-750 text-xs font-bold text-slate-600 border border-slate-200 rounded-xl transition-all">
                <i class="bi bi-pencil me-1.5"></i> Edit Location
            </a>
        </div>
    </div>

    <!-- Status Alerts / Rejection details -->
    @if($location->status === 'rejected')
        <div class="bg-rose-500/10 border border-rose-500/20 rounded-2xl p-5 space-y-2">
            <div class="flex items-center gap-2 text-rose-450 text-xs font-bold">
                <i class="bi bi-x-circle-fill text-lg"></i>
                <span>Verification Failed (Rejected)</span>
            </div>
            <p class="text-xs text-slate-600 pl-7 leading-relaxed">
                <strong>Reason:</strong> {{ $location->rejection_reason ?? 'No detailed feedback provided by admin.' }}
            </p>
            <div class="text-[10px] text-slate-500 pl-7 flex items-center gap-4">
                <span><strong>Rejected By:</strong> {{ $location->rejectedByUser ? $location->rejectedByUser->name : 'Admin' }}</span>
                <span><strong>Date:</strong> {{ $location->rejected_at ? $location->rejected_at->format('d-M-Y H:i') : '' }}</span>
            </div>
        </div>
    @elseif($location->status === 'pending')
        <div class="bg-amber-500/10 border border-amber-500/20 rounded-2xl p-5 flex items-center gap-3 text-amber-400 text-xs font-semibold">
            <i class="bi bi-clock-fill text-lg"></i>
            <div>
                <span>This location request is currently pending verification.</span>
                <p class="text-[10px] text-slate-500 font-normal mt-0.5">Our administration panel will verify the coordinates and footfall claims shortly.</p>
            </div>
        </div>
    @endif

    <!-- Two Column detail grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Left Column: Specs -->
        <div class="space-y-6 lg:col-span-2">
            <div class="bg-white border border-slate-200 rounded-[32px] p-6 sm:p-8 space-y-6">
                <h3 class="text-sm font-bold text-slate-900 border-b border-slate-200 pb-4 flex items-center gap-2">
                    <i class="bi bi-info-circle-fill text-blue-450"></i> Technical Specifications
                </h3>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 text-xs">
                    <div class="space-y-1">
                        <span class="block text-[10px] text-slate-500 font-bold uppercase tracking-wider">Category</span>
                        <span class="block text-slate-700 font-semibold">{{ $location->category ? $location->category->name : 'Uncategorized' }}</span>
                    </div>
                    <div class="space-y-1">
                        <span class="block text-[10px] text-slate-500 font-bold uppercase tracking-wider">Average Daily Footfall</span>
                        <span class="block text-slate-700 font-semibold">{{ number_format($location->daily_footfall) }} people / day</span>
                    </div>
                    <div class="space-y-1 col-span-2">
                        <span class="block text-[10px] text-slate-500 font-bold uppercase tracking-wider">Address</span>
                        <span class="block text-slate-700 leading-relaxed">{{ $location->address }}, {{ $location->city }}, {{ $location->state }} - {{ $location->postal_code }}</span>
                    </div>
                    <div class="space-y-1">
                        <span class="block text-[10px] text-slate-500 font-bold uppercase tracking-wider">Coordinates (Latitude, Longitude)</span>
                        <span class="block text-slate-700 font-mono">{{ $location->latitude }}, {{ $location->longitude }}</span>
                    </div>
                    <div class="space-y-1">
                        <span class="block text-[10px] text-slate-500 font-bold uppercase tracking-wider">Operating Hours</span>
                        <span class="block text-slate-700">{{ $location->operating_hours ?: 'Not Specified' }}</span>
                    </div>
                    @if($location->description)
                        <div class="space-y-1 col-span-2">
                            <span class="block text-[10px] text-slate-500 font-bold uppercase tracking-wider">Description</span>
                            <span class="block text-slate-600 leading-relaxed">{{ $location->description }}</span>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Screen Count (informational only - screens are managed by admin) -->
            <div class="bg-white border border-slate-200 rounded-[32px] p-6 sm:p-8 space-y-4">
                <h3 class="text-sm font-bold text-slate-900 border-b border-slate-200 pb-4 flex items-center gap-2">
                    <i class="bi bi-display-fill text-indigo-400"></i> Advertising Screens
                </h3>
                <div class="flex items-center gap-4">
                    <div class="w-14 h-14 rounded-2xl bg-indigo-50 border border-indigo-100 flex items-center justify-center">
                        <span class="text-2xl font-extrabold text-indigo-600">{{ $location->screens->count() }}</span>
                    </div>
                    <div>
                        <p class="text-sm font-bold text-slate-800">{{ $location->screens->count() }} {{ Str::plural('Screen', $location->screens->count()) }} Available</p>
                        <p class="text-xs text-slate-500 mt-0.5">Screen setup and configuration is managed by Hyper Adz admin.</p>
                    </div>
                </div>
            </div>

        <!-- Campaigns at this Location -->
        <div class="bg-white border border-slate-200 rounded-[32px] p-6 sm:p-8 space-y-6">
            <h3 class="text-sm font-bold text-slate-900 border-b border-slate-200 pb-4 flex items-center gap-2">
                <i class="bi bi-play-circle-fill text-indigo-400"></i> Campaigns Running Here
            </h3>

            @if($campaigns->isEmpty())
                <div class="py-10 text-center space-y-2">
                    <div class="w-12 h-12 rounded-full bg-slate-100 text-slate-500 border border-slate-200 flex items-center justify-center text-lg mx-auto">
                        <i class="bi bi-calendar2-x"></i>
                    </div>
                    <p class="text-xs text-slate-500">No campaigns found for this location.</p>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse text-xs">
                        <thead>
                            <tr class="border-b border-slate-200 text-[10px] font-bold text-slate-500 uppercase tracking-wider">
                                <th class="py-3">Campaign</th>
                                <th class="py-3">Advertiser</th>
                                <th class="py-3">Date Range</th>
                                <th class="py-3">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-850/40">
                            @foreach($campaigns as $camp)
                                <tr class="hover:bg-white/5 transition-colors">
                                    <td class="py-3">
                                        <div class="font-bold text-slate-900">{{ $camp->campaign_name }}</div>
                                        <div class="font-mono text-slate-500 text-[10px]">{{ $camp->campaign_code }}</div>
                                    </td>
                                    <td class="py-3 text-slate-600 font-semibold">{{ $camp->advertiser->company_name ?? 'Unknown' }}</td>
                                    <td class="py-3 text-slate-600">{{ $camp->start_date->format('M d') }} - {{ $camp->end_date->format('M d, Y') }}</td>
                                    <td class="py-3">
                                        <span class="inline-flex items-center gap-1 text-[10px] font-bold px-2 py-0.5 rounded-full border {{ $camp->status === 'Running' ? 'bg-indigo-50 text-indigo-700 border-indigo-200' : 'bg-slate-100 text-slate-700 border-slate-200' }}">
                                            {{ $camp->status }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @if($campaigns->hasPages())
                    <div class="pt-4 border-t border-slate-100 dark:border-slate-750">
                        {{ $campaigns->links() }}
                    </div>
                @endif
            @endif
        </div>
    </div>

        <!-- Right Column: Media Gallery & Approval Log -->
        <div class="space-y-6">
            <!-- Media Gallery Card -->
            <div class="bg-white border border-slate-200 rounded-[32px] p-6 space-y-5">
                <h3 class="text-sm font-bold text-slate-900 border-b border-slate-200 pb-4 flex items-center gap-2">
                    <i class="bi bi-images text-blue-450"></i> Venue Media Gallery
                </h3>

                @if($location->images->isEmpty())
                    <div class="py-8 text-center bg-slate-100 border border-slate-200 rounded-2xl">
                        <i class="bi bi-image text-slate-600 text-2xl"></i>
                        <p class="text-xxs text-slate-500 mt-1">No images uploaded.</p>
                    </div>
                @else
                    <div class="grid grid-cols-2 gap-3">
                        @foreach($location->images as $img)
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

            <!-- Approval Log / Lifecycle details -->
            <div class="bg-white border border-slate-200 rounded-[32px] p-6 space-y-5">
                <h3 class="text-sm font-bold text-slate-900 border-b border-slate-200 pb-4 flex items-center gap-2">
                    <i class="bi bi-calendar-check text-blue-450"></i> Request Lifecycle
                </h3>
                <div class="space-y-4 text-xs">
                    <div class="flex justify-between">
                        <span class="text-slate-500">Submitted On:</span>
                        <span class="text-slate-700 font-semibold">{{ $location->created_at ? $location->created_at->format('d-M-Y H:i') : 'N/A' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-500">Current Status:</span>
                        <x-status-badge :status="$location->status" />
                    </div>
                    @if($location->status === 'active' || $location->status === 'approved')
                        <div class="border-t border-slate-200 pt-4 space-y-4">
                            <div class="flex justify-between">
                                <span class="text-slate-500">Approved On:</span>
                                <span class="text-slate-700 font-semibold">Ready</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-slate-500">Verification Code:</span>
                                <span class="text-emerald-400 font-mono font-bold">{{ $location->location_code }}</span>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
