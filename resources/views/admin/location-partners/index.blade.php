@extends('admin.layouts.app', [
    'title' => 'Location Partners | Hyper Adz Admin'
])

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white">Location Partners</h1>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Manage venue partner profiles, GST registrations, and link advertising screen inventories.</p>
        </div>
        <a href="{{ route('admin.location-partners.create') }}" class="px-4 py-2 bg-[#1155CC] hover:bg-blue-700 text-white rounded-xl text-xs font-bold transition-all shadow-md shadow-blue-500/10 flex items-center gap-1.5">
            <i class="bi bi-person-plus-fill"></i> Add Partner Manually
        </a>
    </div>

    <!-- Navigation Tabs -->
    <div class="flex border-b border-slate-200 dark:border-slate-800 mt-2">
        <a href="{{ route('admin.location-partners.index') }}" class="px-5 py-3 border-b-2 border-[#1155CC] text-[#1155CC] dark:text-blue-400 dark:border-blue-400 font-bold text-sm flex items-center gap-2 transition-all">
            <i class="bi bi-building text-base"></i>
            <span>Verified Partners</span>
        </a>
        <a href="{{ route('admin.leads.index', ['lead_type' => 'location_partner']) }}" class="px-5 py-3 border-b-2 border-transparent hover:border-slate-350 dark:hover:border-slate-700 text-slate-400 hover:text-slate-700 dark:hover:text-slate-200 font-medium text-sm flex items-center gap-2 transition-all">
            <i class="bi bi-building-fill text-base"></i>
            <span>Partner Leads</span>
            @php
                $pendingLeadsCount = \App\Models\Lead::where('lead_type', 'location_partner')->where('status', 'pending')->count();
            @endphp
            @if($pendingLeadsCount > 0)
                <span class="text-[10px] font-bold bg-amber-500 text-slate-950 px-2 py-0.5 rounded-full shrink-0 shadow-sm">
                    {{ $pendingLeadsCount }}
                </span>
            @endif
        </a>
    </div>

    <!-- Feedback Alerts -->
    @if(session('success'))
        <div class="p-4 bg-emerald-50 border border-emerald-100 dark:bg-emerald-950/10 dark:border-emerald-900/30 text-emerald-700 dark:text-emerald-400 rounded-2xl text-xs font-semibold flex items-center gap-2">
            <i class="bi bi-check-circle-fill"></i> {{ session('success') }}
        </div>
    @endif

    <!-- Search & Filters Panel -->
    <div class="bg-white dark:bg-slate-800 rounded-3xl border border-slate-100 dark:border-slate-700/50 shadow-sm p-5">
        <form method="GET" action="{{ route('admin.location-partners.index') }}" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-6 gap-4">
            <!-- Search Text -->
            <div class="space-y-1 md:col-span-2">
                <label class="text-[10px] font-bold text-slate-450 uppercase tracking-wider block">Search</label>
                <div class="relative">
                    <i class="bi bi-search absolute left-3.5 top-3 text-slate-400 text-sm"></i>
                    <input type="text" name="search" value="{{ $filters['search'] ?? '' }}" placeholder="Name, code, company, phone, email, GST..." class="w-full pl-10 pr-4 py-2 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-850 rounded-xl text-xs text-slate-700 dark:text-slate-200 focus:outline-none focus:border-[#1155CC] transition-all">
                </div>
            </div>

            <!-- Status Filter -->
            <div class="space-y-1">
                <label class="text-[10px] font-bold text-slate-450 uppercase tracking-wider block">Status</label>
                <select name="status" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-850 rounded-xl text-xs text-slate-700 dark:text-slate-200 focus:outline-none focus:border-[#1155CC] transition-all">
                    <option value="">All Statuses</option>
                    <option value="pending" {{ ($filters['status'] ?? '') === 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="active" {{ ($filters['status'] ?? '') === 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ ($filters['status'] ?? '') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                    <option value="suspended" {{ ($filters['status'] ?? '') === 'suspended' ? 'selected' : '' }}>Suspended</option>
                </select>
            </div>

            <!-- Assignment Filter -->
            <div class="space-y-1">
                <label class="text-[10px] font-bold text-slate-450 uppercase tracking-wider block">Assignments</label>
                <select name="assignment" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-850 rounded-xl text-xs text-slate-700 dark:text-slate-200 focus:outline-none focus:border-[#1155CC] transition-all">
                    <option value="">All Partners</option>
                    <option value="assigned" {{ ($filters['assignment'] ?? '') === 'assigned' ? 'selected' : '' }}>Has Assigned Locations</option>
                    <option value="unassigned" {{ ($filters['assignment'] ?? '') === 'unassigned' ? 'selected' : '' }}>No Assigned Locations</option>
                </select>
            </div>

            <!-- Date Range Start -->
            <div class="space-y-1">
                <label class="text-[10px] font-bold text-slate-450 uppercase tracking-wider block">Start Date</label>
                <input type="date" name="start_date" value="{{ $filters['start_date'] ?? '' }}" class="w-full px-3 py-1.5 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-850 rounded-xl text-xs text-slate-700 dark:text-slate-200 focus:outline-none focus:border-[#1155CC] transition-all">
            </div>

            <!-- Date Range End / Actions -->
            <div class="space-y-1 flex items-end gap-2">
                <div class="flex-grow space-y-1">
                    <label class="text-[10px] font-bold text-slate-450 uppercase tracking-wider block">End Date</label>
                    <input type="date" name="end_date" value="{{ $filters['end_date'] ?? '' }}" class="w-full px-3 py-1.5 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-850 rounded-xl text-xs text-slate-700 dark:text-slate-200 focus:outline-none focus:border-[#1155CC] transition-all">
                </div>
                <button type="submit" class="p-2.5 bg-[#1155CC] hover:bg-blue-700 text-white rounded-xl transition-all shadow-md shadow-blue-500/10 shrink-0">
                    <i class="bi bi-funnel-fill"></i>
                </button>
                @if(count(array_filter($filters)) > 0)
                    <a href="{{ route('admin.location-partners.index') }}" class="p-2.5 bg-slate-100 hover:bg-slate-200 dark:bg-slate-700 dark:hover:bg-slate-650 text-slate-550 dark:text-slate-300 rounded-xl transition-all shrink-0">
                        <i class="bi bi-arrow-counterclockwise"></i>
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Data Table -->
    <div class="bg-white dark:bg-slate-800 rounded-3xl border border-slate-100 dark:border-slate-700/50 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full border-collapse text-left">
                <thead>
                    <tr class="border-b border-slate-100 dark:border-slate-750 bg-slate-50/50 dark:bg-slate-850/20">
                        <th class="px-6 py-4 text-xs font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider">Logo</th>
                        <th class="px-6 py-4 text-xs font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider">Code</th>
                        <th class="px-6 py-4 text-xs font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider">Company Name</th>
                        <th class="px-6 py-4 text-xs font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider">Contact Person</th>
                        <th class="px-6 py-4 text-xs font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider">Contact Details</th>
                        <th class="px-6 py-4 text-xs font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider">Locations</th>
                        <th class="px-6 py-4 text-xs font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider">Screens</th>
                        <th class="px-6 py-4 text-xs font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-4 text-xs font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider">Created Date</th>
                        <th class="px-6 py-4 text-xs font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-750">
                    @forelse($partners as $partner)
                        <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-850/5 transition-all text-xs text-slate-700 dark:text-slate-350">
                            <!-- Logo Thumbnail -->
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="w-10 h-10 rounded-xl overflow-hidden border border-slate-200/60 dark:border-slate-700 bg-slate-50 dark:bg-slate-900 flex items-center justify-center shadow-inner">
                                    @if($partner->logo_path)
                                        <img src="{{ Storage::url($partner->logo_path) }}" class="w-full h-full object-cover">
                                    @else
                                        <i class="bi bi-building text-slate-400 text-base"></i>
                                    @endif
                                </div>
                            </td>

                            <!-- Code -->
                            <td class="px-6 py-4 font-mono font-bold text-[#1155CC] dark:text-blue-400">
                                {{ $partner->partner_code }}
                            </td>

                            <!-- Company Name -->
                            <td class="px-6 py-4 font-bold text-slate-800 dark:text-slate-200">
                                {{ $partner->company_name }}
                            </td>

                            <!-- Contact Person -->
                            <td class="px-6 py-4 font-semibold">
                                {{ $partner->contact_person }}
                            </td>

                            <!-- Contact Details -->
                            <td class="px-6 py-4">
                                <div class="font-medium">{{ $partner->email }}</div>
                                <div class="text-[10px] text-slate-400 font-mono mt-0.5">{{ $partner->phone }}</div>
                            </td>

                            <!-- Locations count -->
                            <td class="px-6 py-4 font-bold">
                                {{ $partner->locations_count }}
                            </td>

                            <!-- Screens count -->
                            <td class="px-6 py-4 font-bold">
                                {{ $partner->screens_count }}
                            </td>

                            <!-- Status Badge -->
                            <td class="px-6 py-4">
                                @if($partner->status === 'active')
                                    <span class="inline-flex items-center gap-1 text-[10px] font-bold bg-emerald-50 dark:bg-emerald-950/20 text-emerald-600 dark:text-emerald-450 border border-emerald-100 dark:border-emerald-900/30 px-2 py-0.5 rounded-full">
                                        <span class="w-1 h-1 bg-emerald-500 rounded-full"></span> Active
                                    </span>
                                @elseif($partner->status === 'suspended')
                                    <span class="inline-flex items-center gap-1 text-[10px] font-bold bg-amber-50 dark:bg-amber-950/20 text-amber-600 dark:text-amber-450 border border-amber-100 dark:border-amber-900/30 px-2 py-0.5 rounded-full">
                                        <span class="w-1 h-1 bg-amber-500 rounded-full animate-pulse"></span> Suspended
                                    </span>
                                @elseif($partner->status === 'inactive')
                                    <span class="inline-flex items-center gap-1 text-[10px] font-bold bg-slate-100 dark:bg-slate-900 text-slate-500 dark:text-slate-400 border border-slate-200 dark:border-slate-800 px-2 py-0.5 rounded-full">
                                        <span class="w-1 h-1 bg-slate-400 rounded-full"></span> Inactive
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 text-[10px] font-bold bg-indigo-50 dark:bg-indigo-950/20 text-indigo-650 dark:text-indigo-400 border border-indigo-100 dark:border-indigo-900/30 px-2 py-0.5 rounded-full">
                                        <span class="w-1 h-1 bg-indigo-500 rounded-full animate-pulse"></span> Pending
                                    </span>
                                @endif
                            </td>

                            <!-- Date Created -->
                            <td class="px-6 py-4 font-mono text-[10px] text-slate-400">
                                {{ $partner->created_at->format('M d, Y') }}
                            </td>

                            <!-- Actions -->
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-1.5">
                                    <a href="{{ route('admin.location-partners.show', $partner->id) }}" class="w-8 h-8 rounded-lg bg-blue-50/50 hover:bg-blue-100/50 text-[#1155CC] flex items-center justify-center transition-all border border-blue-100/20" title="View Profile and assign locations">
                                        <i class="bi bi-eye-fill"></i>
                                    </a>
                                    <a href="{{ route('admin.location-partners.edit', $partner->id) }}" class="w-8 h-8 rounded-lg bg-slate-50 dark:bg-slate-900 hover:bg-slate-100 dark:hover:bg-slate-800 text-slate-500 hover:text-slate-700 dark:hover:text-slate-200 flex items-center justify-center transition-all border border-slate-200/50 dark:border-slate-800" title="Edit Partner Details">
                                        <i class="bi bi-pencil-square"></i>
                                    </a>
                                    <form action="{{ route('admin.location-partners.destroy', $partner->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this partner profile? All locations assignment will be removed.');" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="w-8 h-8 rounded-lg bg-rose-50/50 hover:bg-rose-100/50 text-rose-600 flex items-center justify-center transition-all border border-rose-100/20" title="Delete Profile">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="px-6 py-12 text-center text-slate-400">
                                <div class="max-w-xs mx-auto space-y-2 py-4">
                                    <i class="bi bi-building text-3xl text-slate-300 dark:text-slate-700"></i>
                                    <p class="font-bold text-slate-750 dark:text-slate-350">No location partners found</p>
                                    <p class="text-[11px]">Convert approved partner leads or register new partners manually.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($partners->hasPages())
            <div class="px-6 py-4 border-t border-slate-100 dark:border-slate-750 bg-slate-50/30 dark:bg-slate-850/10">
                {{ $partners->appends($filters)->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
