@extends('admin.layouts.app', [
    'title' => 'Location Details | Hyper Adz Admin'
])

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.locations.index') }}" class="w-8 h-8 rounded-xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 flex items-center justify-center text-slate-500 hover:text-slate-900 dark:text-slate-400 dark:hover:text-white transition-all shadow-sm">
                <i class="bi bi-arrow-left"></i>
            </a>
            <div>
                <h1 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white">{{ $location->name }}</h1>
                <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">{{ $location->city }}, {{ $location->state }}</p>
            </div>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('admin.locations.edit', $location->id) }}" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 dark:bg-slate-700 dark:hover:bg-slate-650 text-slate-800 dark:text-slate-200 rounded-xl text-xs font-bold transition-all border border-slate-200/50 dark:border-slate-800 flex items-center gap-1.5">
                <i class="bi bi-pencil-square"></i> Edit
            </a>
        </div>
    </div>

    <!-- Campaigns Table -->
    <div class="bg-white dark:bg-slate-800 rounded-3xl border border-slate-100 dark:border-slate-700/50 shadow-sm p-6 space-y-4">
        <h3 class="text-sm font-bold text-slate-800 dark:text-slate-100 border-b border-slate-100 dark:border-slate-700 pb-3">Campaigns at this Location</h3>

        <div class="overflow-x-auto">
            <table class="w-full border-collapse text-left">
                <thead>
                    <tr class="border-b border-slate-100 dark:border-slate-750 bg-slate-50/50 dark:bg-slate-850/20">
                        <th class="px-6 py-4 text-xs font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider">Campaign Name</th>
                        <th class="px-6 py-4 text-xs font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider">Advertiser</th>
                        <th class="px-6 py-4 text-xs font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider">Date Range</th>
                        <th class="px-6 py-4 text-xs font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-4 text-xs font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-750">
                    @forelse($campaigns as $camp)
                        <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-850/5 transition-all text-xs text-slate-700 dark:text-slate-350">
                            <!-- Campaign Name -->
                            <td class="px-6 py-4 font-bold text-slate-700 dark:text-slate-300">
                                {{ $camp->campaign_name }}
                                <div class="text-[10px] text-slate-400 font-mono mt-0.5">{{ $camp->campaign_code }}</div>
                            </td>

                            <!-- Advertiser -->
                            <td class="px-6 py-4 font-bold text-slate-800 dark:text-slate-200">
                                {{ $camp->advertiser->company_name ?? 'Unknown' }}
                            </td>

                            <!-- Date Range -->
                            <td class="px-6 py-4 whitespace-nowrap text-slate-500 dark:text-slate-400">
                                {{ $camp->start_date->format('M d') }} - {{ $camp->end_date->format('M d, Y') }}
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
                            <td colspan="5" class="px-6 py-12 text-center text-slate-450">
                                <p class="font-bold text-slate-700 dark:text-slate-350">No campaigns at this location</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($campaigns->hasPages())
            <div class="pt-4 border-t border-slate-100 dark:border-slate-750">
                {{ $campaigns->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
