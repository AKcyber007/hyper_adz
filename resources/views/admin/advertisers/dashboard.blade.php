@extends('admin.layouts.app', [
    'title' => 'Advertiser Dashboard | Hyper Adz Admin'
])

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white">Advertiser Analytics</h1>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Review active client onboarding volumes, pipeline approvals, and monthly metrics growth.</p>
        </div>
        <a href="{{ route('admin.advertisers.index') }}" class="px-4 py-2 bg-[#1155CC] hover:bg-blue-700 text-white rounded-xl text-xs font-bold transition-all shadow-md shadow-blue-500/10 flex items-center gap-1.5">
            <i class="bi bi-person-badge-fill"></i> View Advertisers Profile List
        </a>
    </div>

    <!-- Analytics Cards Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
        <!-- Total -->
        <div class="bg-white dark:bg-slate-800 rounded-3xl p-5 border border-slate-100 dark:border-slate-700/50 shadow-sm flex items-center gap-4 hover:scale-[1.02] transition-all duration-300">
            <div class="w-12 h-12 rounded-2xl bg-blue-50 dark:bg-blue-950/20 text-[#1155CC] dark:text-blue-400 flex items-center justify-center text-xl shrink-0">
                <i class="bi bi-people-fill"></i>
            </div>
            <div>
                <span class="block text-xs font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider">Total Advertisers</span>
                <span class="block text-2xl font-bold text-slate-850 dark:text-white mt-0.5">{{ number_format($metrics['total_advertisers']) }}</span>
            </div>
        </div>

        <!-- Active -->
        <div class="bg-white dark:bg-slate-800 rounded-3xl p-5 border border-slate-100 dark:border-slate-700/50 shadow-sm flex items-center gap-4 hover:scale-[1.02] transition-all duration-300">
            <div class="w-12 h-12 rounded-2xl bg-emerald-50 dark:bg-emerald-950/20 text-emerald-600 dark:text-emerald-450 flex items-center justify-center text-xl shrink-0">
                <i class="bi bi-person-check-fill"></i>
            </div>
            <div>
                <span class="block text-xs font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider">Active</span>
                <span class="block text-2xl font-bold text-slate-850 dark:text-white mt-0.5">{{ number_format($metrics['active_advertisers']) }}</span>
            </div>
        </div>

        <!-- Suspended -->
        <div class="bg-white dark:bg-slate-800 rounded-3xl p-5 border border-slate-100 dark:border-slate-700/50 shadow-sm flex items-center gap-4 hover:scale-[1.02] transition-all duration-300">
            <div class="w-12 h-12 rounded-2xl bg-amber-50 dark:bg-amber-950/20 text-amber-600 dark:text-amber-450 flex items-center justify-center text-xl shrink-0">
                <i class="bi bi-person-x-fill"></i>
            </div>
            <div>
                <span class="block text-xs font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider">Suspended</span>
                <span class="block text-2xl font-bold text-slate-850 dark:text-white mt-0.5">{{ number_format($metrics['suspended_advertisers']) }}</span>
            </div>
        </div>

        <!-- New this Month -->
        <div class="bg-white dark:bg-slate-800 rounded-3xl p-5 border border-slate-100 dark:border-slate-700/50 shadow-sm flex items-center gap-4 hover:scale-[1.02] transition-all duration-300">
            <div class="w-12 h-12 rounded-2xl bg-purple-50 dark:bg-purple-950/20 text-purple-650 dark:text-purple-400 flex items-center justify-center text-xl shrink-0">
                <i class="bi bi-calendar-plus-fill"></i>
            </div>
            <div>
                <span class="block text-xs font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider">New This Month</span>
                <span class="block text-2xl font-bold text-slate-850 dark:text-white mt-0.5">{{ number_format($metrics['new_advertisers_this_month']) }}</span>
            </div>
        </div>
    </div>

    <!-- Charts / Layout Placeholder -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- industry shares -->
        <div class="bg-white dark:bg-slate-800 rounded-3xl p-6 border border-slate-100 dark:border-slate-700/50 shadow-sm lg:col-span-2 space-y-4">
            <h3 class="text-sm font-bold text-slate-800 dark:text-slate-200">Industry Sector Volume Shares</h3>
            
            <div class="p-5 bg-slate-50 dark:bg-slate-900 rounded-2xl border border-slate-200/50 dark:border-slate-850 flex items-center justify-between text-xs text-slate-500">
                <span>Analytics Chart Engine</span>
                <span class="badge bg-[#1155CC]/10 text-[#1155CC] dark:bg-blue-950/20 dark:text-blue-400 px-2.5 py-1 rounded font-bold">Chart integration Pending Phase 5C</span>
            </div>
        </div>

        <!-- Conversion funnel -->
        <div class="bg-white dark:bg-slate-800 rounded-3xl p-6 border border-slate-100 dark:border-slate-700/50 shadow-sm space-y-4">
            <h3 class="text-sm font-bold text-slate-800 dark:text-slate-200">Onboarding Funnel Status</h3>
            
            <div class="space-y-4 pt-2">
                @php
                    $total = max($metrics['total_advertisers'], 1);
                    $activePerc = ($metrics['active_advertisers'] / $total) * 100;
                    $suspendedPerc = ($metrics['suspended_advertisers'] / $total) * 100;
                @endphp

                <div class="space-y-1">
                    <div class="flex items-center justify-between text-xs font-semibold text-slate-700 dark:text-slate-300">
                        <span>Active Brands</span>
                        <span>{{ number_format($activePerc, 1) }}%</span>
                    </div>
                    <div class="w-full h-2 bg-slate-50 dark:bg-slate-900 rounded-full overflow-hidden">
                        <div class="h-full bg-emerald-500" style="width: {{ $activePerc }}%"></div>
                    </div>
                </div>

                <div class="space-y-1">
                    <div class="flex items-center justify-between text-xs font-semibold text-slate-700 dark:text-slate-300">
                        <span>Suspended / Inactive</span>
                        <span>{{ number_format($suspendedPerc, 1) }}%</span>
                    </div>
                    <div class="w-full h-2 bg-slate-50 dark:bg-slate-900 rounded-full overflow-hidden">
                        <div class="h-full bg-amber-500" style="width: {{ $suspendedPerc }}%"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
