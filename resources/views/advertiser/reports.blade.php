@extends('layouts.advertiser')

@section('title', 'Reports & Invoices')

@section('content')
<div class="space-y-6">
    <div>
        <h2 class="text-xl font-extrabold tracking-tight text-slate-900 font-outfit">Reports & Invoices</h2>
        <p class="text-xs text-slate-500 mt-0.5">Download your billing invoices and performance reports for your active advertising campaigns.</p>
    </div>

    <div class="bg-white border border-slate-200 rounded-[32px] shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full border-collapse text-left">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-100">
                        <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Campaign Name</th>
                        <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Location</th>
                        <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Campaign Status</th>
                        <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Report Upload Date</th>
                        <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($campaigns as $camp)
                        <tr class="hover:bg-slate-50/50 transition-colors text-sm text-slate-700">
                            <!-- Campaign Name -->
                            <td class="px-6 py-4">
                                <div class="font-bold text-slate-900">{{ $camp->campaign_name }}</div>
                                <div class="text-[10px] text-slate-400 font-mono mt-0.5">{{ $camp->campaign_code }}</div>
                            </td>

                            <!-- Location -->
                            <td class="px-6 py-4">
                                <div class="flex flex-col gap-1">
                                    @foreach($camp->locations->take(2) as $loc)
                                        <div class="text-xs text-slate-600 truncate max-w-[200px]"><i class="bi bi-geo-alt-fill text-slate-400 mr-1"></i> {{ $loc->name }}</div>
                                    @endforeach
                                    @if($camp->locations->count() > 2)
                                        <div class="text-[10px] font-bold text-[#1155CC]">
                                            +{{ $camp->locations->count() - 2 }} more
                                        </div>
                                    @endif
                                </div>
                            </td>

                            <!-- Campaign Status -->
                            <td class="px-6 py-4 whitespace-nowrap">
                                @php
                                    $statusColors = [
                                        'Scheduled' => 'bg-cyan-50 text-cyan-700 border-cyan-200',
                                        'Running' => 'bg-indigo-50 text-indigo-700 border-indigo-200',
                                        'Completed' => 'bg-purple-50 text-purple-700 border-purple-200',
                                        'Report Uploaded' => 'bg-fuchsia-50 text-fuchsia-700 border-fuchsia-200',
                                    ];
                                    $colorClass = $statusColors[$camp->status] ?? 'bg-slate-50 text-slate-700 border-slate-200';
                                @endphp
                                <span class="inline-flex items-center gap-1 text-[10px] font-bold px-2 py-0.5 rounded-full border {{ $colorClass }}">
                                    {{ $camp->status }}
                                </span>
                            </td>

                            <!-- Report Upload Date -->
                            <td class="px-6 py-4 whitespace-nowrap text-xs font-medium text-slate-500">
                                {{ $camp->report_uploaded_at ? $camp->report_uploaded_at->format('M d, Y h:i A') : 'N/A' }}
                            </td>

                            <!-- Actions -->
                            <td class="px-6 py-4 text-right whitespace-nowrap">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ asset('storage/'.$camp->report_path) }}" target="_blank" class="px-3 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg text-xs font-bold transition-all shadow-sm">
                                        View Report
                                    </a>
                                    <a href="{{ route('advertiser.my-requests.report.download', $camp->id) }}" class="px-3 py-1.5 bg-[#1155CC] hover:bg-blue-700 text-white rounded-lg text-xs font-bold transition-all shadow-sm flex items-center gap-1.5">
                                        <i class="bi bi-download"></i> Download
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-slate-400">
                                <div class="w-14 h-14 rounded-full bg-slate-50 text-slate-400 border border-slate-200 flex items-center justify-center text-xl mx-auto shadow-inner mb-4">
                                    <i class="bi bi-bar-chart-line-fill text-indigo-650"></i>
                                </div>
                                <p class="font-bold text-slate-600 mb-1">No reports available</p>
                                <p class="text-xs">Your performance reports and invoices will appear here once uploaded by the administration.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
