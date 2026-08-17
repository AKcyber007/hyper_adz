@extends('admin.layouts.app', [
    'title' => 'Location Categories | Hyper Adz Admin'
])

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white flex items-center gap-2.5">
                <i class="bi bi-tags-fill text-[#1155CC]"></i> Venue Categories
            </h1>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Manage and monitor venue category segmentations and distribution counts across the ad network.</p>
        </div>
        <div>
            <a href="{{ route('admin.locations.index') }}" class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 dark:bg-slate-800 dark:hover:bg-slate-700 dark:text-slate-350 rounded-xl text-sm font-semibold transition-all flex items-center gap-2 border border-slate-200/50 dark:border-slate-700">
                <i class="bi bi-list-task"></i> View Locations Inventory
            </a>
        </div>
    </div>

    <!-- Category Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        @foreach($categories as $cat)
            <div class="bg-white dark:bg-slate-800 rounded-3xl p-6 border border-slate-100 dark:border-slate-700/50 shadow-sm hover:shadow-md transition-all flex flex-col justify-between group">
                <div class="flex items-start justify-between">
                    <div class="w-12 h-12 rounded-2xl bg-blue-50 dark:bg-blue-900/20 text-[#1155CC] flex items-center justify-center border border-blue-100/20 group-hover:scale-110 transition-all">
                        <i class="bi {{ $cat->icon ?? 'bi-tag-fill' }} text-xl"></i>
                    </div>
                    @if($cat->status === 'active')
                        <span class="text-[9px] font-extrabold bg-emerald-50 dark:bg-emerald-950/20 text-emerald-600 dark:text-emerald-450 border border-emerald-100 dark:border-emerald-900/30 px-2 py-0.5 rounded-full uppercase tracking-wider">
                            Active
                        </span>
                    @else
                        <span class="text-[9px] font-extrabold bg-slate-100 dark:bg-slate-900 text-slate-500 dark:text-slate-400 border border-slate-250 dark:border-slate-800 px-2 py-0.5 rounded-full uppercase tracking-wider">
                            Inactive
                        </span>
                    @endif
                </div>
                
                <div class="mt-6">
                    <h3 class="text-lg font-bold text-slate-800 dark:text-slate-100">{{ $cat->name }}</h3>
                    <div class="mt-2 flex items-center justify-between text-xs text-slate-400">
                        <span>Total Registered Venues:</span>
                        <span class="font-bold text-slate-700 dark:text-slate-200 text-sm bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-850 px-2.5 py-0.5 rounded-lg">
                            {{ $cat->locations_count }}
                        </span>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>
@endsection
