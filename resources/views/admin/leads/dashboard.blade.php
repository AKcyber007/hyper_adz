@extends('admin.layouts.app', [
    'title' => 'Leads Dashboard | Hyper Adz Admin'
])

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white">Leads Analytics</h1>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Acquisition pipelines, advertiser inquiries, partner onboarding counts, and workflow conversions.</p>
        </div>
        <a href="{{ route('admin.leads.index') }}" class="px-4 py-2 bg-[#1155CC] hover:bg-blue-700 text-white rounded-xl text-xs font-bold transition-all shadow-md shadow-blue-500/10 flex items-center gap-1.5">
            <i class="bi bi-list-task"></i> View All Leads
        </a>
    </div>

    <!-- Analytics Cards Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-6 gap-5">
        <!-- Total Leads -->
        <div class="bg-white dark:bg-slate-800 rounded-3xl p-5 border border-slate-100 dark:border-slate-700/50 shadow-sm flex items-center gap-4 hover:scale-[1.02] transition-all duration-300">
            <div class="w-11 h-11 rounded-2xl bg-blue-50 dark:bg-blue-950/20 text-[#1155CC] dark:text-blue-400 flex items-center justify-center text-lg shrink-0">
                <i class="bi bi-people-fill"></i>
            </div>
            <div>
                <span class="block text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider">Total Leads</span>
                <span class="block text-xl font-bold text-slate-850 dark:text-white mt-0.5">{{ number_format($metrics['total_leads']) }}</span>
            </div>
        </div>

        <!-- New Leads -->
        <div class="bg-white dark:bg-slate-800 rounded-3xl p-5 border border-slate-100 dark:border-slate-700/50 shadow-sm flex items-center gap-4 hover:scale-[1.02] transition-all duration-300">
            <div class="w-11 h-11 rounded-2xl bg-indigo-50 dark:bg-indigo-950/20 text-indigo-600 dark:text-indigo-400 flex items-center justify-center text-lg shrink-0">
                <i class="bi bi-envelope-fill"></i>
            </div>
            <div>
                <span class="block text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider">New</span>
                <span class="block text-xl font-bold text-slate-850 dark:text-white mt-0.5">{{ number_format($metrics['new_leads']) }}</span>
            </div>
        </div>

        <!-- Contacted -->
        <div class="bg-white dark:bg-slate-800 rounded-3xl p-5 border border-slate-100 dark:border-slate-700/50 shadow-sm flex items-center gap-4 hover:scale-[1.02] transition-all duration-300">
            <div class="w-11 h-11 rounded-2xl bg-sky-50 dark:bg-sky-950/20 text-sky-600 dark:text-sky-400 flex items-center justify-center text-lg shrink-0">
                <i class="bi bi-telephone-outbound-fill"></i>
            </div>
            <div>
                <span class="block text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider">Contacted</span>
                <span class="block text-xl font-bold text-slate-850 dark:text-white mt-0.5">{{ number_format($metrics['contacted_leads']) }}</span>
            </div>
        </div>

        <!-- Qualified -->
        <div class="bg-white dark:bg-slate-800 rounded-3xl p-5 border border-slate-100 dark:border-slate-700/50 shadow-sm flex items-center gap-4 hover:scale-[1.02] transition-all duration-300">
            <div class="w-11 h-11 rounded-2xl bg-amber-50 dark:bg-amber-950/20 text-amber-600 dark:text-amber-450 flex items-center justify-center text-lg shrink-0">
                <i class="bi bi-award-fill"></i>
            </div>
            <div>
                <span class="block text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider">Qualified</span>
                <span class="block text-xl font-bold text-slate-850 dark:text-white mt-0.5">{{ number_format($metrics['qualified_leads']) }}</span>
            </div>
        </div>

        <!-- Approved -->
        <div class="bg-white dark:bg-slate-800 rounded-3xl p-5 border border-slate-100 dark:border-slate-700/50 shadow-sm flex items-center gap-4 hover:scale-[1.02] transition-all duration-300">
            <div class="w-11 h-11 rounded-2xl bg-emerald-50 dark:bg-emerald-950/20 text-emerald-600 dark:text-emerald-400 flex items-center justify-center text-lg shrink-0">
                <i class="bi bi-patch-check-fill"></i>
            </div>
            <div>
                <span class="block text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider">Approved</span>
                <span class="block text-xl font-bold text-slate-850 dark:text-white mt-0.5">{{ number_format($metrics['approved_leads']) }}</span>
            </div>
        </div>

        <!-- Rejected -->
        <div class="bg-white dark:bg-slate-800 rounded-3xl p-5 border border-slate-100 dark:border-slate-700/50 shadow-sm flex items-center gap-4 hover:scale-[1.02] transition-all duration-300">
            <div class="w-11 h-11 rounded-2xl bg-red-50 dark:bg-red-950/20 text-red-650 dark:text-red-450 flex items-center justify-center text-lg shrink-0">
                <i class="bi bi-x-octagon-fill"></i>
            </div>
            <div>
                <span class="block text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider">Rejected</span>
                <span class="block text-xl font-bold text-slate-850 dark:text-white mt-0.5">{{ number_format($metrics['rejected_leads']) }}</span>
            </div>
        </div>
    </div>

    <!-- Analytics Placeholder Area -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Lead Types Share Chart -->
        <div class="bg-white dark:bg-slate-800 rounded-3xl p-6 border border-slate-100 dark:border-slate-700/50 shadow-sm lg:col-span-2">
            <h3 class="text-sm font-bold text-slate-800 dark:text-slate-200 flex items-center gap-2 mb-6">
                <i class="bi bi-bar-chart-fill text-[#1155CC]"></i> Acquisition Channel Distribution
            </h3>
            
            <div class="space-y-6">
                <div class="p-4 bg-slate-50 dark:bg-slate-900 rounded-2xl border border-slate-200/50 dark:border-slate-850 flex items-center justify-between text-xs">
                    <span class="text-slate-500">Analytics Graph Tool</span>
                    <span class="badge bg-[#1155CC]/10 text-[#1155CC] dark:bg-blue-950/20 dark:text-blue-400 px-2.5 py-1 rounded font-bold">Chart integration Pending Phase 5B</span>
                </div>

                <div class="grid grid-cols-3 gap-4 text-center">
                    <div class="p-4 rounded-2xl bg-blue-50/40 dark:bg-blue-950/5 border border-blue-50 dark:border-blue-950/10">
                        <span class="block text-[10px] uppercase font-bold text-slate-450">Contact Us Channel</span>
                        <span class="block text-base font-extrabold text-slate-700 dark:text-slate-300 mt-1">Website Form</span>
                    </div>
                    <div class="p-4 rounded-2xl bg-emerald-50/40 dark:bg-emerald-950/5 border border-emerald-50 dark:border-emerald-950/10">
                        <span class="block text-[10px] uppercase font-bold text-slate-450">Advertise Channel</span>
                        <span class="block text-base font-extrabold text-slate-700 dark:text-slate-300 mt-1">Partner Portal</span>
                    </div>
                    <div class="p-4 rounded-2xl bg-purple-50/40 dark:bg-purple-950/5 border border-purple-50 dark:border-purple-950/10">
                        <span class="block text-[10px] uppercase font-bold text-slate-450">Partner Channel</span>
                        <span class="block text-base font-extrabold text-slate-700 dark:text-slate-300 mt-1">Venues Portal</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- conversion metrics -->
        <div class="bg-white dark:bg-slate-800 rounded-3xl p-6 border border-slate-100 dark:border-slate-700/50 shadow-sm">
            <h3 class="text-sm font-bold text-slate-800 dark:text-slate-200 flex items-center gap-2 mb-6">
                <i class="bi bi-funnel-fill text-amber-600"></i> Conversion Funnel
            </h3>
            
            <div class="space-y-4">
                @php
                    $total = max($metrics['total_leads'], 1);
                    $approvedPerc = ($metrics['approved_leads'] / $total) * 100;
                    $qualifiedPerc = ($metrics['qualified_leads'] / $total) * 100;
                    $newPerc = ($metrics['new_leads'] / $total) * 100;
                @endphp

                <!-- funnel stages -->
                <div class="space-y-1">
                    <div class="flex items-center justify-between text-xs font-semibold text-slate-700 dark:text-slate-300">
                        <span>New Inbox Incoming</span>
                        <span>{{ number_format($newPerc, 1) }}%</span>
                    </div>
                    <div class="w-full h-2 bg-slate-50 dark:bg-slate-900 rounded-full overflow-hidden">
                        <div class="h-full bg-indigo-500" style="width: {{ $newPerc }}%"></div>
                    </div>
                </div>

                <div class="space-y-1">
                    <div class="flex items-center justify-between text-xs font-semibold text-slate-700 dark:text-slate-300">
                        <span>Qualified Leads</span>
                        <span>{{ number_format($qualifiedPerc, 1) }}%</span>
                    </div>
                    <div class="w-full h-2 bg-slate-50 dark:bg-slate-900 rounded-full overflow-hidden">
                        <div class="h-full bg-amber-500" style="width: {{ $qualifiedPerc }}%"></div>
                    </div>
                </div>

                <div class="space-y-1">
                    <div class="flex items-center justify-between text-xs font-semibold text-slate-700 dark:text-slate-300">
                        <span>Approved (Ready for account)</span>
                        <span>{{ number_format($approvedPerc, 1) }}%</span>
                    </div>
                    <div class="w-full h-2 bg-slate-50 dark:bg-slate-900 rounded-full overflow-hidden">
                        <div class="h-full bg-emerald-500" style="width: {{ $approvedPerc }}%"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
