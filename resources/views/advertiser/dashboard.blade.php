@extends('layouts.advertiser')

@section('title', 'Dashboard')

@section('content')
<div class="space-y-8">
    <!-- Welcome Banner -->
    <div class="bg-gradient-to-r from-indigo-50 to-blue-50 border border-indigo-100 rounded-[32px] p-6 sm:p-8 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-6 shadow-sm">
        <div class="space-y-2">
            <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-emerald-50 text-emerald-700 border border-emerald-200 rounded-full text-[10px] font-bold uppercase tracking-wider">
                <span class="w-1.5 h-1.5 bg-emerald-500 rounded-full"></span> Live Account Status: Active
            </span>
            <h2 class="text-2xl font-extrabold tracking-tight text-slate-900">Welcome back, {{ $profile->company_name }}!</h2>
            <p class="text-xs text-slate-600 leading-relaxed max-w-xl">Configure your campaigns, manage target screen bookings, upload media creatives, and analyze delivery performance metrics.</p>
        </div>
        <div class="px-5 py-3 bg-white border border-slate-200 rounded-2xl text-center shrink-0 shadow-sm">
            <span class="block text-[9px] font-bold text-slate-400 uppercase tracking-wider">Login Count</span>
            <span class="block text-2xl font-extrabold text-slate-900 mt-0.5">{{ number_format($profile->login_count) }}</span>
        </div>
    </div>

    <!-- Campaigns Stats Cards (Coming Soon Placeholders) -->
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-3 lg:gap-5">
        <div class="bg-white border border-slate-200/80 rounded-[24px] p-5 flex items-center gap-4 relative overflow-hidden group shadow-sm">
            <div class="w-11 h-11 rounded-xl bg-indigo-50 text-indigo-600 border border-indigo-100 flex items-center justify-center text-lg shrink-0">
                <i class="bi bi-play-btn-fill"></i>
            </div>
            <div>
                <span class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider">Total Campaigns</span>
                <span class="block text-2xl font-extrabold text-slate-900 mt-0.5">{{ $stats['total_campaigns'] }}</span>
            </div>
        </div>
        <div class="bg-white border border-slate-200/80 rounded-[24px] p-5 flex items-center gap-4 relative overflow-hidden group shadow-sm">
            <div class="w-11 h-11 rounded-xl bg-emerald-50 text-emerald-600 border border-emerald-100 flex items-center justify-center text-lg shrink-0">
                <i class="bi bi-check-circle-fill"></i>
            </div>
            <div>
                <span class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider">Active Campaigns</span>
                <span class="block text-2xl font-extrabold text-slate-900 mt-0.5">{{ $stats['active_campaigns'] }}</span>
            </div>
        </div>
        <div class="bg-white border border-slate-200/80 rounded-[24px] p-5 flex items-center gap-4 relative overflow-hidden group shadow-sm">
            <div class="w-11 h-11 rounded-xl bg-amber-50 text-amber-600 border border-amber-100 flex items-center justify-center text-lg shrink-0">
                <i class="bi bi-clock-fill"></i>
            </div>
            <div>
                <span class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider">Pending Approvals</span>
                <span class="block text-2xl font-extrabold text-slate-900 mt-0.5">{{ $stats['pending_campaigns'] }}</span>
            </div>
        </div>
        <div class="bg-white border border-slate-200/80 rounded-[24px] p-5 flex items-center gap-4 relative overflow-hidden group shadow-sm">
            <div class="w-11 h-11 rounded-xl bg-purple-50 text-purple-600 border border-purple-100 flex items-center justify-center text-lg shrink-0">
                <i class="bi bi-calendar-event-fill"></i>
            </div>
            <div>
                <span class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider">Scheduled</span>
                <span class="block text-2xl font-extrabold text-slate-900 mt-0.5">{{ $stats['scheduled_campaigns'] }}</span>
            </div>
        </div>
        <div class="bg-white border border-slate-200/80 rounded-[24px] p-5 flex items-center gap-4 relative overflow-hidden group shadow-sm">
            <div class="w-11 h-11 rounded-xl bg-slate-100 text-slate-600 border border-slate-200 flex items-center justify-center text-lg shrink-0">
                <i class="bi bi-archive-fill"></i>
            </div>
            <div>
                <span class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider">Completed</span>
                <span class="block text-2xl font-extrabold text-slate-900 mt-0.5">{{ $stats['completed_campaigns'] }}</span>
            </div>
        </div>
    </div>

    <!-- Main Workspace Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Profile summary -->
        <div class="space-y-6">
            <div class="bg-white border border-slate-200/80 rounded-[32px] p-6 space-y-5 shadow-sm">
                <h3 class="text-sm font-bold text-slate-900 border-b border-slate-100 pb-4 flex items-center gap-2">
                    <i class="bi bi-person-vcard-fill text-indigo-600"></i> Company Profile
                </h3>
                <div class="space-y-4 text-xs">
                    <div class="space-y-1">
                        <span class="block text-[10px] text-slate-400 font-bold uppercase tracking-wider">Company Name</span>
                        <span class="block text-slate-800 font-semibold">{{ $profile->company_name }}</span>
                    </div>
                    <div class="space-y-1">
                        <span class="block text-[10px] text-slate-400 font-bold uppercase tracking-wider">Contact Person</span>
                        <span class="block text-slate-800 font-medium">{{ $profile->contact_person }}</span>
                    </div>
                    <div class="space-y-1">
                        <span class="block text-[10px] text-slate-400 font-bold uppercase tracking-wider">Email Address</span>
                        <span class="block text-slate-800 font-mono">{{ $profile->email }}</span>
                    </div>
                    <div class="space-y-1">
                        <span class="block text-[10px] text-slate-400 font-bold uppercase tracking-wider">Phone</span>
                        <span class="block text-slate-800 font-mono">{{ $profile->phone }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Pane: Recent Campaigns -->
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white border border-slate-200/80 rounded-[32px] p-6 sm:p-8 space-y-6 shadow-sm">
                <div class="flex items-center justify-between pb-4 border-b border-slate-100">
                    <h3 class="text-sm font-bold text-slate-900 flex items-center gap-2">
                        <i class="bi bi-play-circle text-indigo-600"></i> Recent Campaigns
                    </h3>
                    <a href="{{ route('advertiser.my-requests') }}" class="text-[11px] font-bold text-indigo-600 hover:text-indigo-800 transition-colors">
                        View All
                    </a>
                </div>

                <div class="text-center space-y-4">
                    <p class="text-xs text-slate-500">Go to your Campaign Requests to view and manage your advertising campaigns.</p>
                    <a href="{{ route('advertiser.my-requests.create') }}" class="inline-block mt-2 px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-xs font-bold transition-all shadow-md">
                        Create New Campaign
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
