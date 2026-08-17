@extends('admin.layouts.app', [
    'title' => 'Leads Inventory | Hyper Adz Admin'
])

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white">{{ $pageTitle ?? 'Leads Management' }}</h1>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Review advertiser and partner onboarding enquiries, general contact emails, and capture pipelines.</p>
        </div>
    </div>

    <!-- Feedback Alerts -->
    @if(session('success'))
        <div class="p-4 bg-emerald-50 border border-emerald-100 dark:bg-emerald-950/10 dark:border-emerald-900/30 text-emerald-700 dark:text-emerald-400 rounded-2xl text-xs font-semibold flex items-center gap-2">
            <i class="bi bi-check-circle-fill"></i> {{ session('success') }}
        </div>
    @endif

    <!-- Filters & Search Panel -->
    <div class="bg-white dark:bg-slate-800 rounded-3xl border border-slate-100 dark:border-slate-700/50 shadow-sm p-5">
        <form method="GET" action="{{ route('admin.leads.index') }}" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-6 gap-4">
            <!-- Search Text -->
            <div class="space-y-1 md:col-span-2">
                <label class="text-[10px] font-bold text-slate-450 uppercase tracking-wider block">Search</label>
                <div class="relative">
                    <i class="bi bi-search absolute left-3.5 top-3 text-slate-400 text-sm"></i>
                    <input type="text" name="search" value="{{ $filters['search'] ?? '' }}" placeholder="Name, code, company, phone, email..." class="w-full pl-10 pr-4 py-2 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-850 rounded-xl text-xs text-slate-700 dark:text-slate-200 focus:outline-none focus:border-[#1155CC] transition-all">
                </div>
            </div>

            <!-- Lead Type Filter -->
            <div class="space-y-1">
                <label class="text-[10px] font-bold text-slate-450 uppercase tracking-wider block">Lead Type</label>
                <select name="lead_type" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-850 rounded-xl text-xs text-slate-700 dark:text-slate-200 focus:outline-none focus:border-[#1155CC] transition-all">
                    <option value="">All Types</option>
                    <option value="contact" {{ ($filters['lead_type'] ?? '') === 'contact' ? 'selected' : '' }}>Contact Request</option>
                    <option value="advertiser" {{ ($filters['lead_type'] ?? '') === 'advertiser' ? 'selected' : '' }}>Advertiser</option>
                    <option value="location_partner" {{ ($filters['lead_type'] ?? '') === 'location_partner' ? 'selected' : '' }}>Location Partner</option>
                    <option value="digital_signage" {{ ($filters['lead_type'] ?? '') === 'digital_signage' ? 'selected' : '' }}>Digital Signage</option>
                    <option value="sales_partner" {{ ($filters['lead_type'] ?? '') === 'sales_partner' ? 'selected' : '' }}>Sales Partner</option>
                </select>
            </div>

            <!-- Status Filter -->
            <div class="space-y-1">
                <label class="text-[10px] font-bold text-slate-450 uppercase tracking-wider block">Status</label>
                <select name="status" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-850 rounded-xl text-xs text-slate-700 dark:text-slate-200 focus:outline-none focus:border-[#1155CC] transition-all">
                    <option value="">All Statuses</option>
                    <option value="new" {{ ($filters['status'] ?? '') === 'new' ? 'selected' : '' }}>New</option>
                    <option value="contacted" {{ ($filters['status'] ?? '') === 'contacted' ? 'selected' : '' }}>Contacted</option>
                    <option value="qualified" {{ ($filters['status'] ?? '') === 'qualified' ? 'selected' : '' }}>Qualified</option>
                    <option value="approved" {{ ($filters['status'] ?? '') === 'approved' ? 'selected' : '' }}>Approved</option>
                    <option value="rejected" {{ ($filters['status'] ?? '') === 'rejected' ? 'selected' : '' }}>Rejected</option>
                </select>
            </div>

            <!-- Date Range Filters -->
            <div class="space-y-1">
                <label class="text-[10px] font-bold text-slate-450 uppercase tracking-wider block">Start Date</label>
                <input type="date" name="start_date" value="{{ $filters['start_date'] ?? '' }}" class="w-full px-3 py-1.5 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-850 rounded-xl text-xs text-slate-700 dark:text-slate-200 focus:outline-none focus:border-[#1155CC] transition-all">
            </div>

            <div class="space-y-1 flex items-end gap-2">
                <div class="flex-grow space-y-1">
                    <label class="text-[10px] font-bold text-slate-450 uppercase tracking-wider block">End Date</label>
                    <input type="date" name="end_date" value="{{ $filters['end_date'] ?? '' }}" class="w-full px-3 py-1.5 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-850 rounded-xl text-xs text-slate-700 dark:text-slate-200 focus:outline-none focus:border-[#1155CC] transition-all">
                </div>
                <button type="submit" class="p-2.5 bg-[#1155CC] hover:bg-blue-700 text-white rounded-xl transition-all shadow-md shadow-blue-500/10 shrink-0" title="Search Leads">
                    <i class="bi bi-funnel-fill"></i>
                </button>
                @if(count(array_filter($filters)) > 0)
                    <a href="{{ route('admin.leads.index') }}" class="p-2.5 bg-slate-100 hover:bg-slate-200 dark:bg-slate-700 dark:hover:bg-slate-650 text-slate-550 dark:text-slate-300 rounded-xl transition-all shrink-0" title="Reset Filters">
                        <i class="bi bi-arrow-counterclockwise"></i>
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Leads Table Grid -->
    <div class="bg-white dark:bg-slate-800 rounded-3xl border border-slate-100 dark:border-slate-700/50 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full border-collapse text-left">
                <thead>
                    <tr class="border-b border-slate-100 dark:border-slate-750 bg-slate-50/50 dark:bg-slate-850/20">
                        <th class="px-6 py-4 text-xs font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider">Lead Code</th>
                        <th class="px-6 py-4 text-xs font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider">Lead Type</th>
                        <th class="px-6 py-4 text-xs font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider">Contact Name</th>
                        <th class="px-6 py-4 text-xs font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider">Company</th>
                        <th class="px-6 py-4 text-xs font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider">Contact Details</th>
                        <th class="px-6 py-4 text-xs font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-4 text-xs font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider">Created Date</th>
                        <th class="px-6 py-4 text-xs font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-750">
                    @forelse($leads as $lead)
                        <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-850/5 transition-all text-xs text-slate-700 dark:text-slate-350">
                            <!-- Code -->
                            <td class="px-6 py-4 font-mono font-bold text-[#1155CC] dark:text-blue-400">
                                {{ $lead->lead_code }}
                            </td>

                            <!-- Lead Type badge -->
                            <td class="px-6 py-4">
                                @if($lead->lead_type === 'advertiser')
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 text-[10px] font-bold rounded-md bg-blue-50 text-[#1155CC] dark:bg-blue-950/20 dark:text-blue-400">
                                        <i class="bi bi-megaphone-fill"></i> Advertiser
                                    </span>
                                @elseif($lead->lead_type === 'location_partner')
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 text-[10px] font-bold rounded-md bg-emerald-50 text-emerald-700 dark:bg-emerald-950/20 dark:text-emerald-450">
                                        <i class="bi bi-building-fill"></i> Partner
                                    </span>
                                @elseif($lead->lead_type === 'digital_signage')
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 text-[10px] font-bold rounded-md bg-purple-50 text-purple-700 dark:bg-purple-950/20 dark:text-purple-400">
                                        <i class="bi bi-display"></i> Digital Signage
                                    </span>
                                @elseif($lead->lead_type === 'sales_partner')
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 text-[10px] font-bold rounded-md bg-orange-50 text-orange-700 dark:bg-orange-950/20 dark:text-orange-400">
                                        <i class="bi bi-briefcase"></i> Sales Partner
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 text-[10px] font-bold rounded-md bg-slate-100 text-slate-600 dark:bg-slate-900 dark:text-slate-400">
                                        <i class="bi bi-chat-left-dots-fill"></i> Contact
                                    </span>
                                @endif
                            </td>

                            <!-- Name -->
                            <td class="px-6 py-4 font-semibold text-slate-800 dark:text-slate-200">
                                {{ $lead->name }}
                            </td>

                            <!-- Company -->
                            <td class="px-6 py-4">
                                {{ $lead->company_name ?? '-' }}
                            </td>

                            <!-- Contact details -->
                            <td class="px-6 py-4">
                                <div class="font-medium">{{ $lead->email }}</div>
                                <div class="text-[10px] text-slate-400 font-mono mt-0.5">{{ $lead->phone }}</div>
                            </td>

                            <!-- Workflow status badge -->
                            <td class="px-6 py-4">
                                @if($lead->status === 'new')
                                    <span class="inline-flex items-center gap-1 text-[10px] font-bold bg-indigo-50 dark:bg-indigo-950/20 text-indigo-600 dark:text-indigo-400 border border-indigo-100 dark:border-indigo-900/30 px-2 py-0.5 rounded-full">
                                        <span class="w-1 h-1 bg-indigo-500 rounded-full animate-pulse"></span> New
                                    </span>
                                @elseif($lead->status === 'contacted')
                                    <span class="inline-flex items-center gap-1 text-[10px] font-bold bg-sky-50 dark:bg-sky-950/20 text-sky-600 dark:text-sky-400 border border-sky-100 dark:border-sky-900/30 px-2 py-0.5 rounded-full">
                                        <span class="w-1 h-1 bg-sky-500 rounded-full animate-pulse"></span> Contacted
                                    </span>
                                @elseif($lead->status === 'qualified')
                                    <span class="inline-flex items-center gap-1 text-[10px] font-bold bg-amber-50 dark:bg-amber-950/20 text-amber-600 dark:text-amber-450 border border-amber-100 dark:border-amber-900/30 px-2 py-0.5 rounded-full">
                                        <span class="w-1 h-1 bg-amber-500 rounded-full animate-pulse"></span> Qualified
                                    </span>
                                @elseif($lead->status === 'approved')
                                    <span class="inline-flex items-center gap-1 text-[10px] font-bold bg-emerald-50 dark:bg-emerald-950/20 text-emerald-600 dark:text-emerald-450 border border-emerald-100 dark:border-emerald-900/30 px-2 py-0.5 rounded-full">
                                        <span class="w-1.5 h-1.5 bg-emerald-500 rounded-full"></span> Approved
                                    </span>
                                @elseif($lead->status === 'rejected')
                                    <span class="inline-flex items-center gap-1 text-[10px] font-bold bg-red-50 dark:bg-red-950/20 text-red-600 dark:text-red-450 border border-red-100 dark:border-red-900/30 px-2 py-0.5 rounded-full">
                                        <span class="w-1.5 h-1.5 bg-red-500 rounded-full"></span> Rejected
                                    </span>
                                @endif
                            </td>

                            <!-- Created Date -->
                            <td class="px-6 py-4 font-mono text-[10px] text-slate-400">
                                {{ $lead->created_at->format('M d, Y h:i A') }}
                            </td>

                            <!-- Action button triggers -->
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-1.5">
                                    <a href="{{ route('admin.leads.show', $lead->id) }}" class="w-8 h-8 rounded-lg bg-blue-50/50 hover:bg-blue-100/50 text-[#1155CC] flex items-center justify-center transition-all border border-blue-100/20" title="Review Lead Details">
                                        <i class="bi bi-eye-fill"></i>
                                    </a>
                                    <form action="{{ route('admin.leads.destroy', $lead->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this lead?');" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="w-8 h-8 rounded-lg bg-rose-50/50 hover:bg-rose-100/50 text-rose-600 flex items-center justify-center transition-all border border-rose-100/20" title="Delete Lead">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-12 text-center text-slate-400 dark:text-slate-500">
                                <div class="max-w-xs mx-auto space-y-2 py-4">
                                    <i class="bi bi-people text-3xl text-slate-350 dark:text-slate-750"></i>
                                    <p class="font-bold text-slate-700 dark:text-slate-350">No lead enquiries found</p>
                                    <p class="text-[11px]">Enquiries from visitors will populate this index automatically.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination Links -->
        @if($leads->hasPages())
            <div class="px-6 py-4 border-t border-slate-100 dark:border-slate-750 bg-slate-50/30 dark:bg-slate-850/10">
                {{ $leads->appends($filters)->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
