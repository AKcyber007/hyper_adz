@extends('admin.layouts.app', [
    'title' => 'Review Lead | Hyper Adz Admin'
])

@section('content')
<div class="max-w-5xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.leads.index') }}" class="w-10 h-10 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 dark:bg-slate-800 dark:hover:bg-slate-700 dark:text-slate-300 flex items-center justify-center transition-all">
                <i class="bi bi-arrow-left text-lg"></i>
            </a>
            <div>
                <h1 class="text-xl font-bold tracking-tight text-slate-900 dark:text-white flex items-center gap-2">
                    Review Lead: {{ $lead->lead_code }}
                </h1>
                <p class="text-xs text-slate-500 mt-0.5">Track origin channel details, operational actions, and onboarding approvals.</p>
            </div>
        </div>
        
        <!-- Workflow Badges -->
        <div>
            @if($lead->status === 'new')
                <span class="px-3 py-1.5 text-xs font-bold bg-indigo-50 border border-indigo-150 text-indigo-700 dark:bg-indigo-950/20 dark:text-indigo-400 rounded-xl shadow-sm">NEW INCOMING</span>
            @elseif($lead->status === 'contacted')
                <span class="px-3 py-1.5 text-xs font-bold bg-sky-50 border border-sky-150 text-sky-700 dark:bg-sky-950/20 dark:text-sky-400 rounded-xl shadow-sm">CONTACTED</span>
            @elseif($lead->status === 'qualified')
                <span class="px-3 py-1.5 text-xs font-bold bg-amber-50 border border-amber-150 text-amber-700 dark:bg-amber-950/20 dark:text-amber-400 rounded-xl shadow-sm">QUALIFIED</span>
            @elseif($lead->status === 'approved')
                <span class="px-3 py-1.5 text-xs font-bold bg-emerald-50 border border-emerald-150 text-emerald-700 dark:bg-emerald-950/20 dark:text-emerald-450 rounded-xl shadow-sm">APPROVED</span>
            @elseif($lead->status === 'rejected')
                <span class="px-3 py-1.5 text-xs font-bold bg-red-50 border border-red-150 text-red-700 dark:bg-red-950/20 dark:text-red-400 rounded-xl shadow-sm">REJECTED</span>
            @endif
        </div>
    </div>

    <!-- Feedback Alerts -->
    @if(session('success'))
        <div class="p-4 bg-emerald-50 border border-emerald-100 dark:bg-emerald-950/10 dark:border-emerald-900/30 text-emerald-700 dark:text-emerald-400 rounded-2xl text-xs font-semibold flex items-center gap-2">
            <i class="bi bi-check-circle-fill"></i> {{ session('success') }}
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Left 2 Columns: Core Lead Info -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Information Card -->
            <div class="bg-white dark:bg-slate-800 rounded-3xl border border-slate-100 dark:border-slate-700/50 shadow-sm p-6 sm:p-8 space-y-6">
                <!-- Title -->
                <div>
                    <h3 class="text-sm font-bold text-slate-800 dark:text-slate-200 border-b border-slate-100 dark:border-slate-750 pb-2 mb-4">Contact Information</h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5 text-sm">
                        <div>
                            <span class="block text-[10px] uppercase font-bold text-slate-400">Full Name</span>
                            <span class="font-semibold text-slate-750 dark:text-slate-200">{{ $lead->name }}</span>
                        </div>
                        <div>
                            <span class="block text-[10px] uppercase font-bold text-slate-400">Company Name</span>
                            <span class="font-semibold text-slate-755 dark:text-slate-200">{{ $lead->company_name ?? 'Individual' }}</span>
                        </div>
                        <div>
                            <span class="block text-[10px] uppercase font-bold text-slate-400">Email Address</span>
                            <span class="font-mono text-slate-750 dark:text-slate-200">{{ $lead->email }}</span>
                        </div>
                        <div>
                            <span class="block text-[10px] uppercase font-bold text-slate-400">Phone Number</span>
                            <span class="font-mono text-slate-750 dark:text-slate-200">{{ $lead->phone }}</span>
                        </div>
                    </div>
                </div>

                <!-- Message / Details -->
                <div>
                    <h3 class="text-sm font-bold text-slate-800 dark:text-slate-200 border-b border-slate-100 dark:border-slate-750 pb-2 mb-3">Enquiry details / Message</h3>
                    <div class="p-4 bg-slate-50 dark:bg-slate-900 border border-slate-200/55 dark:border-slate-850 rounded-2xl text-xs text-slate-600 dark:text-slate-400 leading-relaxed font-sans min-h-[80px]">
                        {{ $lead->message ?? 'No message body provided.' }}
                    </div>
                </div>

                <!-- Internal remarks -->
                <div>
                    <h3 class="text-sm font-bold text-slate-800 dark:text-slate-200 border-b border-slate-100 dark:border-slate-750 pb-2 mb-3">Internal Admin Remarks (Notes)</h3>
                    <form method="POST" action="{{ route('admin.leads.remarks', $lead->id) }}" class="space-y-3">
                        @csrf
                        <textarea name="remarks" rows="3" placeholder="Append phone call logs, customer demands, or follow-up schedules..." class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-850 rounded-xl text-xs text-slate-700 dark:text-slate-200 focus:outline-none focus:border-[#1155CC] transition-all">{{ old('remarks', $lead->remarks) }}</textarea>
                        <div class="flex justify-end">
                            <button type="submit" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 dark:bg-slate-700 dark:hover:bg-slate-650 dark:text-slate-250 rounded-xl text-xs font-bold transition-all">
                                Save Remarks
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Audit History log list -->
            <div class="bg-white dark:bg-slate-800 rounded-3xl border border-slate-100 dark:border-slate-700/50 shadow-sm p-6 sm:p-8">
                <h3 class="text-sm font-bold text-slate-800 dark:text-slate-200 border-b border-slate-100 dark:border-slate-750 pb-2 mb-4"><i class="bi bi-clock-history"></i> Lead Status History</h3>
                
                <div class="flow-root">
                    <ul class="-mb-8">
                        @forelse($logs as $log)
                            <li>
                                <div class="relative pb-8">
                                    @if (!$loop->last)
                                        <span class="absolute top-4 left-4 -ml-px h-full w-0.5 bg-slate-200 dark:bg-slate-700" aria-hidden="true"></span>
                                    @endif
                                    <div class="relative flex space-x-3 text-xs">
                                        <div>
                                            <span class="h-8 w-8 rounded-full bg-slate-100 dark:bg-slate-900 flex items-center justify-center text-slate-500 font-bold border border-slate-250/30">
                                                {{ $log->user ? strtoupper(substr($log->user->name, 0, 1)) : 'S' }}
                                            </span>
                                        </div>
                                        <div class="flex-1 min-w-0 pt-1.5 flex justify-between space-x-4">
                                            <div>
                                                <p class="text-slate-600 dark:text-slate-350 font-semibold">{{ $log->description }}</p>
                                                <span class="text-[10px] text-slate-400 block mt-0.5">By: {{ $log->user->name ?? 'System Guest Form' }} ({{ $log->user->email ?? 'seeder@hyperadz.local' }})</span>
                                            </div>
                                            <div class="text-right text-[10px] whitespace-nowrap text-slate-400 font-mono">
                                                <time>{{ $log->created_at->format('M d, h:i A') }}</time>
                                                <span class="block text-[9px] font-sans mt-0.5 text-slate-500">{{ $log->created_at->diffForHumans() }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </li>
                        @empty
                            <p class="text-xs text-slate-450 italic py-2">No activity records logged.</p>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>

        <!-- Right 1 Column: Operations & Status Transitions -->
        <div class="space-y-6">
            <!-- Assignment Card -->
            <div class="bg-white dark:bg-slate-800 rounded-3xl border border-slate-100 dark:border-slate-700/50 shadow-sm p-6 space-y-4">
                <h4 class="text-xs font-bold text-slate-400 uppercase tracking-wider">Owner Assignment</h4>
                
                @if($lead->assignedAdmin)
                    <div class="flex items-center gap-3 p-3 bg-slate-50 dark:bg-slate-900 rounded-2xl border border-slate-200/50 dark:border-slate-850">
                        <div class="w-9 h-9 rounded-full bg-[#1155CC] text-white flex items-center justify-center font-bold text-sm shadow">
                            {{ strtoupper(substr($lead->assignedAdmin->name, 0, 1)) }}
                        </div>
                        <div>
                            <span class="block text-xs font-bold text-slate-800 dark:text-slate-200">{{ $lead->assignedAdmin->name }}</span>
                            <span class="block text-[10px] text-slate-450">{{ $lead->assignedAdmin->email }}</span>
                        </div>
                    </div>
                @else
                    <div class="p-3 bg-amber-50/40 dark:bg-amber-950/5 border border-amber-100 dark:border-amber-900/10 rounded-2xl text-center">
                        <span class="block text-xxs font-bold text-amber-600 dark:text-amber-450 uppercase mb-2">Unassigned Lead</span>
                        <form method="POST" action="{{ route('admin.leads.assign', $lead->id) }}">
                            @csrf
                            <button type="submit" class="w-full py-2 bg-[#1155CC] hover:bg-blue-700 text-white rounded-xl text-xs font-bold transition-all shadow-md shadow-blue-500/10">
                                Assign to Myself
                            </button>
                        </form>
                    </div>
                @endif
            </div>

            <!-- Workflow Stage update -->
            @if($lead->status !== 'approved' && $lead->status !== 'rejected')
                <div class="bg-white dark:bg-slate-800 rounded-3xl border border-slate-100 dark:border-slate-700/50 shadow-sm p-6 space-y-4">
                    <h4 class="text-xs font-bold text-slate-400 uppercase tracking-wider">Update Workflow Stage</h4>
                    <form method="POST" action="{{ route('admin.leads.updateStatus', $lead->id) }}" class="space-y-3">
                        @csrf
                        @method('PUT')
                        <select name="status" class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-850 rounded-xl text-xs text-slate-700 dark:text-slate-200 focus:outline-none focus:border-[#1155CC] transition-all">
                            <option value="new" {{ $lead->status === 'new' ? 'selected' : '' }}>New Inbox</option>
                            <option value="contacted" {{ $lead->status === 'contacted' ? 'selected' : '' }}>Contacted</option>
                            <option value="qualified" {{ $lead->status === 'qualified' ? 'selected' : '' }}>Qualified</option>
                        </select>
                        <button type="submit" class="w-full py-2 bg-slate-100 hover:bg-slate-200 text-slate-800 dark:bg-slate-700 dark:hover:bg-slate-650 dark:text-slate-250 rounded-xl text-xs font-bold transition-all">
                            Update Status
                        </button>
                    </form>
                </div>

                <!-- Approval Actions Workflow -->
                <div class="bg-white dark:bg-slate-800 rounded-3xl border border-slate-100 dark:border-slate-700/50 shadow-sm p-6 space-y-3">
                    <h4 class="text-xs font-bold text-slate-400 uppercase tracking-wider">Approval Actions</h4>
                    
                    <div class="grid grid-cols-2 gap-3">
                        <form method="POST" action="{{ route('admin.leads.approve', $lead->id) }}">
                            @csrf
                            <button type="submit" onclick="return confirm('Approve lead? (Marks as approved and prepares for User account creation).');" class="w-full py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-bold transition-all flex items-center justify-center gap-1.5 shadow-md shadow-emerald-500/10">
                                <i class="bi bi-patch-check"></i> Approve
                            </button>
                        </form>

                        <form method="POST" action="{{ route('admin.leads.reject', $lead->id) }}">
                            @csrf
                            <button type="submit" onclick="return confirm('Reject lead?');" class="w-full py-2.5 bg-rose-600 hover:bg-rose-700 text-white rounded-xl text-xs font-bold transition-all flex items-center justify-center gap-1.5 shadow-md shadow-rose-500/10">
                                <i class="bi bi-x-octagon"></i> Reject
                            </button>
                        </form>
                    </div>
                </div>
            @else
                <div class="bg-white dark:bg-slate-800 rounded-3xl border border-slate-100 dark:border-slate-700/50 shadow-sm p-6 text-center space-y-2">
                    <i class="bi bi-lock-fill text-2xl text-slate-400 block mb-1"></i>
                    <span class="block text-xs font-bold text-slate-500">Workflow Finalized</span>
                    <span class="block text-[10px] text-slate-450">This lead status is <strong>{{ strtoupper($lead->status) }}</strong>.</span>
                </div>

                @if($lead->lead_type === 'advertiser' && $lead->status === 'approved')
                    @php
                        $profile = \App\Models\AdvertiserProfile::where('lead_id', $lead->id)->first();
                    @endphp

                    @if($profile)
                        <div class="bg-white dark:bg-slate-800 rounded-3xl border border-slate-100 dark:border-slate-700/50 shadow-sm p-6 text-center space-y-2 mt-4">
                            <i class="bi bi-person-check-fill text-3xl text-emerald-500 block"></i>
                            <span class="block text-xs font-bold text-slate-850 dark:text-white">Converted Advertiser</span>
                            <span class="block text-[10px] text-slate-400">Registered profile:</span>
                            <a href="{{ route('admin.advertisers.show', $profile->id) }}" class="block px-3 py-2 bg-emerald-50 hover:bg-emerald-100 text-emerald-700 dark:bg-emerald-950/20 dark:text-emerald-400 rounded-xl text-xs font-bold font-mono transition-all">
                                {{ $profile->advertiser_code }}
                            </a>
                        </div>
                    @else
                        <div class="bg-white dark:bg-slate-800 rounded-3xl border border-slate-100 dark:border-slate-700/50 shadow-sm p-6 space-y-4 mt-4">
                            <h4 class="text-xs font-bold text-slate-400 uppercase tracking-wider flex items-center gap-1.5">
                                <i class="bi bi-person-plus text-blue-650"></i> Convert Lead
                            </h4>
                            <p class="text-[10px] text-slate-450 leading-relaxed">Convert this approved lead into an Advertiser Profile.</p>
                            
                            <form method="POST" action="{{ route('admin.advertisers.convert', $lead->id) }}" class="space-y-3">
                                @csrf
                                <div class="space-y-1">
                                    <label class="text-[9px] font-bold text-slate-450 uppercase block">Industry Sector <span class="text-red-500">*</span></label>
                                    <select name="industry_id" required class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-850 rounded-xl text-xs text-slate-700 dark:text-slate-200 focus:outline-none focus:border-[#1155CC] transition-all">
                                        <option value="">Select Industry</option>
                                        @foreach(\App\Models\Industry::where('status', 'active')->orderBy('name')->get() as $ind)
                                            <option value="{{ $ind->id }}">{{ $ind->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="grid grid-cols-2 gap-2 text-xxs">
                                    <div class="space-y-1">
                                        <label class="font-bold text-slate-450 block">GST Number</label>
                                        <input type="text" name="gst_number" placeholder="GSTN" class="w-full px-2.5 py-1.5 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-850 rounded-lg text-xs text-slate-700 focus:outline-none transition-all">
                                    </div>
                                    <div class="space-y-1">
                                        <label class="font-bold text-slate-450 block">Website</label>
                                        <input type="text" name="website" placeholder="www.website.com" class="w-full px-2.5 py-1.5 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-850 rounded-lg text-xs text-slate-700 focus:outline-none transition-all">
                                    </div>
                                </div>

                                <button type="submit" class="w-full py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-xs font-bold transition-all shadow-md shadow-blue-500/10 flex items-center justify-center gap-1.5">
                                    <i class="bi bi-person-check-fill"></i> Convert Profile
                                </button>
                            </form>
                        </div>
                    @endif
                @endif

                @if($lead->lead_type === 'location_partner' && $lead->status === 'approved')
                    @php
                        $partnerProfile = \App\Models\LocationPartnerProfile::where('lead_id', $lead->id)->first();
                    @endphp

                    @if($partnerProfile)
                        <div class="bg-white dark:bg-slate-800 rounded-3xl border border-slate-100 dark:border-slate-700/50 shadow-sm p-6 text-center space-y-2 mt-4">
                            <i class="bi bi-building-check text-3xl text-emerald-500 block"></i>
                            <span class="block text-xs font-bold text-slate-850 dark:text-white">Converted Partner</span>
                            <span class="block text-[10px] text-slate-400">Registered partner:</span>
                            <a href="{{ route('admin.location-partners.show', $partnerProfile->id) }}" class="block px-3 py-2 bg-emerald-50 hover:bg-emerald-100 text-emerald-700 dark:bg-emerald-950/20 dark:text-emerald-450 rounded-xl text-xs font-bold font-mono transition-all">
                                {{ $partnerProfile->partner_code }}
                            </a>
                        </div>
                    @else
                        <div class="bg-white dark:bg-slate-800 rounded-3xl border border-slate-100 dark:border-slate-700/50 shadow-sm p-6 space-y-4 mt-4">
                            <h4 class="text-xs font-bold text-slate-400 uppercase tracking-wider flex items-center gap-1.5">
                                <i class="bi bi-person-plus text-blue-655"></i> Convert Lead
                            </h4>
                            <p class="text-[10px] text-slate-450 leading-relaxed">Convert this approved lead into a Location Partner Profile.</p>
                            
                            <form method="POST" action="{{ route('admin.location-partners.convert', $lead->id) }}" class="space-y-3">
                                @csrf
                                <div class="space-y-1">
                                    <label class="text-[9px] font-bold text-slate-450 uppercase block">Designation</label>
                                    <input type="text" name="designation" placeholder="e.g. Managing Director" class="w-full px-2.5 py-1.5 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-850 rounded-lg text-xs text-slate-700 focus:outline-none transition-all">
                                </div>

                                <div class="grid grid-cols-2 gap-2 text-xxs">
                                    <div class="space-y-1">
                                        <label class="font-bold text-slate-450 block">GST Number</label>
                                        <input type="text" name="gst_number" placeholder="GSTN" class="w-full px-2.5 py-1.5 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-850 rounded-lg text-xs text-slate-700 focus:outline-none transition-all">
                                    </div>
                                    <div class="space-y-1">
                                        <label class="font-bold text-slate-450 block">Website</label>
                                        <input type="text" name="website" placeholder="www.website.com" class="w-full px-2.5 py-1.5 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-850 rounded-lg text-xs text-slate-700 focus:outline-none transition-all">
                                    </div>
                                </div>

                                <button type="submit" class="w-full py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-xs font-bold transition-all shadow-md shadow-blue-500/10 flex items-center justify-center gap-1.5">
                                    <i class="bi bi-person-check-fill"></i> Convert Profile
                                </button>
                            </form>
                        </div>
                    @endif
                @endif
            @endif
        </div>
    </div>
</div>
@endsection
