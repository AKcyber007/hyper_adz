@extends('admin.layouts.app', [
    'title' => 'Manage Locations | Hyper Adz Admin'
])

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white flex items-center gap-2.5">
                <i class="bi bi-geo-alt-fill text-[#1155CC]"></i> Locations Inventory
            </h1>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Manage and track advertising network screens, venues, coordinates, and media assets.</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.locations.map') }}" class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 dark:bg-slate-800 dark:hover:bg-slate-700 dark:text-slate-350 rounded-xl text-sm font-semibold transition-all flex items-center gap-2 border border-slate-200/50 dark:border-slate-700">
                <i class="bi bi-map-fill"></i> Map View
            </a>
            <a href="{{ route('admin.locations.create') }}" class="px-4 py-2.5 bg-[#1155CC] hover:bg-blue-700 text-white rounded-xl text-sm font-semibold transition-all flex items-center gap-2 shadow-lg shadow-blue-500/10">
                <i class="bi bi-plus-lg"></i> Add Location
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
        <form method="GET" action="{{ route('admin.locations.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
            <!-- Search -->
            <div class="space-y-1.5 col-span-1 lg:col-span-2">
                <label class="text-[11px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider">Search</label>
                <div class="relative">
                    <i class="bi bi-search absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400"></i>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search name, code, city..." class="w-full pl-10 pr-4 py-2.5 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-850 rounded-xl text-sm text-slate-700 dark:text-slate-200 placeholder-slate-400 focus:outline-none focus:border-[#1155CC] transition-all">
                </div>
            </div>

            <!-- Category -->
            <div class="space-y-1.5">
                <label class="text-[11px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider">Category</label>
                <select name="category_id" class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-850 rounded-xl text-sm text-slate-700 dark:text-slate-200 focus:outline-none focus:border-[#1155CC] transition-all">
                    <option value="">All Categories</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Status -->
            <div class="space-y-1.5">
                <label class="text-[11px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider">Status</label>
                <select name="status" class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-850 rounded-xl text-sm text-slate-700 dark:text-slate-200 focus:outline-none focus:border-[#1155CC] transition-all">
                    <option value="">All Statuses</option>
                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                    <option value="maintenance" {{ request('status') === 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                </select>
            </div>

            <!-- Action buttons -->
            <div class="flex items-end gap-2">
                <button type="submit" class="flex-1 py-2.5 bg-[#1155CC] hover:bg-blue-700 text-white rounded-xl text-sm font-semibold transition-all flex items-center justify-center gap-1.5 shadow-md shadow-blue-500/10">
                    <i class="bi bi-funnel-fill"></i> Filter
                </button>
                @if(request()->anyFilled(['search', 'category_id', 'status', 'city']))
                    <a href="{{ route('admin.locations.index') }}" class="py-2.5 px-3 bg-slate-100 hover:bg-slate-200 text-slate-700 dark:bg-slate-800 dark:hover:bg-slate-700 dark:text-slate-350 rounded-xl text-sm font-semibold transition-all flex items-center justify-center border border-slate-200/50 dark:border-slate-700" title="Reset Filters">
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
                        <th class="px-6 py-4 text-xs font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider">Code & Name</th>
                        <th class="px-6 py-4 text-xs font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider">Category</th>
                        <th class="px-6 py-4 text-xs font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider">City</th>
                        <th class="px-6 py-4 text-xs font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider">Partner</th>
                        <th class="px-6 py-4 text-xs font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider">₹/Day</th>
                        <th class="px-6 py-4 text-xs font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider">Screens</th>
                        <th class="px-6 py-4 text-xs font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-4 text-xs font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider">Date Created</th>
                        <th class="px-6 py-4 text-xs font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-750">
                    @forelse($locations as $loc)
                        <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-850/5 transition-all">
                            <!-- Image Thumbnail -->
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="w-14 h-10 rounded-lg overflow-hidden border border-slate-200/60 dark:border-slate-700 bg-slate-50 dark:bg-slate-900 flex items-center justify-center shadow-sm">
                                    @if($loc->primary_image)
                                        <img src="{{ $loc->primary_image->url }}" class="w-full h-full object-cover">
                                    @else
                                        <i class="bi bi-image text-slate-350 dark:text-slate-600 text-lg"></i>
                                    @endif
                                </div>
                            </td>
                            <!-- Code & Name -->
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="font-bold text-slate-800 dark:text-slate-200 text-sm hover:text-[#1155CC] transition-all">
                                    {{ $loc->name }}
                                </div>
                                <div class="flex items-center gap-1.5 mt-0.5">
                                    <span class="text-[10px] font-extrabold font-mono bg-slate-100 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 text-slate-500 dark:text-slate-400 px-1.5 py-0.5 rounded">
                                        {{ $loc->location_code }}
                                    </span>
                                    <span class="text-[10px] text-slate-400 font-mono">
                                        {{ $loc->slug }}
                                    </span>
                                </div>
                            </td>
                            <!-- Category Relation -->
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center gap-2 text-slate-650 dark:text-slate-350 text-sm">
                                    <i class="bi {{ $loc->category->icon ?? 'bi-tag-fill' }} text-slate-400 text-base"></i>
                                    <span>{{ $loc->category->name ?? 'Uncategorized' }}</span>
                                </div>
                            </td>
                            <!-- City -->
                            <td class="px-6 py-4 whitespace-nowrap text-slate-600 dark:text-slate-400 text-sm">
                                {{ $loc->city }}
                            </td>
                            <!-- Partner -->
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($loc->locationPartner)
                                    <div class="text-xs font-semibold text-slate-700 dark:text-slate-300">{{ $loc->locationPartner->company_name }}</div>
                                    <div class="text-[10px] text-slate-400 font-mono">{{ $loc->locationPartner->partner_code }}</div>
                                @else
                                    <span class="text-[10px] text-rose-500 font-semibold">⚠ Unassigned</span>
                                @endif
                            </td>
                            <!-- Price/Day -->
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="text-sm font-bold text-emerald-600 dark:text-emerald-450">
                                    ₹{{ number_format((float) $loc->price_per_day, 0) }}
                                </span>
                                <span class="text-[9px] text-slate-400 font-normal">/day</span>
                            </td>
                            <!-- Screens -->
                            <td class="px-6 py-4 whitespace-nowrap text-slate-800 dark:text-slate-200 font-bold text-sm">
                                <span class="px-2 py-1 bg-slate-100 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg text-xs">{{ $loc->screens()->count() }}</span>
                            </td>
                            <!-- Status -->
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($loc->status === 'active')
                                    <span class="inline-flex items-center gap-1 text-[10px] font-bold bg-emerald-50 dark:bg-emerald-950/20 text-emerald-600 dark:text-emerald-450 border border-emerald-100 dark:border-emerald-900/30 px-2 py-0.5 rounded-full">
                                        <span class="w-1.5 h-1.5 bg-emerald-500 rounded-full animate-pulse"></span> Active
                                    </span>
                                @elseif($loc->status === 'maintenance')
                                    <span class="inline-flex items-center gap-1 text-[10px] font-bold bg-amber-50 dark:bg-amber-950/20 text-amber-600 dark:text-amber-450 border border-amber-100 dark:border-amber-900/30 px-2 py-0.5 rounded-full">
                                        <span class="w-1.5 h-1.5 bg-amber-500 rounded-full animate-pulse"></span> Maintenance
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 text-[10px] font-bold bg-slate-100 dark:bg-slate-900 text-slate-500 dark:text-slate-400 border border-slate-200 dark:border-slate-800 px-2 py-0.5 rounded-full">
                                        <span class="w-1.5 h-1.5 bg-slate-400 rounded-full"></span> Inactive
                                    </span>
                                @endif
                            </td>
                            <!-- Date Created -->
                            <td class="px-6 py-4 whitespace-nowrap text-slate-500 text-xs">
                                {{ $loc->created_at->format('M d, Y') }}
                            </td>
                            <!-- Action Buttons -->
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex items-center justify-end gap-1.5">
                                    <a href="{{ route('admin.locations.show', $loc->id) }}" class="w-8 h-8 rounded-lg bg-slate-50 dark:bg-slate-900 hover:bg-slate-100 dark:hover:bg-slate-800 text-slate-500 hover:text-slate-700 dark:hover:text-slate-200 flex items-center justify-center transition-all border border-slate-200/50 dark:border-slate-800" title="View Campaigns">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.locations.edit', $loc->id) }}" class="w-8 h-8 rounded-lg bg-blue-50/50 hover:bg-blue-100/50 text-[#1155CC] flex items-center justify-center transition-all border border-blue-100/20" title="Edit Location">
                                        <i class="bi bi-pencil-square"></i>
                                    </a>
                                    <form action="{{ route('admin.locations.destroy', $loc->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this location? (Soft delete will be used.)');" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="w-8 h-8 rounded-lg bg-rose-50/50 hover:bg-rose-100/50 text-rose-600 flex items-center justify-center transition-all border border-rose-100/20" title="Delete Location">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="px-6 py-12 text-center text-slate-400 dark:text-slate-500">
                                <div class="flex flex-col items-center justify-center gap-3">
                                    <i class="bi bi-geo-alt text-4xl text-slate-300 dark:text-slate-700 animate-bounce"></i>
                                    <span class="text-sm">No locations found matching your filter criteria.</span>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Pagination Links -->
        @if($locations->hasPages())
            <div class="px-6 py-4 border-t border-slate-100 dark:border-slate-750 bg-slate-50/30 dark:bg-slate-850/5">
                {{ $locations->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
