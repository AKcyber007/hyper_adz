@extends('layouts.partner')

@section('title', 'Dashboard')

@section('content')
<div class="space-y-8">
    <!-- Welcome Banner -->
    <div class="bg-gradient-to-r from-blue-50 to-indigo-50 border border-blue-100 rounded-2xl p-6 sm:p-8 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-6 shadow-sm">
        <div class="space-y-2">
            <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-emerald-100 text-emerald-700 border border-emerald-200 rounded-full text-[10px] font-bold uppercase tracking-wider">
                <span class="w-1.5 h-1.5 bg-emerald-500 rounded-full"></span> Live Account Status: Active
            </span>
            <h2 class="text-2xl font-extrabold tracking-tight text-slate-900">Welcome back, {{ $profile->company_name }}!</h2>
            <p class="text-xs text-slate-600 leading-relaxed max-w-xl">Manage your venues, register screens, track approvals, and check network health status from your operations hub dashboard.</p>
        </div>
        <div class="px-5 py-3 bg-white border border-slate-200 shadow-sm rounded-xl text-center shrink-0">
            <span class="block text-[9px] font-bold text-slate-500 uppercase tracking-wider">Login Count</span>
            <span class="block text-2xl font-extrabold text-blue-600 mt-0.5">{{ number_format($profile->login_count) }}</span>
        </div>
    </div>

    <!-- Inventory Stats Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
        <div class="bg-white border border-slate-200 shadow-sm hover:shadow-md hover:-translate-y-0.5 transition-all duration-200 ease-in-out rounded-2xl p-5 flex items-center gap-4">
            <div class="w-11 h-11 rounded-xl bg-blue-50 border border-blue-100 flex items-center justify-center text-blue-500 text-lg shrink-0">
                <i class="bi bi-geo-alt-fill"></i>
            </div>
            <div>
                <span class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider">Total Locations</span>
                <span class="block text-2xl font-extrabold text-slate-900 mt-0.5">{{ $stats['total_locations'] }}</span>
            </div>
        </div>

        <div class="bg-white border border-slate-200 shadow-sm hover:shadow-md hover:-translate-y-0.5 transition-all duration-200 ease-in-out rounded-2xl p-5 flex items-center gap-4">
            <div class="w-11 h-11 rounded-xl bg-amber-50 border border-amber-100 flex items-center justify-center text-amber-500 text-lg shrink-0">
                <i class="bi bi-clock-history"></i>
            </div>
            <div>
                <span class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider">Pending Approvals</span>
                <span class="block text-2xl font-extrabold text-slate-900 mt-0.5">{{ $stats['pending_approvals'] }}</span>
            </div>
        </div>

        <div class="bg-white border border-slate-200 shadow-sm hover:shadow-md hover:-translate-y-0.5 transition-all duration-200 ease-in-out rounded-2xl p-5 flex items-center gap-4">
            <div class="w-11 h-11 rounded-xl bg-indigo-50 border border-indigo-100 flex items-center justify-center text-indigo-500 text-lg shrink-0">
                <i class="bi bi-play-circle-fill"></i>
            </div>
            <div>
                <span class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider">Running Campaigns</span>
                <span class="block text-2xl font-extrabold text-slate-900 mt-0.5">{{ $stats['running_campaigns'] }}</span>
            </div>
        </div>
    </div>

    <!-- Main Content Area Grid -->
    <div class="flex flex-col lg:flex-row gap-8">
        <!-- Profile Overview -->
        <div class="w-full lg:w-1/3 flex flex-col space-y-6">
            <!-- Business Profile Summary Card -->
            <div class="bg-white border border-slate-200 shadow-sm rounded-2xl p-6 space-y-5 h-full">
                <h3 class="text-sm font-bold text-slate-900 border-b border-slate-100 pb-4 flex items-center gap-2">
                    <i class="bi bi-person-vcard-fill text-blue-500"></i> Business Profile
                </h3>
                <div class="space-y-4 text-xs">
                    <div class="space-y-1">
                        <span class="block text-[10px] text-slate-500 font-bold uppercase tracking-wider">Company Name</span>
                        <span class="block text-slate-800 font-semibold">{{ $profile->company_name }}</span>
                    </div>
                    <div class="space-y-1">
                        <span class="block text-[10px] text-slate-500 font-bold uppercase tracking-wider">Contact Person</span>
                        <span class="block text-slate-700 font-medium">{{ $profile->contact_person }}</span>
                    </div>
                    <div class="space-y-1">
                        <span class="block text-[10px] text-slate-500 font-bold uppercase tracking-wider">Email Address</span>
                        <span class="block text-slate-700 font-mono">{{ $profile->email }}</span>
                    </div>
                    <div class="space-y-1">
                        <span class="block text-[10px] text-slate-500 font-bold uppercase tracking-wider">Phone</span>
                        <span class="block text-slate-700 font-mono">{{ $profile->phone }}</span>
                    </div>
                    <div class="space-y-1">
                        <span class="block text-[10px] text-slate-500 font-bold uppercase tracking-wider">City</span>
                        <span class="block text-slate-700">{{ $profile->city ?? '—' }}</span>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Locations Widget -->
        <div class="w-full lg:w-2/3 flex flex-col space-y-6">
            <div class="bg-white border border-slate-200 shadow-sm rounded-2xl p-6 space-y-5 h-full">
                <h3 class="text-sm font-bold text-slate-900 border-b border-slate-100 pb-4 flex items-center gap-2">
                    <i class="bi bi-geo-alt-fill text-blue-500"></i> Your Locations
                </h3>
                
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="border-b border-slate-100 bg-slate-50/50">
                                <th class="px-4 py-3 text-[10px] font-bold text-slate-500 uppercase tracking-wider rounded-tl-lg">Location</th>
                                <th class="px-4 py-3 text-[10px] font-bold text-slate-500 uppercase tracking-wider">Code</th>
                                <th class="px-4 py-3 text-[10px] font-bold text-slate-500 uppercase tracking-wider">City</th>
                                <th class="px-4 py-3 text-[10px] font-bold text-slate-500 uppercase tracking-wider rounded-tr-lg">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($locations->take(5) as $loc)
                                <tr class="hover:bg-slate-50/80 transition-colors duration-150">
                                    <td class="px-4 py-3 text-xs font-bold text-slate-800">
                                        {{ $loc->name }}
                                    </td>
                                    <td class="px-4 py-3 text-[10px] font-mono text-slate-500">
                                        {{ $loc->location_code }}
                                    </td>
                                    <td class="px-4 py-3 text-xs text-slate-600">
                                        {{ $loc->city }}
                                    </td>
                                    <td class="px-4 py-3">
                                        <span class="inline-flex items-center gap-1 text-[10px] font-bold px-2 py-0.5 rounded-full border {{ $loc->status === 'active' || $loc->status === 'approved' ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : ($loc->status === 'rejected' ? 'bg-rose-50 text-rose-700 border-rose-200' : 'bg-indigo-50 text-indigo-700 border-indigo-200') }}">
                                            {{ ucfirst($loc->status) }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-4 py-8 text-center text-slate-400">
                                        <p class="text-xs font-semibold text-slate-500">No locations added</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if($locations->count() > 5)
                <div class="pt-2 text-right">
                    <a href="{{ route('partner.locations.index') }}" class="text-xs font-bold text-blue-600 hover:text-blue-700 transition-colors">View All {{ $locations->count() }} Locations &rarr;</a>
                </div>
                @endif
            </div>
        </div>
    </div>
    
    <!-- Running Campaigns Widget -->
    <div class="space-y-6 mt-8">
            <div class="bg-white border border-slate-200 shadow-sm rounded-2xl p-6 space-y-5">
                <h3 class="text-sm font-bold text-slate-900 border-b border-slate-100 pb-4 flex items-center gap-2">
                    <i class="bi bi-play-circle-fill text-indigo-500"></i> Active Campaigns on Your Screens
                </h3>
                
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="border-b border-slate-100 bg-slate-50/50">
                                <th class="px-4 py-3 text-[10px] font-bold text-slate-500 uppercase tracking-wider rounded-tl-lg">Campaign</th>
                                <th class="px-4 py-3 text-[10px] font-bold text-slate-500 uppercase tracking-wider">Advertiser</th>
                                <th class="px-4 py-3 text-[10px] font-bold text-slate-500 uppercase tracking-wider">Date Range</th>
                                <th class="px-4 py-3 text-[10px] font-bold text-slate-500 uppercase tracking-wider rounded-tr-lg">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($running_campaigns as $camp)
                                <tr class="hover:bg-slate-50/80 transition-colors duration-150">
                                    <td class="px-4 py-3">
                                        <div class="text-xs font-bold text-slate-800">{{ $camp->campaign_name }}</div>
                                        <div class="text-[10px] text-slate-400 font-mono mt-0.5">{{ $camp->campaign_code }}</div>
                                    </td>
                                    <td class="px-4 py-3 text-xs font-semibold text-slate-700">
                                        {{ $camp->advertiser->company_name ?? 'Unknown' }}
                                    </td>
                                    <td class="px-4 py-3 text-xs text-slate-500">
                                        {{ $camp->start_date->format('M d') }} - {{ $camp->end_date->format('M d, Y') }}
                                    </td>
                                    <td class="px-4 py-3">
                                        <span class="inline-flex items-center gap-1 text-[10px] font-bold px-2 py-0.5 rounded-full border {{ $camp->status === 'Running' ? 'bg-indigo-50 text-indigo-700 border-indigo-200' : 'bg-slate-50 text-slate-700 border-slate-200' }}">
                                            {{ $camp->status }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-4 py-8 text-center text-slate-400">
                                        <p class="text-xs font-semibold text-slate-500">No active campaigns</p>
                                        <p class="text-[10px] mt-1">There are no campaigns currently running or scheduled for your locations.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

