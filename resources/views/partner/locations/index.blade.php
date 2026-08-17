@extends('layouts.partner')

@section('title', 'My Locations')

@section('content')
<div class="space-y-6">
    <!-- Top Actions Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-xl font-extrabold tracking-tight text-slate-900">Registered Locations</h2>
            <p class="text-xs text-slate-500 mt-0.5">Manage your physical venue locations and check their approval states.</p>
        </div>
        <a href="{{ route('partner.map') }}" class="px-4 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-500 text-xs font-bold text-white flex items-center gap-2 transition-all shrink-0 shadow-lg shadow-blue-500/10">
            <i class="bi bi-plus-lg text-sm border-2 border-white rounded-md flex items-center justify-center w-4 h-4"></i> Add New Location
        </a>
    </div>

    <!-- Filters & Search -->
    <div class="bg-white border border-slate-200 p-4 rounded-2xl">
        <form method="GET" action="{{ route('partner.locations.index') }}" class="flex flex-col sm:flex-row items-center gap-4">
            <div class="relative w-full sm:flex-grow">
                <i class="bi bi-search absolute left-4 top-1/2 -translate-y-1/2 text-slate-500 text-xs"></i>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by name, code, or city..." class="w-full pl-10 pr-4 py-2.5 bg-white border border-slate-200 rounded-xl text-xs text-slate-205 placeholder-slate-500 focus:outline-none focus:border-blue-600 focus:ring-1 focus:ring-blue-600 transition-all">
            </div>

            <div class="w-full sm:w-48 shrink-0">
                <select name="status" onchange="this.form.submit()" class="w-full px-4 py-2.5 bg-white border border-slate-200 rounded-xl text-xs text-slate-600 focus:outline-none focus:border-blue-600 transition-all">
                    <option value="">All Statuses</option>
                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active / Approved</option>
                    <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending Review</option>
                    <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Rejected</option>
                </select>
            </div>
            
            @if(request()->anyFilled(['search', 'status']))
                <a href="{{ route('partner.locations.index') }}" class="px-4 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-750 text-xs font-bold text-slate-600 transition-all shrink-0">Clear Filters</a>
            @endif
        </form>
    </div>

    <!-- Locations Table/List -->
    <div class="bg-white border border-slate-200 shadow-sm rounded-2xl overflow-hidden">
        @if($locations->isEmpty())
            <div class="py-16 text-center space-y-3">
                <div class="w-14 h-14 rounded-full bg-white/60 text-slate-500 border border-slate-200 flex items-center justify-center text-xl mx-auto">
                    <i class="bi bi-geo-alt"></i>
                </div>
                <h4 class="text-sm font-bold text-slate-700">No locations found</h4>
                <p class="text-xs text-slate-500 max-w-sm mx-auto">Submit a location request form to begin registering your venues on our DOOH screen grid network.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="border-b border-slate-100 bg-slate-50/50 text-[10px] font-bold text-slate-500 uppercase tracking-wider">
                            <th class="px-6 py-4">Code</th>
                            <th class="px-6 py-4">Location Details</th>
                            <th class="px-6 py-4">Category</th>
                            <th class="px-6 py-4 text-center">Assigned Screens</th>
                            <th class="px-6 py-4">Approval Status</th>
                            <th class="px-6 py-4 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-xs">
                        @foreach($locations as $loc)
                            <tr class="hover:bg-slate-50/80 transition-colors duration-150">
                                <td class="px-6 py-4 font-mono font-bold text-slate-500">{{ $loc->location_code }}</td>
                                <td class="px-6 py-4">
                                    <div>
                                        <a href="{{ route('partner.locations.show', $loc->id) }}" class="font-bold text-slate-900 hover:text-blue-400 transition-colors">{{ $loc->name }}</a>
                                        <p class="text-[10px] text-slate-500 mt-0.5">{{ $loc->address }}, {{ $loc->city }}</p>
                                    </div>
                                </td>
                                <td class="px-6 py-4 font-medium text-slate-600">{{ $loc->category ? $loc->category->name : 'Uncategorized' }}</td>
                                <td class="px-6 py-4 text-center font-bold text-slate-900">{{ $loc->screens->count() }}</td>
                                <td class="px-6 py-4">
                                    <x-status-badge :status="$loc->status" />
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <a href="{{ route('partner.locations.show', $loc->id) }}" class="w-8 h-8 rounded-lg bg-slate-50 hover:bg-slate-100 border border-transparent hover:border-slate-200 text-slate-500 flex items-center justify-center transition-all duration-200" title="View Detail Details">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <a href="{{ route('partner.locations.edit', $loc->id) }}" class="w-8 h-8 rounded-lg bg-slate-50 hover:bg-slate-100 border border-transparent hover:border-slate-200 text-slate-500 flex items-center justify-center transition-all duration-200" title="Edit Profile Details">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <form method="POST" action="{{ route('partner.locations.destroy', $loc->id) }}" onsubmit="return confirm('Are you sure you want to delete this location? This will soft delete the location from the grid.')" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="w-8 h-8 rounded-lg bg-slate-50 hover:bg-rose-50 border border-transparent hover:border-rose-100 text-slate-500 hover:text-rose-600 flex items-center justify-center transition-all duration-200" title="Delete Location">
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

            @if($locations->hasPages())
                <div class="p-6 border-t border-slate-100 bg-white">
                    {{ $locations->links() }}
                </div>
            @endif
        @endif
    </div>
</div>
@endsection
