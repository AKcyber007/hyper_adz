@extends('layouts.advertiser')

@section('title', 'Create Campaign')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-slate-900 font-outfit">Campaigns & Reports</h1>
            <p class="text-xs text-slate-550 mt-1">Track bookings, payments, and view performance reports for your campaigns.</p>
        </div>
        <a href="{{ route('advertiser.my-requests.create') }}" class="px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-xs font-bold transition-all shadow-lg shadow-indigo-600/20 flex items-center gap-2">
            <i class="bi bi-plus-lg"></i>
            <span>New Campaign Request</span>
        </a>
    </div>

    <!-- Status Tabs -->
    <div class="flex items-center gap-2 overflow-x-auto pb-2 border-b border-slate-200 hide-scrollbar">
        <a href="{{ route('advertiser.my-requests', ['tab' => 'all']) }}" class="px-4 py-2 text-xs font-bold whitespace-nowrap rounded-t-xl border-b-2 transition-all {{ (!isset($tab) || $tab === 'all') ? 'text-indigo-600 border-indigo-600 bg-indigo-50/50' : 'text-slate-500 border-transparent hover:text-slate-700 hover:bg-slate-50' }}">
            All Campaigns
        </a>
        <a href="{{ route('advertiser.my-requests', ['tab' => 'action_required']) }}" class="px-4 py-2 text-xs font-bold whitespace-nowrap rounded-t-xl border-b-2 transition-all {{ (isset($tab) && $tab === 'action_required') ? 'text-rose-600 border-rose-600 bg-rose-50/50' : 'text-slate-500 border-transparent hover:text-slate-700 hover:bg-slate-50' }}">
            Action Required
        </a>
        <a href="{{ route('advertiser.my-requests', ['tab' => 'pending']) }}" class="px-4 py-2 text-xs font-bold whitespace-nowrap rounded-t-xl border-b-2 transition-all {{ (isset($tab) && $tab === 'pending') ? 'text-amber-600 border-amber-600 bg-amber-50/50' : 'text-slate-500 border-transparent hover:text-slate-700 hover:bg-slate-50' }}">
            Pending Review
        </a>
        <a href="{{ route('advertiser.my-requests', ['tab' => 'active']) }}" class="px-4 py-2 text-xs font-bold whitespace-nowrap rounded-t-xl border-b-2 transition-all {{ (isset($tab) && $tab === 'active') ? 'text-emerald-600 border-emerald-600 bg-emerald-50/50' : 'text-slate-500 border-transparent hover:text-slate-700 hover:bg-slate-50' }}">
            Active
        </a>
        <a href="{{ route('advertiser.my-requests', ['tab' => 'completed']) }}" class="px-4 py-2 text-xs font-bold whitespace-nowrap rounded-t-xl border-b-2 transition-all {{ (isset($tab) && $tab === 'completed') ? 'text-purple-600 border-purple-600 bg-purple-50/50' : 'text-slate-500 border-transparent hover:text-slate-700 hover:bg-slate-50' }}">
            Completed / Reports
        </a>
    </div>

    <!-- Requests Table -->
    <div class="bg-white border border-slate-200/80 rounded-[32px] overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full border-collapse text-left">
                <thead>
                    <tr class="border-b border-slate-100 bg-slate-50">
                        <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Campaign Code</th>
                        <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Campaign Name</th>
                        <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Type</th>
                        <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Date Range</th>
                        <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Budget</th>
                        <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Locations</th>
                        <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($campaigns as $camp)
                        <tr class="hover:bg-slate-50/50 transition-all text-xs text-slate-600">
                            <!-- Code -->
                            <td class="px-6 py-4 whitespace-nowrap font-mono text-[10px] text-slate-400 font-bold">
                                {{ $camp->campaign_code }}
                            </td>

                            <!-- Name -->
                            <td class="px-6 py-4 font-bold text-slate-800">
                                {{ $camp->campaign_name }}
                            </td>

                            <!-- Type -->
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center gap-1 text-[9px] font-bold bg-indigo-55/60 text-indigo-700 border border-indigo-100 px-2 py-0.5 rounded-full">
                                    {{ $camp->campaign_type }}
                                </span>
                            </td>

                            <!-- Date Range -->
                            <td class="px-6 py-4 whitespace-nowrap text-slate-500">
                                {{ $camp->start_date->format('M d') }} - {{ $camp->end_date->format('M d, Y') }}
                            </td>

                            <!-- Budget -->
                            <td class="px-6 py-4 whitespace-nowrap font-bold text-slate-800">
                                ₹{{ number_format($camp->budget, 2) }}
                            </td>

                            <!-- Locations count -->
                            <td class="px-6 py-4 whitespace-nowrap font-semibold">
                                <span class="text-indigo-650">{{ $camp->locations->count() }}</span> locations
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

                            <!-- Action link -->
                            <td class="px-6 py-4 text-right whitespace-nowrap space-x-1">
                                @if($camp->status === 'Draft')
                                    <a href="{{ route('advertiser.my-requests.edit', $camp->id) }}" class="px-2.5 py-1 bg-white hover:bg-slate-50 text-[10px] font-bold text-indigo-600 rounded-lg transition-colors border border-indigo-200">
                                        Edit
                                    </a>
                                @endif
                                @if($camp->status === 'Payment Pending')
                                    <a href="{{ route('advertiser.my-requests.show', $camp->id) }}" class="px-2.5 py-1 bg-orange-600 hover:bg-orange-700 text-[10px] font-bold text-white rounded-lg transition-colors border border-orange-700 shadow-sm">
                                        Pay Now
                                    </a>
                                @endif
                                @if($camp->status === 'Report Uploaded' && $camp->report_path)
                                    <a href="{{ route('advertiser.my-requests.report.download', $camp->id) }}" class="px-2.5 py-1 bg-purple-50 hover:bg-purple-100 text-[10px] font-bold text-purple-700 rounded-lg transition-colors border border-purple-200">
                                        Download Report
                                    </a>
                                @endif
                                <a href="{{ route('advertiser.my-requests.show', $camp->id) }}" class="px-2.5 py-1 bg-slate-100 hover:bg-slate-200 text-[10px] font-bold text-slate-700 rounded-lg transition-colors border border-slate-200">
                                    Details
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-12 text-center text-slate-400">
                                <div class="max-w-xs mx-auto space-y-2 py-4">
                                    <i class="bi bi-file-earmark-play text-3xl text-slate-300"></i>
                                    <p class="font-bold text-slate-800">No advertising requests yet</p>
                                    <p class="text-[11px] text-slate-500">Submit your first digital indoor screen slot request by clicking the button above.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
