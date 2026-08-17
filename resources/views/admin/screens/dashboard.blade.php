@extends('admin.layouts.app', [
    'title' => 'Inventory Dashboard | Hyper Adz Admin'
])

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center gap-3">
        <a href="{{ route('admin.screens.index') }}" class="w-10 h-10 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 dark:bg-slate-800 dark:hover:bg-slate-700 dark:text-slate-300 flex items-center justify-center transition-all">
            <i class="bi bi-arrow-left text-lg"></i>
        </a>
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white">Inventory Dashboard</h1>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Real-time statistics of screens performance, media assets, availability, and impressions analytics.</p>
        </div>
    </div>

    <!-- KPI Metric Cards Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-5">
        <!-- Total Screens -->
        <div class="bg-white dark:bg-slate-800 rounded-3xl p-5 border border-slate-100 dark:border-slate-700/50 shadow-sm flex items-center gap-4 relative overflow-hidden group hover:scale-[1.02] transition-all duration-300">
            <div class="w-12 h-12 rounded-2xl bg-blue-50 dark:bg-blue-950/20 text-[#1155CC] dark:text-blue-400 flex items-center justify-center text-xl shrink-0">
                <i class="bi bi-display-fill"></i>
            </div>
            <div>
                <span class="block text-xs font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider">Total Screens</span>
                <span class="block text-2xl font-bold text-slate-850 dark:text-white mt-0.5">{{ number_format($metrics['total_screens']) }}</span>
            </div>
        </div>

        <!-- Active Screens -->
        <div class="bg-white dark:bg-slate-800 rounded-3xl p-5 border border-slate-100 dark:border-slate-700/50 shadow-sm flex items-center gap-4 relative overflow-hidden group hover:scale-[1.02] transition-all duration-300">
            <div class="w-12 h-12 rounded-2xl bg-emerald-50 dark:bg-emerald-950/20 text-emerald-600 dark:text-emerald-400 flex items-center justify-center text-xl shrink-0">
                <i class="bi bi-check-circle-fill"></i>
            </div>
            <div>
                <span class="block text-xs font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider">Active Screens</span>
                <span class="block text-2xl font-bold text-slate-850 dark:text-white mt-0.5">{{ number_format($metrics['active_screens']) }}</span>
            </div>
        </div>

        <!-- Maintenance Screens -->
        <div class="bg-white dark:bg-slate-800 rounded-3xl p-5 border border-slate-100 dark:border-slate-700/50 shadow-sm flex items-center gap-4 relative overflow-hidden group hover:scale-[1.02] transition-all duration-300">
            <div class="w-12 h-12 rounded-2xl bg-amber-50 dark:bg-amber-950/20 text-amber-600 dark:text-amber-450 flex items-center justify-center text-xl shrink-0">
                <i class="bi bi-tools"></i>
            </div>
            <div>
                <span class="block text-xs font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider">Maintenance</span>
                <span class="block text-2xl font-bold text-slate-850 dark:text-white mt-0.5">{{ number_format($metrics['maintenance_screens']) }}</span>
            </div>
        </div>

        <!-- Inactive Screens -->
        <div class="bg-white dark:bg-slate-800 rounded-3xl p-5 border border-slate-100 dark:border-slate-700/50 shadow-sm flex items-center gap-4 relative overflow-hidden group hover:scale-[1.02] transition-all duration-300">
            <div class="w-12 h-12 rounded-2xl bg-slate-50 dark:bg-slate-905/20 text-slate-600 dark:text-slate-400 flex items-center justify-center text-xl shrink-0">
                <i class="bi bi-x-circle-fill"></i>
            </div>
            <div>
                <span class="block text-xs font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider">Inactive</span>
                <span class="block text-2xl font-bold text-slate-850 dark:text-white mt-0.5">{{ number_format($metrics['inactive_screens']) }}</span>
            </div>
        </div>

        <!-- Total Daily Impressions -->
        <div class="bg-white dark:bg-slate-800 rounded-3xl p-5 border border-slate-100 dark:border-slate-700/50 shadow-sm flex items-center gap-4 relative overflow-hidden group hover:scale-[1.02] transition-all duration-300 sm:col-span-2 lg:col-span-1">
            <div class="w-12 h-12 rounded-2xl bg-purple-50 dark:bg-purple-950/20 text-purple-600 dark:text-purple-400 flex items-center justify-center text-xl shrink-0">
                <i class="bi bi-eye-fill"></i>
            </div>
            <div>
                <span class="block text-xs font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider">Impressions / Day</span>
                <span class="block text-2xl font-bold text-slate-850 dark:text-white mt-0.5">{{ number_format($metrics['total_daily_impressions']) }}</span>
            </div>
        </div>
    </div>

    <!-- Charts / Breakdown Section -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Screens By Type Chart Card -->
        <div class="bg-white dark:bg-slate-800 rounded-3xl p-6 border border-slate-100 dark:border-slate-700/50 shadow-sm">
            <h3 class="text-sm font-bold text-slate-800 dark:text-slate-200 flex items-center gap-2 mb-6">
                <i class="bi bi-tag-fill text-[#1155CC]"></i> Screens By Screen Type
            </h3>
            <div class="space-y-4">
                @php
                    $maxTypeCount = !empty($metrics['screens_by_type']) ? max($metrics['screens_by_type']) : 1;
                @endphp
                @forelse($metrics['screens_by_type'] as $type => $count)
                    @php
                        $percentage = ($count / $maxTypeCount) * 100;
                    @endphp
                    <div class="space-y-1.5">
                        <div class="flex items-center justify-between text-xs font-semibold text-slate-700 dark:text-slate-350">
                            <span>{{ $type }}</span>
                            <span class="font-bold">{{ $count }} ({{ number_format(($count / max($metrics['total_screens'], 1)) * 100, 1) }}%)</span>
                        </div>
                        <div class="w-full h-3 bg-slate-50 dark:bg-slate-900 rounded-full overflow-hidden">
                            <div class="h-full bg-gradient-to-r from-[#1155CC] to-blue-500 rounded-full transition-all duration-500" style="width: {{ $percentage }}%"></div>
                        </div>
                    </div>
                @empty
                    <p class="text-xs text-slate-400 italic text-center py-8">No screen type statistics available.</p>
                @endforelse
            </div>
        </div>

        <!-- Screens By Location Chart Card -->
        <div class="bg-white dark:bg-slate-800 rounded-3xl p-6 border border-slate-100 dark:border-slate-700/50 shadow-sm">
            <h3 class="text-sm font-bold text-slate-800 dark:text-slate-200 flex items-center gap-2 mb-6">
                <i class="bi bi-geo-alt-fill text-emerald-600"></i> Screens By Venue Location
            </h3>
            <div class="space-y-4">
                @php
                    $maxLocCount = !empty($metrics['screens_by_location']) ? max($metrics['screens_by_location']) : 1;
                @endphp
                @forelse($metrics['screens_by_location'] as $locName => $count)
                    @php
                        $percentage = ($count / $maxLocCount) * 100;
                    @endphp
                    <div class="space-y-1.5">
                        <div class="flex items-center justify-between text-xs font-semibold text-slate-700 dark:text-slate-350">
                            <span>{{ $locName }}</span>
                            <span class="font-bold">{{ $count }} screen(s)</span>
                        </div>
                        <div class="w-full h-3 bg-slate-50 dark:bg-slate-900 rounded-full overflow-hidden">
                            <div class="h-full bg-gradient-to-r from-emerald-500 to-teal-400 rounded-full transition-all duration-500" style="width: {{ $percentage }}%"></div>
                        </div>
                    </div>
                @empty
                    <p class="text-xs text-slate-400 italic text-center py-8">No location statistics available.</p>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
