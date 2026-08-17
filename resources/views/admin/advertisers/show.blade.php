@extends('admin.layouts.app', [
    'title' => 'Advertiser Details | Hyper Adz Admin'
])

@section('content')
<div class="space-y-6">
    <!-- Header Back Navigation -->
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.advertisers.index') }}" class="w-10 h-10 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 dark:bg-slate-800 dark:hover:bg-slate-700 dark:text-slate-300 flex items-center justify-center transition-all">
                <i class="bi bi-arrow-left text-lg"></i>
            </a>
            <div>
                <h1 class="text-xl font-bold tracking-tight text-slate-900 dark:text-white">{{ $advertiser->company_name }}</h1>
                <p class="text-xs text-slate-550 dark:text-slate-400 mt-0.5">Corporate profile and compliance details review.</p>
            </div>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.advertisers.campaigns', $advertiser->id) }}" class="px-4 py-2 bg-[#1155CC] hover:bg-blue-700 text-white rounded-xl text-xs font-bold transition-all shadow-sm flex items-center gap-1.5">
                <i class="bi bi-play-circle-fill"></i> View Campaigns
            </a>
            <a href="{{ route('admin.advertisers.edit', $advertiser->id) }}" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 dark:bg-slate-700 dark:hover:bg-slate-650 text-slate-800 dark:text-slate-200 rounded-xl text-xs font-bold transition-all border border-slate-200/50 dark:border-slate-800 flex items-center gap-1.5">
                <i class="bi bi-pencil-square"></i> Edit Profile
            </a>
        </div>
    </div>

    <!-- Alert Notifications -->
    @if(session('success'))
        <div class="p-4 bg-emerald-50 border border-emerald-100 dark:bg-emerald-950/10 dark:border-emerald-900/30 text-emerald-700 dark:text-emerald-400 rounded-2xl text-xs font-semibold flex items-center gap-2">
            <i class="bi bi-check-circle-fill"></i> {{ session('success') }}
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Left: Main Information Card -->
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white dark:bg-slate-800 rounded-3xl border border-slate-100 dark:border-slate-700/50 shadow-sm p-6 sm:p-8 space-y-6">
                <!-- Top Header Row -->
                <div class="flex items-start gap-4 pb-6 border-b border-slate-100 dark:border-slate-750">
                    <div class="w-20 h-20 rounded-2xl overflow-hidden border border-slate-200/80 dark:border-slate-700 bg-slate-50 dark:bg-slate-900 flex items-center justify-center shadow-inner shrink-0">
                        @if($advertiser->logo_path)
                            <img src="{{ Storage::url($advertiser->logo_path) }}" class="w-full h-full object-cover">
                        @else
                            <i class="bi bi-building text-slate-400 text-3xl"></i>
                        @endif
                    </div>
                    <div class="space-y-1.5">
                        <div class="flex items-center gap-2">
                            <span class="px-2.5 py-0.5 bg-[#1155CC]/10 text-[#1155CC] dark:bg-blue-950/20 dark:text-blue-400 rounded-full font-mono text-[10px] font-bold">
                                {{ $advertiser->advertiser_code }}
                            </span>
                            @if($advertiser->status === 'active')
                                <span class="inline-flex items-center gap-1 text-[10px] font-bold bg-emerald-50 dark:bg-emerald-950/20 text-emerald-600 dark:text-emerald-450 border border-emerald-100 dark:border-emerald-900/30 px-2 py-0.5 rounded-full">
                                    Active
                                </span>
                            @elseif($advertiser->status === 'suspended')
                                <span class="inline-flex items-center gap-1 text-[10px] font-bold bg-amber-50 dark:bg-amber-950/20 text-amber-600 dark:text-amber-450 border border-amber-100 dark:border-amber-900/30 px-2 py-0.5 rounded-full">
                                    Suspended
                                </span>
                            @elseif($advertiser->status === 'inactive')
                                <span class="inline-flex items-center gap-1 text-[10px] font-bold bg-slate-100 dark:bg-slate-900 text-slate-500 dark:text-slate-400 border border-slate-200 dark:border-slate-800 px-2 py-0.5 rounded-full">
                                    Inactive
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 text-[10px] font-bold bg-indigo-50 dark:bg-indigo-950/20 text-indigo-650 dark:text-indigo-400 border border-indigo-100 dark:border-indigo-900/30 px-2 py-0.5 rounded-full">
                                    Pending Approval
                                </span>
                            @endif
                        </div>
                        <h2 class="text-xl font-bold text-slate-850 dark:text-white">{{ $advertiser->company_name }}</h2>
                        <p class="text-xs text-slate-400 font-semibold">{{ $advertiser->industry->name ?? 'No Industry Class' }}</p>
                    </div>
                </div>

                <!-- Technical Properties Grid -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 text-xs">
                    <!-- Contact Person -->
                    <div class="space-y-1">
                        <span class="block text-[10px] font-bold text-slate-450 uppercase tracking-wider">Contact Person</span>
                        <span class="block font-semibold text-slate-800 dark:text-slate-200">{{ $advertiser->contact_person }}</span>
                    </div>

                    <!-- GST Number -->
                    <div class="space-y-1">
                        <span class="block text-[10px] font-bold text-slate-450 uppercase tracking-wider">GST Number</span>
                        <span class="block font-semibold text-slate-800 dark:text-slate-200 font-mono">{{ $advertiser->gst_number ?: 'Not Registered' }}</span>
                    </div>

                    <!-- Email -->
                    <div class="space-y-1">
                        <span class="block text-[10px] font-bold text-slate-450 uppercase tracking-wider">Email Address</span>
                        <a href="mailto:{{ $advertiser->email }}" class="block font-semibold text-[#1155CC] hover:underline">{{ $advertiser->email }}</a>
                    </div>

                    <!-- Phone -->
                    <div class="space-y-1">
                        <span class="block text-[10px] font-bold text-slate-450 uppercase tracking-wider">Phone Number</span>
                        <span class="block font-semibold text-slate-800 dark:text-slate-200 font-mono">{{ $advertiser->phone }}</span>
                    </div>

                    <!-- Website -->
                    <div class="space-y-1">
                        <span class="block text-[10px] font-bold text-slate-450 uppercase tracking-wider">Website</span>
                        @if($advertiser->website)
                            <a href="{{ Str::startsWith($advertiser->website, 'http') ? $advertiser->website : 'https://' . $advertiser->website }}" target="_blank" class="block font-semibold text-[#1155CC] hover:underline flex items-center gap-1">
                                {{ $advertiser->website }} <i class="bi bi-box-arrow-up-right text-[10px]"></i>
                            </a>
                        @else
                            <span class="block text-slate-400">None</span>
                        @endif
                    </div>

                    <!-- Created Date -->
                    <div class="space-y-1">
                        <span class="block text-[10px] font-bold text-slate-450 uppercase tracking-wider">Registered Since</span>
                        <span class="block font-semibold text-slate-800 dark:text-slate-200">{{ $advertiser->created_at->format('M d, Y h:i A') }}</span>
                    </div>
                </div>

                <!-- Office Address Details -->
                <div class="space-y-2 border-t border-slate-100 dark:border-slate-750 pt-6">
                    <span class="block text-[10px] font-bold text-slate-450 uppercase tracking-wider">Office Address</span>
                    <div class="p-4 bg-slate-50 dark:bg-slate-900 rounded-2xl text-xs text-slate-750 dark:text-slate-300 leading-relaxed">
                        @if($advertiser->address_line_1)
                            <p class="font-semibold">{{ $advertiser->address_line_1 }}</p>
                            @if($advertiser->address_line_2)<p>{{ $advertiser->address_line_2 }}</p>@endif
                            <p class="mt-1">
                                {{ implode(', ', array_filter([$advertiser->city, $advertiser->state, $advertiser->postal_code, $advertiser->country])) }}
                            </p>
                        @else
                            <span class="text-slate-400 italic">No address registered.</span>
                        @endif
                    </div>
                </div>

                <!-- Notes / Admin Remarks -->
                <div class="space-y-2 border-t border-slate-100 dark:border-slate-750 pt-6">
                    <span class="block text-[10px] font-bold text-slate-450 uppercase tracking-wider">Internal Administrative Remarks</span>
                    <div class="p-4 bg-slate-50 dark:bg-slate-900 rounded-2xl text-xs text-slate-750 dark:text-slate-350 leading-relaxed italic whitespace-pre-line border-l-4 border-slate-300 dark:border-slate-700">
                        {{ $advertiser->notes ?: 'No administrative remarks recorded.' }}
                    </div>
                </div>
            </div>
        </div>

        <!-- Right: Status Settings, Lead Map, and Logs -->
        <div class="space-y-6">
            <!-- Status Update Action Cards -->
            <div class="bg-white dark:bg-slate-800 rounded-3xl border border-slate-100 dark:border-slate-700/50 shadow-sm p-6 space-y-4">
                <h4 class="text-xs font-bold text-slate-400 uppercase tracking-wider">Account Status Management</h4>
                
                <div class="space-y-2.5">
                    @if($advertiser->status !== 'active')
                        <form method="POST" action="{{ route('admin.advertisers.updateStatus', $advertiser->id) }}">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="status" value="active">
                            <button type="submit" class="w-full py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-bold transition-all shadow-md shadow-emerald-500/10 flex items-center justify-center gap-1.5">
                                <i class="bi bi-shield-check"></i> Activate Advertiser Profile
                            </button>
                        </form>
                    @endif

                    @if($advertiser->status !== 'suspended')
                        <form method="POST" action="{{ route('admin.advertisers.updateStatus', $advertiser->id) }}">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="status" value="suspended">
                            <button type="submit" onclick="return confirm('Suspend this advertiser? All campaigns will be paused.');" class="w-full py-2.5 bg-amber-500 hover:bg-amber-600 text-white rounded-xl text-xs font-bold transition-all shadow-md shadow-amber-500/10 flex items-center justify-center gap-1.5">
                                <i class="bi bi-shield-slash"></i> Suspend Advertiser Profile
                            </button>
                        </form>
                    @endif
                </div>

                @if($advertiser->approvedByUser)
                    <div class="pt-3 border-t border-slate-100 dark:border-slate-750 text-xxs text-slate-400 space-y-1">
                        <span class="block">Approved by: <strong>{{ $advertiser->approvedByUser->name }}</strong></span>
                        <span class="block">Approved at: {{ $advertiser->approved_at->format('M d, Y') }}</span>
                    </div>
                @endif
            </div>

            <!-- Login Statistics Card -->
            <div class="bg-white dark:bg-slate-800 rounded-3xl border border-slate-100 dark:border-slate-700/50 shadow-sm p-6 space-y-4">
                <h4 class="text-xs font-bold text-slate-400 uppercase tracking-wider flex items-center gap-1.5">
                    <i class="bi bi-shield-lock-fill text-[#1155CC]"></i> Login Activity
                </h4>
                <div class="grid grid-cols-2 gap-3">
                    <div class="bg-slate-50 dark:bg-slate-900 rounded-2xl border border-slate-200/50 dark:border-slate-850 p-3 space-y-1 text-center">
                        <span class="block text-[9px] font-bold text-slate-400 uppercase tracking-wider">Login Count</span>
                        <span class="block text-xl font-extrabold text-slate-800 dark:text-white">{{ number_format($advertiser->login_count ?? 0) }}</span>
                    </div>
                    <div class="bg-slate-50 dark:bg-slate-900 rounded-2xl border border-slate-200/50 dark:border-slate-850 p-3 space-y-1 text-center">
                        <span class="block text-[9px] font-bold text-slate-400 uppercase tracking-wider">Last Login</span>
                        <span class="block text-xs font-bold text-slate-800 dark:text-white">
                            {{ $advertiser->last_login_at ? $advertiser->last_login_at->diffForHumans() : '—' }}
                        </span>
                        @if($advertiser->last_login_at)
                            <span class="block text-[9px] text-slate-400">{{ $advertiser->last_login_at->format('d M Y, H:i') }}</span>
                        @endif
                    </div>
                </div>
                @if(!$advertiser->last_login_at)
                    <p class="text-xxs text-slate-400 italic text-center">This advertiser has not logged in yet.</p>
                @endif
            </div>

            <!-- Lead Mapping Card -->
            @if($advertiser->lead)
                <div class="bg-white dark:bg-slate-800 rounded-3xl border border-slate-100 dark:border-slate-700/50 shadow-sm p-6 space-y-3">
                    <h4 class="text-xs font-bold text-slate-400 uppercase tracking-wider">Converted Lead Link</h4>
                    <p class="text-xxs text-slate-450 leading-relaxed">This profile was generated from a public onboarding form submission.</p>
                    <a href="{{ route('admin.leads.show', $advertiser->lead_id) }}" class="flex items-center justify-between p-3 bg-slate-50 dark:bg-slate-900 rounded-2xl border border-slate-200/50 dark:border-slate-850 hover:bg-slate-100 transition-all">
                        <div class="space-y-0.5">
                            <span class="block text-xs font-bold text-[#1155CC]">{{ $advertiser->lead->lead_code }}</span>
                            <span class="block text-[10px] text-slate-400">Source: {{ strtoupper($advertiser->lead->source) }}</span>
                        </div>
                        <i class="bi bi-chevron-right text-slate-400"></i>
                    </a>
                </div>
            @endif

            <!-- Chronological Activity History -->
            <div class="bg-white dark:bg-slate-800 rounded-3xl border border-slate-100 dark:border-slate-700/50 shadow-sm p-6 space-y-4">
                <h4 class="text-xs font-bold text-slate-400 uppercase tracking-wider">Activity History Logs</h4>
                
                <div class="relative border-l-2 border-slate-100 dark:border-slate-750 ml-2.5 pl-4 space-y-5">
                    @forelse($logs as $log)
                        <div class="relative text-xxs">
                            <!-- Bullet -->
                            <span class="absolute -left-[23px] top-0.5 w-2.5 h-2.5 rounded-full bg-slate-300 dark:bg-slate-700 border-2 border-white dark:border-slate-800"></span>
                            
                            <div class="font-bold text-slate-700 dark:text-slate-300">{{ $log->description }}</div>
                            <div class="text-[9px] text-slate-400 mt-0.5">
                                By {{ $log->user->name ?? 'System' }} &bull; {{ $log->created_at->diffForHumans() }}
                            </div>
                        </div>
                    @empty
                        <div class="text-xxs text-slate-400 py-2">No activities recorded.</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
