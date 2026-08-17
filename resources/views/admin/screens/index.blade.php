@extends('admin.layouts.app', [
    'title' => 'Screens Inventory | Hyper Adz Admin'
])

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white flex items-center gap-2.5">
                <i class="bi bi-display-fill text-[#1155CC]"></i> Screens Inventory
            </h1>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Configure and manage digital screens, dimensions, resolutions, orientations, and media specs.</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.screens.dashboard') }}" class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 dark:bg-slate-800 dark:hover:bg-slate-700 dark:text-slate-350 rounded-xl text-sm font-semibold transition-all flex items-center gap-2 border border-slate-200/50 dark:border-slate-700">
                <i class="bi bi-pie-chart-fill"></i> Stats Dashboard
            </a>
            <a href="{{ route('admin.screens.create') }}" class="px-4 py-2.5 bg-[#1155CC] hover:bg-blue-700 text-white rounded-xl text-sm font-semibold transition-all flex items-center gap-2 shadow-lg shadow-blue-500/10">
                <i class="bi bi-plus-lg"></i> Add Screen
            </a>
        </div>
    </div>

    <!-- Feedback Alerts -->
    @if(session('success'))
        <div class="p-4 bg-emerald-50 border border-emerald-100 dark:bg-emerald-950/10 dark:border-emerald-900/30 text-emerald-600 dark:text-emerald-450 rounded-2xl flex items-center gap-3 text-sm">
            <i class="bi bi-check-circle-fill text-lg shrink-0"></i>
            <div>{{ session('success') }}</div>
        </div>
    @endif

    <!-- Filters Panel -->
    <div class="bg-white dark:bg-slate-800 rounded-3xl p-5 border border-slate-100 dark:border-slate-700/50 shadow-sm">
        <form method="GET" action="{{ route('admin.screens.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
            <!-- Search -->
            <div class="space-y-1.5 col-span-1 lg:col-span-2">
                <label class="text-[11px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider">Search</label>
                <div class="relative">
                    <i class="bi bi-search absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400"></i>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search name, code, identifier, location..." class="w-full pl-10 pr-4 py-2.5 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-850 rounded-xl text-sm text-slate-700 dark:text-slate-200 placeholder-slate-400 focus:outline-none focus:border-[#1155CC] transition-all">
                </div>
            </div>

            <!-- Location -->
            <div class="space-y-1.5">
                <label class="text-[11px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider">Location</label>
                <select name="location_id" class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-850 rounded-xl text-sm text-slate-700 dark:text-slate-200 focus:outline-none focus:border-[#1155CC] transition-all">
                    <option value="">All Locations</option>
                    @foreach($locations as $loc)
                        <option value="{{ $loc->id }}" {{ request('location_id') == $loc->id ? 'selected' : '' }}>{{ $loc->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Screen Type -->
            <div class="space-y-1.5">
                <label class="text-[11px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider">Screen Type</label>
                <select name="screen_type_id" class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-850 rounded-xl text-sm text-slate-700 dark:text-slate-200 focus:outline-none focus:border-[#1155CC] transition-all">
                    <option value="">All Types</option>
                    @foreach($screenTypes as $type)
                        <option value="{{ $type->id }}" {{ request('screen_type_id') == $type->id ? 'selected' : '' }}>{{ $type->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Action buttons -->
            <div class="flex items-end gap-2">
                <button type="submit" class="flex-1 py-2.5 bg-[#1155CC] hover:bg-blue-700 text-white rounded-xl text-sm font-semibold transition-all flex items-center justify-center gap-1.5 shadow-md shadow-blue-500/10">
                    <i class="bi bi-funnel-fill"></i> Filter
                </button>
                @if(request()->anyFilled(['search', 'location_id', 'screen_type_id', 'status', 'orientation']))
                    <a href="{{ route('admin.screens.index') }}" class="py-2.5 px-3 bg-slate-100 hover:bg-slate-200 text-slate-700 dark:bg-slate-800 dark:hover:bg-slate-700 dark:text-slate-350 rounded-xl text-sm font-semibold transition-all flex items-center justify-center border border-slate-200/50 dark:border-slate-700" title="Reset Filters">
                        <i class="bi bi-arrow-counterclockwise"></i>
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Inventory Datatable -->
    <div class="bg-white dark:bg-slate-800 rounded-3xl border border-slate-100 dark:border-slate-700/50 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full border-collapse text-left">
                <thead>
                    <tr class="border-b border-slate-100 dark:border-slate-750 bg-slate-50/50 dark:bg-slate-850/20">
                        <th class="px-6 py-4 text-xs font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider">Image</th>
                        <th class="px-6 py-4 text-xs font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider">Screen Details</th>
                        <th class="px-6 py-4 text-xs font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider">Location & Type</th>
                        <th class="px-6 py-4 text-xs font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider">Specs & Media</th>
                        <th class="px-6 py-4 text-xs font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider">Impressions</th>
                        <th class="px-6 py-4 text-xs font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-4 text-xs font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-750">
                    @forelse($screens as $scr)
                        <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-850/5 transition-all">
                            <!-- Thumbnail -->
                            <td class="px-6 py-4 shrink-0">
                                <div class="w-14 h-10 rounded-lg overflow-hidden bg-slate-100 dark:bg-slate-900 border border-slate-200/50 dark:border-slate-800">
                                    <img src="{{ $scr->primary_image_url }}" alt="{{ $scr->name }}" class="w-full h-full object-cover">
                                </div>
                            </td>

                            <!-- Code & Name -->
                            <td class="px-6 py-4">
                                <div>
                                    <div class="flex items-center gap-2">
                                        <span class="font-bold text-slate-800 dark:text-slate-250 text-sm tracking-tight">{{ $scr->screen_code }}</span>
                                        @if($scr->screen_identifier)
                                            <span class="text-[10px] bg-slate-100 text-slate-600 dark:bg-slate-800 dark:text-slate-400 px-2 py-0.5 rounded font-mono font-bold">{{ $scr->screen_identifier }}</span>
                                        @endif
                                    </div>
                                    <span class="block text-xs text-slate-500 dark:text-slate-400 font-medium mt-0.5">{{ $scr->name }}</span>
                                </div>
                            </td>

                            <!-- Location & Type -->
                            <td class="px-6 py-4">
                                <span class="block text-sm text-slate-700 dark:text-slate-300 font-semibold">{{ $scr->location->name ?? 'N/A' }}</span>
                                <span class="block text-[11px] text-slate-400 dark:text-slate-500 font-semibold mt-0.5 uppercase tracking-wide">{{ $scr->type->name ?? 'Custom' }}</span>
                            </td>

                            <!-- Specs & Format -->
                            <td class="px-6 py-4 text-xs space-y-0.5 text-slate-600 dark:text-slate-400">
                                <div class="flex items-center gap-1.5">
                                    <i class="bi bi-phone-landscape text-slate-400"></i>
                                    <span>{{ $scr->orientation }} ({{ $scr->resolution ?? 'N/A' }})</span>
                                </div>
                                <div class="flex items-center gap-1.5 font-mono text-[10px]">
                                    <i class="bi bi-file-earmark-arrow-up"></i>
                                    <span>{{ $scr->supported_formats }} ({{ $scr->max_video_duration ? $scr->max_video_duration.'s' : 'Static Only' }})</span>
                                </div>
                            </td>

                            <!-- Daily Impressions -->
                            <td class="px-6 py-4">
                                <span class="font-bold text-slate-800 dark:text-slate-300 text-sm">{{ number_format($scr->daily_impressions) }}</span>
                                <span class="block text-[10px] text-slate-400 dark:text-slate-500 font-medium -mt-0.5">daily views</span>
                            </td>

                            <!-- Status & Availability -->
                            <td class="px-6 py-4 space-y-1">
                                <!-- Status Badge -->
                                <div>
                                    @if($scr->status === 'active')
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 bg-emerald-50 text-emerald-700 dark:bg-emerald-950/10 dark:text-emerald-450 text-xs rounded-full font-bold">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Active
                                        </span>
                                    @elseif($scr->status === 'maintenance')
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 bg-amber-50 text-amber-700 dark:bg-amber-950/10 dark:text-amber-450 text-xs rounded-full font-bold">
                                            <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span> Maintenance
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-400 text-xs rounded-full font-bold">
                                            <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span> Inactive
                                        </span>
                                    @endif
                                </div>

                                <!-- Availability Badge -->
                                <div>
                                    @if($scr->availability_status === 'available')
                                        <span class="inline-flex items-center px-2 py-0.5 text-[10px] font-bold rounded-md bg-blue-50 text-[#1155CC] dark:bg-blue-950/20 dark:text-blue-400">
                                            Available
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-0.5 text-[10px] font-bold rounded-md bg-red-50 text-red-600 dark:bg-red-950/20 dark:text-red-400">
                                            Occupied
                                        </span>
                                    @endif
                                </div>
                            </td>

                            <!-- Actions -->
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('locations.details', $scr->location->slug) }}" target="_blank" class="p-1.5 hover:bg-slate-100 dark:hover:bg-slate-750 text-slate-500 dark:text-slate-400 hover:text-slate-700 rounded-lg transition-all" title="View Detail Page">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.screens.edit', $scr->id) }}" class="p-1.5 hover:bg-slate-100 dark:hover:bg-slate-750 text-slate-500 dark:text-slate-400 hover:text-[#1155CC] rounded-lg transition-all" title="Edit Screen">
                                        <i class="bi bi-pencil-square"></i>
                                    </a>
                                    <form action="{{ route('admin.screens.destroy', $scr->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this screen? This can be recovered from soft deletes.');" class="inline-block">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-1.5 hover:bg-red-50 dark:hover:bg-red-950/15 text-slate-500 dark:text-slate-400 hover:text-red-650 dark:hover:text-red-400 rounded-lg transition-all" title="Delete Screen">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center">
                                <div class="max-w-xs mx-auto space-y-2.5">
                                    <div class="w-12 h-12 rounded-2xl bg-slate-50 dark:bg-slate-900 border border-slate-150 dark:border-slate-800 flex items-center justify-center mx-auto text-slate-400">
                                        <i class="bi bi-display text-xl"></i>
                                    </div>
                                    <p class="text-sm font-bold text-slate-700 dark:text-slate-300">No screens found</p>
                                    <p class="text-xs text-slate-450">Try adjusting your filters or click Add Screen to expand your inventory.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($screens->hasPages())
            <div class="px-6 py-4 border-t border-slate-100 dark:border-slate-750 bg-slate-50/30 dark:bg-slate-850/10">
                {{ $screens->appends(request()->query())->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
