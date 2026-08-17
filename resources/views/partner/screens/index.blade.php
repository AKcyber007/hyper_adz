@extends('layouts.partner')

@section('title', 'My Screens')

@section('content')
<div class="space-y-6">
    <!-- Top Actions Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-xl font-extrabold tracking-tight text-slate-900">Registered Screens</h2>
            <p class="text-xs text-slate-500 mt-0.5">Manage your digital screens, monitoring states, and specs.</p>
        </div>
        <a href="{{ route('partner.screens.create') }}" class="px-4 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-500 text-xs font-bold text-slate-900 flex items-center gap-2 transition-all shrink-0 shadow-lg shadow-blue-500/10">
            <i class="bi bi-plus-lg"></i> Add New Screen
        </a>
    </div>

    <!-- Filters & Search -->
    <div class="bg-white border border-slate-200 p-4 rounded-2xl">
        <form method="GET" action="{{ route('partner.screens.index') }}" class="flex flex-col sm:flex-row items-center gap-4">
            <div class="relative w-full sm:flex-grow">
                <i class="bi bi-search absolute left-4 top-1/2 -translate-y-1/2 text-slate-500 text-xs"></i>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by screen name or code..." class="w-full pl-10 pr-4 py-2.5 bg-white border border-slate-200 rounded-xl text-xs text-slate-205 placeholder-slate-500 focus:outline-none focus:border-blue-600 focus:ring-1 focus:ring-blue-600 transition-all">
            </div>

            <div class="w-full sm:w-48 shrink-0">
                <select name="status" onchange="this.form.submit()" class="w-full px-4 py-2.5 bg-white border border-slate-200 rounded-xl text-xs text-slate-600 focus:outline-none focus:border-blue-600 transition-all">
                    <option value="">All Statuses</option>
                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active / Online</option>
                    <option value="offline" {{ request('status') === 'offline' ? 'selected' : '' }}>Offline</option>
                    <option value="maintenance" {{ request('status') === 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                    <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending Review</option>
                    <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Rejected</option>
                </select>
            </div>
            
            @if(request()->anyFilled(['search', 'status']))
                <a href="{{ route('partner.screens.index') }}" class="px-4 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-750 text-xs font-bold text-slate-600 transition-all shrink-0">Clear Filters</a>
            @endif
        </form>
    </div>

    <!-- Screens Table/List -->
    <div class="bg-white border border-slate-200 rounded-[32px] overflow-hidden">
        @if($screens->isEmpty())
            <div class="py-16 text-center space-y-3">
                <div class="w-14 h-14 rounded-full bg-white/60 text-slate-500 border border-slate-200 flex items-center justify-center text-xl mx-auto">
                    <i class="bi bi-display"></i>
                </div>
                <h4 class="text-sm font-bold text-slate-700">No screens found</h4>
                <p class="text-xs text-slate-500 max-w-sm mx-auto">Submit a screen request form to register screens under your approved venue locations.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="border-b border-slate-200 text-[10px] font-bold text-slate-500 uppercase tracking-wider bg-white/10">
                            <th class="px-6 py-4">Code</th>
                            <th class="px-6 py-4">Screen Name</th>
                            <th class="px-6 py-4">Location</th>
                            <th class="px-6 py-4">Type</th>
                            <th class="px-6 py-4">Status / Health</th>
                            <th class="px-6 py-4 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-850/60 text-xs">
                        @foreach($screens as $scr)
                            <tr class="hover:bg-white/10 transition-colors">
                                <td class="px-6 py-4 font-mono font-bold text-slate-500">{{ $scr->screen_code }}</td>
                                <td class="px-6 py-4">
                                    <div>
                                        <a href="{{ route('partner.screens.show', $scr->id) }}" class="font-bold text-slate-900 hover:text-blue-400 transition-colors">{{ $scr->name }}</a>
                                        <p class="text-[10px] text-slate-500 mt-0.5">{{ $scr->screen_identifier ?: 'No custom identifier' }}</p>
                                    </div>
                                </td>
                                <td class="px-6 py-4 font-medium text-slate-600">{{ $scr->location ? $scr->location->name : 'N/A' }}</td>
                                <td class="px-6 py-4 font-medium text-slate-600">{{ $scr->type ? $scr->type->name : 'N/A' }}</td>
                                <td class="px-6 py-4">
                                    <x-status-badge :status="$scr->status" />
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <a href="{{ route('partner.screens.show', $scr->id) }}" class="w-8 h-8 rounded-lg bg-slate-100 hover:bg-slate-750 text-slate-600 flex items-center justify-center transition-all" title="View details">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <a href="{{ route('partner.screens.edit', $scr->id) }}" class="w-8 h-8 rounded-lg bg-slate-100 hover:bg-slate-750 text-slate-600 flex items-center justify-center transition-all" title="Edit Screen">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <form method="POST" action="{{ route('partner.screens.destroy', $scr->id) }}" onsubmit="return confirm('Are you sure you want to delete this screen?')" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="w-8 h-8 rounded-lg bg-slate-100 hover:bg-rose-950/40 text-slate-600 hover:text-rose-455 flex items-center justify-center transition-all" title="Delete Screen">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if($screens->hasPages())
                <div class="p-6 border-t border-slate-200 bg-white/10">
                    {{ $screens->links() }}
                </div>
            @endif
        @endif
    </div>
</div>
@endsection
