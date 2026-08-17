@extends('admin.layouts.app', [
    'title' => 'Campaigns for ' . $advertiser->company_name . ' | Hyper Adz Admin'
])

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.advertisers.index') }}" class="w-8 h-8 rounded-xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 flex items-center justify-center text-slate-500 hover:text-slate-900 dark:text-slate-400 dark:hover:text-white transition-all shadow-sm">
                <i class="bi bi-arrow-left"></i>
            </a>
            <div>
                <h1 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white">Campaigns for {{ $advertiser->company_name }}</h1>
                <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Review all campaigns for this advertiser.</p>
            </div>
        </div>
    </div>

    <!-- Alerts -->
    @if(session('success'))
        <div class="p-4 bg-emerald-50 border border-emerald-100 dark:bg-emerald-950/10 dark:border-emerald-900/30 text-emerald-700 dark:text-emerald-400 rounded-2xl text-xs font-semibold flex items-center gap-2">
            <i class="bi bi-check-circle-fill"></i> {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="p-4 bg-rose-50 border border-rose-100 dark:bg-rose-950/10 dark:border-rose-900/30 text-rose-700 dark:text-rose-400 rounded-2xl text-xs font-semibold flex items-center gap-2">
            <i class="bi bi-exclamation-circle-fill"></i> {{ session('error') }}
        </div>
    @endif

    <!-- Campaigns Table -->
    <div class="bg-white dark:bg-slate-800 rounded-3xl border border-slate-100 dark:border-slate-700/50 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full border-collapse text-left">
                <thead>
                    <tr class="border-b border-slate-100 dark:border-slate-750 bg-slate-50/50 dark:bg-slate-850/20">
                        <th class="px-6 py-4 text-xs font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider">Campaign Code</th>
                        <th class="px-6 py-4 text-xs font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider">Campaign Name</th>
                        <th class="px-6 py-4 text-xs font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider">Type</th>
                        <th class="px-6 py-4 text-xs font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider">Date Range</th>
                        <th class="px-6 py-4 text-xs font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider">Budget</th>
                        <th class="px-6 py-4 text-xs font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-4 text-xs font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-750">
                    @forelse($campaigns as $camp)
                        <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-850/5 transition-all text-xs text-slate-700 dark:text-slate-350">
                            <!-- Code -->
                            <td class="px-6 py-4 whitespace-nowrap font-mono text-[10px] text-slate-400 font-bold">
                                {{ $camp->campaign_code }}
                            </td>

                            <!-- Campaign Name -->
                            <td class="px-6 py-4 font-bold text-slate-700 dark:text-slate-300">
                                {{ $camp->campaign_name }}
                            </td>

                            <!-- Type -->
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center gap-1 text-[10px] font-bold bg-indigo-50 dark:bg-indigo-950/20 text-[#1155CC] dark:text-indigo-400 border border-indigo-100 dark:border-indigo-900/30 px-2.5 py-0.5 rounded-full">
                                    {{ $camp->campaign_type }}
                                </span>
                            </td>

                            <!-- Date Range -->
                            <td class="px-6 py-4 whitespace-nowrap text-slate-500 dark:text-slate-400">
                                {{ $camp->start_date->format('M d') }} - {{ $camp->end_date->format('M d, Y') }}
                            </td>

                            <!-- Budget -->
                            <td class="px-6 py-4 whitespace-nowrap font-bold text-slate-800 dark:text-slate-200">
                                ₹{{ number_format($camp->budget, 2) }}
                            </td>

                            <!-- Status Badge -->
                            <td class="px-6 py-4 whitespace-nowrap">
                                @php
                                    $statusColors = [
                                        'Draft' => 'bg-slate-100 text-slate-700 border-slate-200',
                                        'Submitted' => 'bg-amber-50 text-amber-700 border-amber-200',
                                        'Creative Review' => 'bg-blue-50 text-blue-700 border-blue-200',
                                        'Approved' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                        'Payment Pending' => 'bg-orange-50 text-orange-700 border-orange-200',
                                        'Payment Completed' => 'bg-teal-50 text-teal-700 border-teal-200',
                                        'Scheduled' => 'bg-cyan-50 text-cyan-700 border-cyan-200',
                                        'Running' => 'bg-indigo-50 text-indigo-700 border-indigo-200',
                                        'Completed' => 'bg-purple-50 text-purple-700 border-purple-200',
                                        'Report Uploaded' => 'bg-fuchsia-50 text-fuchsia-700 border-fuchsia-200',
                                        'Rejected (Admin)' => 'bg-rose-50 text-rose-700 border-rose-200',
                                        'Rejected (Payment Expired)' => 'bg-red-50 text-red-700 border-red-200',
                                    ];
                                    $colorClass = $statusColors[$camp->status] ?? 'bg-slate-50 text-slate-700 border-slate-200';
                                @endphp
                                <span class="inline-flex items-center gap-1 text-[10px] font-bold px-2 py-0.5 rounded-full border {{ $colorClass }}">
                                    {{ $camp->status }}
                                </span>
                            </td>

                            <!-- Detail link -->
                            <td class="px-6 py-4 text-right whitespace-nowrap">
                                <a href="{{ route('admin.advertising.requests.show', $camp->id) }}" class="px-3 py-1.5 bg-[#1155CC] hover:bg-blue-700 text-white rounded-lg text-xs font-bold transition-all shadow-sm">
                                    Review
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-slate-450">
                                <div class="max-w-xs mx-auto space-y-2 py-4">
                                    <i class="bi bi-file-earmark-play text-3xl text-slate-300 dark:text-slate-700"></i>
                                    <p class="font-bold text-slate-700 dark:text-slate-350">No campaigns found</p>
                                    <p class="text-[11px]">There are no campaigns matching the criteria.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($campaigns->hasPages())
            <div class="px-6 py-4 border-t border-slate-100 dark:border-slate-750 bg-slate-50/30 dark:bg-slate-850/10">
                {{ $campaigns->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
