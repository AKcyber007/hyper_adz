@extends('admin.layouts.app', [
    'title' => 'Location Partner Details | Hyper Adz Admin'
])

@section('content')
<div class="space-y-6">
    <!-- Header Back Navigation -->
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.location-partners.index') }}" class="w-10 h-10 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 dark:bg-slate-800 dark:hover:bg-slate-700 dark:text-slate-300 flex items-center justify-center transition-all">
                <i class="bi bi-arrow-left text-lg"></i>
            </a>
            <div>
                <h1 class="text-xl font-bold tracking-tight text-slate-900 dark:text-white">{{ $partner->company_name }}</h1>
                <p class="text-xs text-slate-550 dark:text-slate-400 mt-0.5">Corporate partner details and venue inventory assignment.</p>
            </div>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.location-partners.edit', $partner->id) }}" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 dark:bg-slate-700 dark:hover:bg-slate-650 text-slate-805 dark:text-slate-200 rounded-xl text-xs font-bold transition-all border border-slate-200/50 dark:border-slate-800 flex items-center gap-1.5">
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
        <!-- Left: Partner Details & Inventory Table -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Profile Card -->
            <div class="bg-white dark:bg-slate-800 rounded-3xl border border-slate-100 dark:border-slate-700/50 shadow-sm p-6 sm:p-8 space-y-6">
                <div class="flex items-start gap-4 pb-6 border-b border-slate-100 dark:border-slate-750">
                    <div class="w-20 h-20 rounded-2xl overflow-hidden border border-slate-200/80 dark:border-slate-700 bg-slate-50 dark:bg-slate-900 flex items-center justify-center shadow-inner shrink-0">
                        @if($partner->logo_path)
                            <img src="{{ Storage::url($partner->logo_path) }}" class="w-full h-full object-cover">
                        @else
                            <i class="bi bi-building text-slate-400 text-3xl"></i>
                        @endif
                    </div>
                    <div class="space-y-1.5">
                        <div class="flex items-center gap-2">
                            <span class="px-2.5 py-0.5 bg-[#1155CC]/10 text-[#1155CC] dark:bg-blue-950/20 dark:text-blue-400 rounded-full font-mono text-[10px] font-bold">
                                {{ $partner->partner_code }}
                            </span>
                            @if($partner->status === 'active')
                                <span class="inline-flex items-center gap-1 text-[10px] font-bold bg-emerald-50 dark:bg-emerald-950/20 text-emerald-600 dark:text-emerald-450 border border-emerald-100 dark:border-emerald-900/30 px-2 py-0.5 rounded-full">
                                    Active
                                </span>
                            @elseif($partner->status === 'suspended')
                                <span class="inline-flex items-center gap-1 text-[10px] font-bold bg-amber-50 dark:bg-amber-950/20 text-amber-600 dark:text-amber-450 border border-amber-100 dark:border-amber-900/30 px-2 py-0.5 rounded-full">
                                    Suspended
                                </span>
                            @elseif($partner->status === 'inactive')
                                <span class="inline-flex items-center gap-1 text-[10px] font-bold bg-slate-100 dark:bg-slate-900 text-slate-500 dark:text-slate-400 border border-slate-200 dark:border-slate-800 px-2 py-0.5 rounded-full">
                                    Inactive
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 text-[10px] font-bold bg-indigo-50 dark:bg-indigo-950/20 text-indigo-650 dark:text-indigo-400 border border-indigo-100 dark:border-indigo-900/30 px-2 py-0.5 rounded-full">
                                    Pending Approval
                                </span>
                            @endif
                        </div>
                        <h2 class="text-xl font-bold text-slate-850 dark:text-white">{{ $partner->company_name }}</h2>
                        <p class="text-xs text-slate-450 font-semibold">{{ $partner->contact_person }} @if($partner->designation) ({{ $partner->designation }}) @endif</p>
                    </div>
                </div>

                <!-- Metrics Grid -->
                <div class="grid grid-cols-3 gap-4 py-3 bg-slate-50 dark:bg-slate-900/40 rounded-2xl border border-slate-150/40 dark:border-slate-850/30 text-center text-xs">
                    <div>
                        <span class="block text-[9px] font-bold text-slate-405 dark:text-slate-550 uppercase tracking-wider">Locations</span>
                        <span class="block text-base font-bold text-[#1155CC] dark:text-blue-400 mt-0.5">{{ $partner->locations_count }}</span>
                    </div>
                    <div class="border-x border-slate-200/60 dark:border-slate-800">
                        <span class="block text-[9px] font-bold text-slate-405 dark:text-slate-550 uppercase tracking-wider">Screens</span>
                        <span class="block text-base font-bold text-slate-800 dark:text-white mt-0.5">{{ $partner->screens_count }}</span>
                    </div>
                    <div>
                        <span class="block text-[9px] font-bold text-slate-405 dark:text-slate-550 uppercase tracking-wider">Daily Impressions</span>
                        <span class="block text-base font-bold text-emerald-600 dark:text-emerald-450 mt-0.5">{{ number_format($partner->total_impressions) }}</span>
                    </div>
                </div>

                <!-- Properties Grid -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 text-xs border-t border-slate-100 dark:border-slate-750 pt-6">
                    <div class="space-y-1">
                        <span class="block text-[10px] font-bold text-slate-405 uppercase tracking-wider">Email Address</span>
                        <a href="mailto:{{ $partner->email }}" class="block font-semibold text-[#1155CC] hover:underline">{{ $partner->email }}</a>
                    </div>
                    <div class="space-y-1">
                        <span class="block text-[10px] font-bold text-slate-405 uppercase tracking-wider">Phone Number</span>
                        <span class="block font-semibold text-slate-800 dark:text-slate-200 font-mono">{{ $partner->phone }}</span>
                    </div>
                    <div class="space-y-1">
                        <span class="block text-[10px] font-bold text-slate-405 uppercase tracking-wider">GST Number</span>
                        <span class="block font-semibold text-slate-800 dark:text-slate-200 font-mono">{{ $partner->gst_number ?: 'Not Registered' }}</span>
                    </div>
                    <div class="space-y-1">
                        <span class="block text-[10px] font-bold text-slate-405 uppercase tracking-wider">Website</span>
                        @if($partner->website)
                            <a href="{{ Str::startsWith($partner->website, 'http') ? $partner->website : 'https://' . $partner->website }}" target="_blank" class="block font-semibold text-[#1155CC] hover:underline flex items-center gap-1">
                                {{ $partner->website }} <i class="bi bi-box-arrow-up-right text-[10px]"></i>
                            </a>
                        @else
                            <span class="block text-slate-400">None</span>
                        @endif
                    </div>
                </div>

                <!-- Address -->
                <div class="space-y-2 border-t border-slate-100 dark:border-slate-750 pt-6">
                    <span class="block text-[10px] font-bold text-slate-405 uppercase tracking-wider">Corporate Address</span>
                    <div class="p-4 bg-slate-50 dark:bg-slate-900 rounded-2xl text-xs text-slate-700 dark:text-slate-300 leading-relaxed">
                        @if($partner->address_line_1)
                            <p class="font-semibold">{{ $partner->address_line_1 }}</p>
                            @if($partner->address_line_2)<p>{{ $partner->address_line_2 }}</p>@endif
                            <p class="mt-1">
                                {{ implode(', ', array_filter([$partner->city, $partner->state, $partner->postal_code, $partner->country])) }}
                            </p>
                        @else
                            <span class="text-slate-400 italic">No address registered.</span>
                        @endif
                    </div>
                </div>

                <!-- Remarks -->
                <div class="space-y-2 border-t border-slate-100 dark:border-slate-750 pt-6">
                    <span class="block text-[10px] font-bold text-slate-405 uppercase tracking-wider">Internal Remarks Notes</span>
                    <div class="p-4 bg-slate-50 dark:bg-slate-900 rounded-2xl text-xs text-slate-700 dark:text-slate-350 italic whitespace-pre-line border-l-4 border-slate-300 dark:border-slate-700">
                        {{ $partner->notes ?: 'No administrative remarks recorded.' }}
                    </div>
                </div>
            </div>

            <!-- Assigned Locations Table -->
            <div class="bg-white dark:bg-slate-800 rounded-3xl border border-slate-100 dark:border-slate-700/50 shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-100 dark:border-slate-750">
                    <h3 class="text-sm font-bold text-slate-800 dark:text-slate-200">Assigned Venue Inventory</h3>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full border-collapse text-left">
                        <thead>
                            <tr class="border-b border-slate-100 dark:border-slate-750 bg-slate-50/50 dark:bg-slate-850/20">
                                <th class="px-6 py-4 text-xs font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider">Location Code</th>
                                <th class="px-6 py-4 text-xs font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider">Name</th>
                                <th class="px-6 py-4 text-xs font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider">City</th>
                                <th class="px-6 py-4 text-xs font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider">Screens count</th>
                                <th class="px-6 py-4 text-xs font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-750">
                            @forelse($partner->locations as $loc)
                                <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-850/5 transition-all text-xs text-slate-700 dark:text-slate-350">
                                    <td class="px-6 py-4 font-mono font-bold text-[#1155CC] dark:text-blue-400">
                                        {{ $loc->location_code }}
                                    </td>
                                    <td class="px-6 py-4 font-bold text-slate-800 dark:text-slate-200">
                                        {{ $loc->name }}
                                    </td>
                                    <td class="px-6 py-4 font-medium">
                                        {{ $loc->city }}
                                    </td>
                                    <td class="px-6 py-4 font-bold">
                                        {{ $loc->screens()->count() }}
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <form action="{{ route('admin.location-partners.locations.remove', $loc->id) }}" method="POST" onsubmit="return confirm('Remove location assignment?');" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="w-8 h-8 rounded-lg bg-rose-50/50 hover:bg-rose-100/50 text-rose-600 flex items-center justify-center transition-all border border-rose-100/20" title="Remove Assignment">
                                                <i class="bi bi-x-lg"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-12 text-center text-slate-400">
                                        <i class="bi bi-geo-alt text-2xl block mb-2 opacity-35"></i>
                                        <span>No locations assigned to this partner yet.</span>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Right Column: Status & Assignment Controls -->
        <div class="space-y-6">
            <!-- Account Status Management -->
            <div class="bg-white dark:bg-slate-800 rounded-3xl border border-slate-100 dark:border-slate-700/50 shadow-sm p-6 space-y-4">
                <h4 class="text-xs font-bold text-slate-400 uppercase tracking-wider">Account Status Management</h4>
                
                <div class="space-y-2.5">
                    @if($partner->status !== 'active')
                        <form method="POST" action="{{ route('admin.location-partners.updateStatus', $partner->id) }}">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="status" value="active">
                            <button type="submit" class="w-full py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-bold transition-all shadow-md shadow-emerald-500/10 flex items-center justify-center gap-1.5">
                                <i class="bi bi-shield-check"></i> Activate Partner Profile
                            </button>
                        </form>
                    @endif

                    @if($partner->status !== 'suspended')
                        <form method="POST" action="{{ route('admin.location-partners.updateStatus', $partner->id) }}">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="status" value="suspended">
                            <button type="submit" onclick="return confirm('Suspend this partner? All screens under their locations will set status accordingly.');" class="w-full py-2.5 bg-amber-500 hover:bg-amber-600 text-white rounded-xl text-xs font-bold transition-all shadow-md shadow-amber-500/10 flex items-center justify-center gap-1.5">
                                <i class="bi bi-shield-slash"></i> Suspend Partner Profile
                            </button>
                        </form>
                    @endif
                </div>

                @if($partner->approvedByUser)
                    <div class="pt-3 border-t border-slate-100 dark:border-slate-750 text-xxs text-slate-405 space-y-1">
                        <span class="block">Approved by: <strong>{{ $partner->approvedByUser->name }}</strong></span>
                        <span class="block">Approved at: {{ $partner->approved_at->format('M d, Y') }}</span>
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
                        <span class="block text-xl font-extrabold text-slate-800 dark:text-white">{{ number_format($partner->login_count ?? 0) }}</span>
                    </div>
                    <div class="bg-slate-50 dark:bg-slate-900 rounded-2xl border border-slate-200/50 dark:border-slate-850 p-3 space-y-1 text-center">
                        <span class="block text-[9px] font-bold text-slate-400 uppercase tracking-wider">Last Login</span>
                        <span class="block text-xs font-bold text-slate-800 dark:text-white">
                            {{ $partner->last_login_at ? $partner->last_login_at->diffForHumans() : '—' }}
                        </span>
                        @if($partner->last_login_at)
                            <span class="block text-[9px] text-slate-400">{{ $partner->last_login_at->format('d M Y, H:i') }}</span>
                        @endif
                    </div>
                </div>
                @if(!$partner->last_login_at)
                    <p class="text-xxs text-slate-400 italic text-center">This partner has not logged in yet.</p>
                @endif
            </div>

            <!-- Lead Mapping Link -->
            @if($partner->lead)
                <div class="bg-white dark:bg-slate-800 rounded-3xl border border-slate-100 dark:border-slate-700/50 shadow-sm p-6 space-y-3">
                    <h4 class="text-xs font-bold text-slate-400 uppercase tracking-wider">Converted Lead Link</h4>
                    <p class="text-xxs text-slate-450 leading-relaxed">This profile was generated from a Become a Partner onboarding lead.</p>
                    <a href="{{ route('admin.leads.show', $partner->lead_id) }}" class="flex items-center justify-between p-3 bg-slate-50 dark:bg-slate-900 rounded-2xl border border-slate-200/50 dark:border-slate-850 hover:bg-slate-100 transition-all">
                        <div class="space-y-0.5">
                            <span class="block text-xs font-bold text-[#1155CC]">{{ $partner->lead->lead_code }}</span>
                            <span class="block text-[10px] text-slate-400">Source: {{ strtoupper($partner->lead->source) }}</span>
                        </div>
                        <i class="bi bi-chevron-right text-slate-400"></i>
                    </a>
                </div>
            @endif

            <!-- Assign Locations Action -->
            <div class="bg-white dark:bg-slate-800 rounded-3xl border border-slate-100 dark:border-slate-700/50 shadow-sm p-6 space-y-4">
                <h4 class="text-xs font-bold text-slate-400 uppercase tracking-wider flex items-center gap-1.5">
                    <i class="bi bi-geo-alt-fill text-blue-600"></i> Assign Locations
                </h4>
                
                @if(count($unassignedLocations) > 0)
                    <form method="POST" action="{{ route('admin.location-partners.locations.assign', $partner->id) }}" class="space-y-3">
                        @csrf
                        <div class="space-y-1">
                            <label class="text-[9px] font-bold text-slate-450 uppercase block">Select Unassigned Locations</label>
                            <select name="location_ids[]" multiple required class="w-full h-32 px-3 py-2 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-850 rounded-xl text-xs text-slate-700 dark:text-slate-200 focus:outline-none focus:border-[#1155CC] transition-all">
                                @foreach($unassignedLocations as $uloc)
                                    <option value="{{ $uloc->id }}">{{ $uloc->name }} ({{ $uloc->city }})</option>
                                @endforeach
                            </select>
                            <span class="block text-[9px] text-slate-400">Hold Ctrl or Cmd key to select multiple venues.</span>
                        </div>

                        <button type="submit" class="w-full py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-xs font-bold transition-all shadow-md shadow-blue-500/10 flex items-center justify-center gap-1">
                            <i class="bi bi-plus-lg"></i> Link Selected Locations
                        </button>
                    </form>
                @else
                    <div class="p-4 bg-slate-50 dark:bg-slate-900 rounded-2xl text-center text-xxs text-slate-400">
                        All locations are already assigned. Create new locations first.
                    </div>
                @endif
            </div>

            <!-- Chronological Activity Logs -->
            <div class="bg-white dark:bg-slate-800 rounded-3xl border border-slate-100 dark:border-slate-700/50 shadow-sm p-6 space-y-4">
                <h4 class="text-xs font-bold text-slate-400 uppercase tracking-wider">Activity History Logs</h4>
                
                <div class="relative border-l-2 border-slate-100 dark:border-slate-750 ml-2.5 pl-4 space-y-5">
                    @forelse($logs as $log)
                        <div class="relative text-xxs">
                            <span class="absolute -left-[23px] top-0.5 w-2.5 h-2.5 rounded-full bg-slate-300 dark:bg-slate-700 border-2 border-white dark:border-slate-800"></span>
                            <div class="font-bold text-slate-700 dark:text-slate-300">{{ $log->description }}</div>
                            <div class="text-[9px] text-slate-400 mt-0.5">
                                By {{ $log->user->name ?? 'System' }} &bull; {{ $log->created_at->diffForHumans() }}
                            </div>
                        </div>
                    @empty
                        <div class="text-xxs text-slate-400 py-2">No activity records logged.</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
