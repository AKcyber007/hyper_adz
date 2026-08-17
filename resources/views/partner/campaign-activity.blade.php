@extends('layouts.partner')
@section('title', 'Campaign Activity')

@section('content')
<div class="space-y-8">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl font-extrabold text-slate-900">Campaign Activity</h1>
            <p class="text-xs text-slate-500 mt-0.5">Live and upcoming advertising campaigns at your locations.</p>
        </div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
        <div class="bg-white border border-slate-200 shadow-sm hover:shadow-md hover:-translate-y-0.5 transition-all duration-200 ease-in-out rounded-2xl p-5 flex items-center gap-4">
            <div class="w-11 h-11 rounded-xl bg-emerald-50 border border-emerald-100 flex items-center justify-center text-emerald-600 text-lg shrink-0"><i class="bi bi-play-fill"></i></div>
            <div>
                <span class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider">Running Now</span>
                <span class="block text-2xl font-extrabold text-slate-900 mt-0.5">{{ $running->count() }}</span>
            </div>
        </div>
        <div class="bg-white border border-slate-200 shadow-sm hover:shadow-md hover:-translate-y-0.5 transition-all duration-200 ease-in-out rounded-2xl p-5 flex items-center gap-4">
            <div class="w-11 h-11 rounded-xl bg-purple-50 border border-purple-100 flex items-center justify-center text-purple-600 text-lg shrink-0"><i class="bi bi-calendar-event-fill"></i></div>
            <div>
                <span class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider">Upcoming</span>
                <span class="block text-2xl font-extrabold text-slate-900 mt-0.5">{{ $upcoming->count() }}</span>
            </div>
        </div>
        <div class="bg-white border border-slate-200 shadow-sm hover:shadow-md hover:-translate-y-0.5 transition-all duration-200 ease-in-out rounded-2xl p-5 flex items-center gap-4">
            <div class="w-11 h-11 rounded-xl bg-slate-50 border border-slate-100 flex items-center justify-center text-slate-500 text-lg shrink-0"><i class="bi bi-archive-fill"></i></div>
            <div>
                <span class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider">Completed</span>
                <span class="block text-2xl font-extrabold text-slate-900 mt-0.5">{{ $completed->count() }}</span>
            </div>
        </div>
    </div>

    <div class="bg-white border border-slate-200 shadow-sm rounded-2xl p-6 sm:p-8 space-y-5">
        <h2 class="text-sm font-bold text-slate-900 flex items-center gap-2 border-b border-slate-100 pb-4">
            <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse inline-block"></span> Running Campaigns
        </h2>
        @if($running->isEmpty())
            <div class="py-10 text-center space-y-2">
                <div class="w-12 h-12 rounded-full bg-slate-100 text-slate-400 flex items-center justify-center text-xl mx-auto"><i class="bi bi-play-circle"></i></div>
                <p class="text-xs text-slate-500">No campaigns currently running at your locations.</p>
            </div>
        @else
            <div class="space-y-3">
                @foreach($running as $campaign)
                <div class="border border-slate-200 hover:border-emerald-200 bg-white hover:bg-emerald-50/30 rounded-2xl p-5 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 shadow-sm hover:shadow transition-all duration-200 ease-in-out">
                    <div class="space-y-1.5">
                        <div class="flex items-center gap-2">
                            <span class="text-sm font-bold text-slate-900">{{ $campaign->campaign_name }}</span>
                            <span class="text-[9px] font-bold bg-emerald-50 text-emerald-600 border border-emerald-100 px-2 py-0.5 rounded-full">LIVE</span>
                        </div>
                        <p class="text-xs text-slate-500">By <span class="font-semibold text-slate-700">{{ $campaign->advertiser->company_name ?? 'N/A' }}</span></p>
                        <div class="flex items-center gap-4 text-[10px] text-slate-500 font-medium">
                            <span><i class="bi bi-calendar3 mr-1 text-slate-400"></i>{{ \Carbon\Carbon::parse($campaign->start_date)->format('d M Y') }} - {{ \Carbon\Carbon::parse($campaign->end_date)->format('d M Y') }}</span>
                            <span><i class="bi bi-geo-alt mr-1 text-slate-400"></i>{{ $campaign->locations->whereIn('id', $locationIds)->pluck('name')->implode(', ') }}</span>
                        </div>
                    </div>
                    <div class="text-xs text-slate-500 shrink-0 sm:text-right bg-slate-50 border border-slate-100 px-4 py-2 rounded-xl">
                        <span class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider">Ends in</span>
                        <span class="block text-emerald-600 font-extrabold mt-0.5">{{ \Carbon\Carbon::parse($campaign->end_date)->diffForHumans() }}</span>
                    </div>
                </div>
                @endforeach
            </div>
        @endif
    </div>

    <div class="bg-white border border-slate-200 shadow-sm rounded-2xl p-6 sm:p-8 space-y-5">
        <h2 class="text-sm font-bold text-slate-900 flex items-center gap-2 border-b border-slate-100 pb-4">
            <i class="bi bi-calendar-event-fill text-purple-500"></i> Upcoming Campaigns
        </h2>
        @if($upcoming->isEmpty())
            <div class="py-10 text-center space-y-2">
                <div class="w-12 h-12 rounded-full bg-slate-100 text-slate-400 flex items-center justify-center text-xl mx-auto"><i class="bi bi-calendar-plus"></i></div>
                <p class="text-xs text-slate-500">No upcoming campaigns scheduled at your locations yet.</p>
            </div>
        @else
            <div class="space-y-3">
                @foreach($upcoming as $campaign)
                <div class="border border-slate-200 hover:border-purple-200 bg-white hover:bg-purple-50/30 rounded-2xl p-5 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 shadow-sm hover:shadow transition-all duration-200 ease-in-out">
                    <div class="space-y-1.5">
                        <div class="flex items-center gap-2">
                            <span class="text-sm font-bold text-slate-900">{{ $campaign->campaign_name }}</span>
                            <span class="text-[9px] font-bold bg-purple-50 text-purple-600 border border-purple-100 px-2 py-0.5 rounded-full">{{ strtoupper($campaign->status) }}</span>
                        </div>
                        <p class="text-xs text-slate-500">By <span class="font-semibold text-slate-700">{{ $campaign->advertiser->company_name ?? 'N/A' }}</span></p>
                        <div class="flex items-center gap-4 text-[10px] text-slate-500 font-medium">
                            <span><i class="bi bi-calendar3 mr-1 text-slate-400"></i>{{ \Carbon\Carbon::parse($campaign->start_date)->format('d M Y') }} - {{ \Carbon\Carbon::parse($campaign->end_date)->format('d M Y') }}</span>
                            <span><i class="bi bi-geo-alt mr-1 text-slate-400"></i>{{ $campaign->locations->whereIn('id', $locationIds)->pluck('name')->implode(', ') }}</span>
                        </div>
                    </div>
                    <div class="text-xs text-slate-500 shrink-0 sm:text-right bg-slate-50 border border-slate-100 px-4 py-2 rounded-xl">
                        <span class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider">Starts</span>
                        <span class="block text-purple-600 font-extrabold mt-0.5">{{ \Carbon\Carbon::parse($campaign->start_date)->diffForHumans() }}</span>
                    </div>
                </div>
                @endforeach
            </div>
        @endif
    </div>

    <div class="bg-white border border-slate-200 shadow-sm rounded-2xl p-6 sm:p-8 space-y-5">
        <h2 class="text-sm font-bold text-slate-900 flex items-center gap-2 border-b border-slate-100 pb-4">
            <i class="bi bi-archive-fill text-slate-400"></i> Recently Completed
        </h2>
        @if($completed->isEmpty())
            <div class="py-8 text-center"><p class="text-xs text-slate-500">No completed campaigns yet.</p></div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-xs text-left border-collapse">
                    <thead>
                        <tr class="border-b border-slate-100 text-[10px] font-bold text-slate-400 uppercase tracking-wider">
                            <th class="pb-3">Campaign</th><th class="pb-3">Advertiser</th><th class="pb-3">Duration</th><th class="pb-3">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        @foreach($completed as $campaign)
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="py-3 font-semibold text-slate-800">{{ $campaign->campaign_name }}</td>
                            <td class="py-3 text-slate-600">{{ $campaign->advertiser->company_name ?? 'N/A' }}</td>
                            <td class="py-3 text-slate-500">{{ \Carbon\Carbon::parse($campaign->start_date)->format('d M') }} - {{ \Carbon\Carbon::parse($campaign->end_date)->format('d M Y') }}</td>
                            <td class="py-3"><span class="text-[10px] font-bold bg-slate-100 text-slate-600 px-2 py-0.5 rounded-full">{{ $campaign->status }}</span></td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>
@endsection

