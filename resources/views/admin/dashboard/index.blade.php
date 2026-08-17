@extends('admin.layouts.app')

@section('page_title')
    <i class="bi bi-grid-1x2-fill text-[#1155CC]"></i> Admin Dashboard
@endsection

@section('content')
<div class="space-y-8">
    <!-- Welcome Header banner -->
    <div class="bg-gradient-to-r from-[#0A1628] to-[#1155CC] text-white rounded-3xl p-6 md:p-8 shadow-xl flex flex-col md:flex-row md:items-center justify-between gap-6 relative overflow-hidden">
        <!-- Decorative subtle background circle -->
        <div class="absolute right-0 bottom-0 w-80 h-80 bg-white/5 rounded-full blur-2xl translate-x-20 translate-y-20"></div>
        <div class="space-y-2 relative z-10">
            <h1 class="text-2xl md:text-3xl font-extrabold tracking-tight">Welcome back, {{ Auth::user()->name }}!</h1>
            <p class="text-slate-300 text-sm max-w-xl">
                Manage your digital advertising fleet, track screen statuses, verify pending advertiser requests, and plan campaigns from your command center.
            </p>
        </div>
        <div class="flex items-center gap-3 relative z-10 shrink-0">
            <a href="{{ route('admin.settings') }}" class="px-5 py-2.5 bg-white/10 hover:bg-white/20 text-white rounded-xl text-sm font-semibold transition-all border border-white/10 flex items-center gap-2">
                <i class="bi bi-sliders"></i> System Settings
            </a>
        </div>
    </div>

    <!-- KPI Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-6">
        <!-- Total Locations -->
        <div class="bg-white dark:bg-slate-800 rounded-2xl p-6 border border-slate-100 dark:border-slate-700/50 shadow-sm hover:shadow-md transition-all flex flex-col justify-between">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold text-slate-400 dark:text-slate-500 uppercase tracking-wider text-[10px]">Total Locations</span>
                <div class="w-10 h-10 rounded-xl bg-blue-50 dark:bg-blue-900/20 text-[#1155CC] flex items-center justify-center">
                    <i class="bi bi-geo-alt text-lg"></i>
                </div>
            </div>
            <div class="mt-4">
                <h3 class="text-3xl font-bold text-slate-800 dark:text-slate-100">{{ $kpis['total_locations'] }}</h3>
                <span class="text-xs text-slate-400 font-semibold flex items-center gap-1 mt-1">
                    <i class="bi bi-geo"></i> Total in network
                </span>
            </div>
        </div>

        <!-- Active Locations -->
        <div class="bg-white dark:bg-slate-800 rounded-2xl p-6 border border-slate-100 dark:border-slate-700/50 shadow-sm hover:shadow-md transition-all flex flex-col justify-between">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold text-slate-400 dark:text-slate-500 uppercase tracking-wider text-[10px]">Active Locations</span>
                <div class="w-10 h-10 rounded-xl bg-emerald-50 dark:bg-emerald-900/20 text-emerald-600 flex items-center justify-center">
                    <i class="bi bi-check-circle text-lg"></i>
                </div>
            </div>
            <div class="mt-4">
                <h3 class="text-3xl font-bold text-slate-800 dark:text-slate-100">{{ $kpis['active_locations'] }}</h3>
                <span class="text-xs text-emerald-500 font-semibold flex items-center gap-1 mt-1">
                    <span class="w-1.5 h-1.5 bg-emerald-500 rounded-full animate-pulse"></span> Broadcasting live
                </span>
            </div>
        </div>

        <!-- Inactive Locations -->
        <div class="bg-white dark:bg-slate-800 rounded-2xl p-6 border border-slate-100 dark:border-slate-700/50 shadow-sm hover:shadow-md transition-all flex flex-col justify-between">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold text-slate-400 dark:text-slate-500 uppercase tracking-wider text-[10px]">Inactive Locations</span>
                <div class="w-10 h-10 rounded-xl bg-slate-50 dark:bg-slate-900/20 text-slate-500 flex items-center justify-center">
                    <i class="bi bi-dash-circle text-lg"></i>
                </div>
            </div>
            <div class="mt-4">
                <h3 class="text-3xl font-bold text-slate-800 dark:text-slate-100">{{ $kpis['inactive_locations'] }}</h3>
                <span class="text-xs text-slate-400 font-semibold flex items-center gap-1 mt-1">
                    <i class="bi bi-slash-circle"></i> Off-grid nodes
                </span>
            </div>
        </div>

        <!-- Maintenance Locations -->
        <div class="bg-white dark:bg-slate-800 rounded-2xl p-6 border border-slate-100 dark:border-slate-700/50 shadow-sm hover:shadow-md transition-all flex flex-col justify-between">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold text-slate-400 dark:text-slate-500 uppercase tracking-wider text-[10px]">Maintenance</span>
                <div class="w-10 h-10 rounded-xl bg-amber-50 dark:bg-amber-900/20 text-amber-600 flex items-center justify-center">
                    <i class="bi bi-tools text-lg"></i>
                </div>
            </div>
            <div class="mt-4">
                <h3 class="text-3xl font-bold text-slate-800 dark:text-slate-100">{{ $kpis['maintenance_locations'] }}</h3>
                <span class="text-xs text-amber-600 font-semibold flex items-center gap-1 mt-1">
                    <i class="bi bi-exclamation-triangle"></i> Requires attention
                </span>
            </div>
        </div>

        <!-- Pending Campaigns -->
        <div class="bg-[#1155CC]/5 dark:bg-blue-950/20 border border-[#1155CC]/10 dark:border-blue-900/30 rounded-2xl p-6 shadow-sm hover:shadow-md transition-all flex flex-col justify-between">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold text-[#1155CC] dark:text-blue-400 uppercase tracking-wider text-[10px]">Pending Campaigns</span>
                <div class="w-10 h-10 rounded-xl bg-[#1155CC] text-white flex items-center justify-center shadow-lg shadow-blue-500/10">
                    <i class="bi bi-play-btn text-lg"></i>
                </div>
            </div>
            <div class="mt-4">
                <h3 class="text-3xl font-bold text-slate-800 dark:text-slate-100">{{ $kpis['pending_campaigns'] }}</h3>
                <span class="text-xs text-[#1155CC] dark:text-blue-400 font-semibold flex items-center gap-1 mt-1">
                    <i class="bi bi-clock-history"></i> Awaiting review
                </span>
            </div>
        </div>
    </div>

    <!-- Details Grid (Activity + Categories) -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Left: Platform Activity -->
        <div class="lg:col-span-2 bg-white dark:bg-slate-800 rounded-3xl p-6 md:p-8 border border-slate-100 dark:border-slate-700/50 shadow-sm">
            <h3 class="text-lg font-bold text-slate-800 dark:text-slate-100 mb-6 flex items-center gap-2">
                <i class="bi bi-lightning-charge text-[#1155CC]"></i> Recent Platform Activity
            </h3>
            
            <div class="space-y-4">
                @forelse($paidCampaigns as $campaign)
                    <a href="{{ route('admin.advertising.show', $campaign->id) }}" class="flex items-center justify-between p-4 bg-slate-50 dark:bg-slate-900/50 rounded-2xl border border-slate-100/50 dark:border-slate-800 hover:border-blue-200 transition-all cursor-pointer">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-blue-100/60 dark:bg-blue-900/20 text-[#1155CC] flex items-center justify-center shrink-0">
                                <i class="bi bi-currency-rupee"></i>
                            </div>
                            <div>
                                <span class="block text-sm font-semibold text-slate-700 dark:text-slate-200">{{ $campaign->name }} - Payment Received</span>
                                <span class="block text-xs text-slate-400">Paid {{ $campaign->payment_paid_at->diffForHumans() }} by {{ $campaign->advertiser->user->email ?? 'Unknown' }}</span>
                            </div>
                        </div>
                        <span class="text-xs bg-emerald-100 text-emerald-800 px-3 py-1 rounded-full font-bold uppercase tracking-wider dark:bg-emerald-900/20 dark:text-emerald-450">Paid</span>
                    </a>
                @empty
                    <p class="text-xs text-slate-400 text-center py-4">No recent completed payments.</p>
                @endforelse
            </div>
        </div>

        <!-- Right: Locations By Category -->
        <div class="bg-white dark:bg-slate-800 rounded-3xl p-6 md:p-8 border border-slate-100 dark:border-slate-700/50 shadow-sm flex flex-col justify-between">
            <div>
                <h3 class="text-lg font-bold text-slate-800 dark:text-slate-100 mb-6 flex items-center gap-2">
                    <i class="bi bi-tags-fill text-[#1155CC]"></i> Locations by Category
                </h3>
                
                <div class="space-y-3.5 overflow-y-auto max-h-72 pr-1">
                    @forelse($categoriesList as $cat)
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2.5">
                                <div class="w-8 h-8 rounded-lg bg-slate-50 dark:bg-slate-900 text-slate-500 flex items-center justify-center border border-slate-200/50 dark:border-slate-850 shrink-0">
                                    <i class="bi {{ $cat->icon ?? 'bi-tag-fill' }}"></i>
                                </div>
                                <span class="text-sm font-semibold text-slate-700 dark:text-slate-350">{{ $cat->name }}</span>
                            </div>
                            <span class="text-xs font-bold text-slate-800 dark:text-slate-200 bg-slate-100 dark:bg-slate-900 px-2 py-0.5 rounded-md">
                                {{ $cat->locations_count }}
                            </span>
                        </div>
                    @empty
                        <p class="text-xs text-slate-400 text-center py-4">No categories initialized.</p>
                    @endforelse
                </div>
            </div>
            
            <div class="mt-6 border-t border-slate-100 dark:border-slate-850 pt-4 text-center">
                <a href="{{ route('admin.locations.categories') }}" class="text-xs font-bold text-[#1155CC] hover:underline flex items-center justify-center gap-1">
                    Manage Categories <i class="bi bi-arrow-right"></i>
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
