@extends('admin.layouts.app', [
    'title' => 'Location Update Requests | Hyper Adz Admin'
])

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white">Location Update Requests</h1>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Review and process requests submitted by Venue Partners to edit location specs, pricing, and maintenance status.</p>
        </div>
    </div>

    <!-- Feedback Alerts -->
    @if(session('success'))
        <div class="p-4 bg-emerald-50 border border-emerald-100 dark:bg-emerald-950/10 dark:border-emerald-900/30 text-emerald-700 dark:text-emerald-400 rounded-2xl text-xs font-semibold flex items-center gap-2">
            <i class="bi bi-check-circle-fill"></i> {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="p-4 bg-rose-50 border border-rose-100 dark:bg-rose-950/10 dark:border-rose-900/30 text-rose-700 dark:text-rose-400 rounded-2xl text-xs font-semibold flex items-center gap-2">
            <i class="bi bi-exclamation-circle-fill"></i> {{ session('error') }}
        </div>
    @endif

    <!-- Status Tabs Navigation -->
    <div class="flex border-b border-slate-200 dark:border-slate-800">
        <a href="{{ route('admin.locations.update-requests', ['status' => 'pending']) }}" class="px-5 py-3 border-b-2 font-bold text-sm flex items-center gap-2 transition-all {{ $status === 'pending' ? 'border-[#1155CC] text-[#1155CC] dark:text-blue-400 dark:border-blue-400' : 'border-transparent text-slate-450 hover:text-slate-700 dark:hover:text-slate-250' }}">
            <i class="bi bi-clock-history"></i>
            <span>Pending Requests</span>
            @php
                $pendingCount = \App\Models\LocationUpdateRequest::where('status', 'pending')->count();
            @endphp
            @if($pendingCount > 0)
                <span class="text-[10px] font-bold bg-[#1155CC] text-white px-2 py-0.5 rounded-full shrink-0 shadow-sm">
                    {{ $pendingCount }}
                </span>
            @endif
        </a>
        <a href="{{ route('admin.locations.update-requests', ['status' => 'approved']) }}" class="px-5 py-3 border-b-2 font-semibold text-sm flex items-center gap-2 transition-all {{ $status === 'approved' ? 'border-[#1155CC] text-[#1155CC] dark:text-blue-400 dark:border-blue-400' : 'border-transparent text-slate-450 hover:text-slate-700 dark:hover:text-slate-250' }}">
            <i class="bi bi-check-circle-fill"></i>
            <span>Approved</span>
        </a>
        <a href="{{ route('admin.locations.update-requests', ['status' => 'rejected']) }}" class="px-5 py-3 border-b-2 font-semibold text-sm flex items-center gap-2 transition-all {{ $status === 'rejected' ? 'border-[#1155CC] text-[#1155CC] dark:text-blue-400 dark:border-blue-400' : 'border-transparent text-slate-450 hover:text-slate-700 dark:hover:text-slate-250' }}">
            <i class="bi bi-x-circle-fill"></i>
            <span>Rejected</span>
        </a>
    </div>

    <!-- Requests Table -->
    <div class="bg-white dark:bg-slate-800 rounded-3xl border border-slate-100 dark:border-slate-700/50 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full border-collapse text-left">
                <thead>
                    <tr class="border-b border-slate-100 dark:border-slate-750 bg-slate-50/50 dark:bg-slate-850/20">
                        <th class="px-6 py-4 text-xs font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider">Date</th>
                        <th class="px-6 py-4 text-xs font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider">Partner</th>
                        <th class="px-6 py-4 text-xs font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider">Location</th>
                        <th class="px-6 py-4 text-xs font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider">Type</th>
                        <th class="px-6 py-4 text-xs font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider">Requested Change</th>
                        @if($status === 'rejected')
                            <th class="px-6 py-4 text-xs font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider">Rejection Reason</th>
                        @endif
                        @if($status === 'pending')
                            <th class="px-6 py-4 text-xs font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider text-right">Actions</th>
                        @endif
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-750">
                    @forelse($requests as $req)
                        <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-850/5 transition-all text-xs text-slate-700 dark:text-slate-350">
                            <!-- Date -->
                            <td class="px-6 py-4 whitespace-nowrap font-mono text-[10px] text-slate-400">
                                {{ $req->created_at->format('M d, Y h:i A') }}
                            </td>

                            <!-- Partner -->
                            <td class="px-6 py-4 font-bold text-slate-800 dark:text-slate-200">
                                {{ $req->partner->company_name ?? 'Unknown Partner' }}
                                <div class="text-[10px] text-slate-400 font-mono mt-0.5">{{ $req->partner->partner_code ?? '' }}</div>
                            </td>

                            <!-- Location -->
                            <td class="px-6 py-4 font-semibold text-slate-700 dark:text-slate-300">
                                {{ $req->location->name ?? 'Deleted Location' }}
                                <div class="text-[10px] text-slate-400 font-mono mt-0.5">{{ $req->location->location_code ?? '' }}</div>
                            </td>

                            <!-- Request Type -->
                            <td class="px-6 py-4">
                                @if($req->request_type === 'new_location')
                                    <span class="inline-flex items-center gap-1 text-[10px] font-bold bg-indigo-50 dark:bg-indigo-950/20 text-indigo-600 dark:text-indigo-400 border border-indigo-100 dark:border-indigo-900/30 px-2.5 py-0.5 rounded-full">
                                        <i class="bi bi-plus-circle-fill"></i> New Location
                                    </span>
                                @elseif($req->request_type === 'price_change')
                                    <span class="inline-flex items-center gap-1 text-[10px] font-bold bg-blue-50 dark:bg-blue-950/20 text-[#1155CC] dark:text-blue-400 border border-blue-100 dark:border-blue-900/30 px-2.5 py-0.5 rounded-full">
                                        Price Change
                                    </span>
                                @elseif($req->request_type === 'maintenance')
                                    <span class="inline-flex items-center gap-1 text-[10px] font-bold bg-amber-50 dark:bg-amber-950/20 text-amber-600 dark:text-amber-450 border border-amber-100 dark:border-amber-900/30 px-2.5 py-0.5 rounded-full">
                                        Maintenance
                                    </span>
                                @elseif($req->request_type === 'active')
                                    <span class="inline-flex items-center gap-1 text-[10px] font-bold bg-emerald-50 dark:bg-emerald-950/20 text-emerald-600 dark:text-emerald-450 border border-emerald-100 dark:border-emerald-900/30 px-2.5 py-0.5 rounded-full">
                                        Set Active
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 text-[10px] font-bold bg-purple-50 dark:bg-purple-950/20 text-purple-600 dark:text-purple-400 border border-purple-100 dark:border-purple-900/30 px-2.5 py-0.5 rounded-full">
                                        Edit Details
                                    </span>
                                @endif
                            </td>

                            <!-- Requested Change details -->
                            <td class="px-6 py-4 max-w-xs">
                                @if($req->request_type === 'new_location')
                                    @php
                                        $newLoc = json_decode($req->requested_value, true);
                                    @endphp
                                    <div class="font-medium text-slate-800 dark:text-slate-200">
                                        New Location: {{ $req->location->name ?? 'Unknown' }}
                                    </div>
                                    <div class="text-[10px] text-slate-400 mt-0.5">
                                        @if(is_array($newLoc))
                                            {{ $newLoc['city'] ?? '' }}{{ isset($newLoc['price_per_day']) ? ' · ₹'.$newLoc['price_per_day'].'/day' : '' }}
                                        @endif
                                    </div>
                                @elseif($req->request_type === 'price_change')
                                    <div class="font-medium text-slate-800 dark:text-slate-200">
                                        Price Change Request
                                    </div>
                                    <div class="text-[10px] text-slate-400 mt-0.5">
                                        Requested details: {{ $req->requested_value }}
                                    </div>
                                @elseif($req->request_type === 'details_edit')
                                    @php
                                        $details = json_decode($req->requested_value, true);
                                    @endphp
                                    @if(is_array($details) && $req->location)
                                        <div class="space-y-1">
                                            @foreach(collect($details)->except(['temp_images','delete_images']) as $field => $val)
                                                @php
                                                    $rawOldVal = $req->location->{$field} ?? '';
                                                    $oldVal = is_array($rawOldVal) ? json_encode($rawOldVal) : $rawOldVal;
                                                    $newVal = is_array($val) ? json_encode($val) : $val;
                                                @endphp
                                                @if((string)$oldVal !== (string)$newVal)
                                                    <div class="text-[10px]">
                                                        <span class="font-semibold uppercase tracking-wider text-slate-500">{{ str_replace('_', ' ', $field) }}:</span>
                                                        <span class="text-rose-500 line-through mr-1">{{ $oldVal ?: 'N/A' }}</span>
                                                        <i class="bi bi-arrow-right text-slate-400 mx-1"></i>
                                                        <span class="text-emerald-600 font-medium">{{ $newVal ?: 'N/A' }}</span>
                                                    </div>
                                                @endif
                                            @endforeach
                                            @if(isset($details['temp_images']) && count($details['temp_images']) > 0)
                                                <div class="text-[10px] text-blue-500 font-semibold">+ {{ count($details['temp_images']) }} new photo(s) pending approval</div>
                                            @endif
                                            @if(isset($details['delete_images']) && count($details['delete_images']) > 0)
                                                <div class="text-[10px] text-rose-500 font-semibold">- {{ count($details['delete_images']) }} photo(s) requested for deletion</div>
                                            @endif
                                        </div>
                                    @else
                                        <div class="text-[10px] text-slate-450">{{ $req->requested_value }}</div>
                                    @endif
                                @else
                                    <div class="text-[10px] text-slate-450">
                                        Notes: {{ $req->notes ?? 'No additional notes provided' }}
                                    </div>
                                @endif
                            </td>

                            <!-- Rejection Reason (only on rejected tab) -->
                            @if($status === 'rejected')
                                <td class="px-6 py-4 text-rose-600 dark:text-rose-400 italic">
                                    {{ $req->rejection_reason ?? 'No reason stated' }}
                                </td>
                            @endif

                            <!-- Action buttons (only on pending tab) -->
                            @if($status === 'pending')
                                <td class="px-6 py-4 text-right whitespace-nowrap">
                                    <div class="flex items-center justify-end gap-2">
                                        <form action="{{ route('admin.locations.update-requests.approve', $req->id) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit" class="px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-xs font-bold transition-all shadow-sm">
                                                Approve
                                            </button>
                                        </form>
                                        <button onclick="openRejectModal({{ $req->id }})" class="px-3 py-1.5 bg-rose-600 hover:bg-rose-700 text-white rounded-lg text-xs font-bold transition-all shadow-sm">
                                            Reject
                                        </button>
                                    </div>
                                </td>
                            @endif
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ $status === 'rejected' ? 6 : ($status === 'pending' ? 6 : 5) }}" class="px-6 py-12 text-center text-slate-450">
                                <div class="max-w-xs mx-auto space-y-2 py-4">
                                    <i class="bi bi-patch-question text-3xl text-slate-300 dark:text-slate-700"></i>
                                    <p class="font-bold text-slate-700 dark:text-slate-350">No {{ $status }} requests</p>
                                    <p class="text-[11px]">Location update requests from partners will appear here for admin review.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($requests->hasPages())
            <div class="px-6 py-4 border-t border-slate-100 dark:border-slate-750 bg-slate-50/30 dark:bg-slate-850/10">
                {{ $requests->appends(['status' => $status])->links() }}
            </div>
        @endif
    </div>
</div>

<!-- Rejection Modal -->
<div id="rejectModal" class="fixed inset-0 bg-slate-950/40 backdrop-blur-sm z-50 flex items-center justify-center hidden">
    <div class="bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800 rounded-3xl p-6 max-w-md w-full mx-4 space-y-4 shadow-2xl">
        <h3 class="text-base font-bold text-slate-800 dark:text-slate-100">Reject Update Request</h3>
        <p class="text-xs text-slate-450">Please provide a brief reason for rejecting this change request. This will be visible to the venue partner.</p>
        
        <form id="rejectForm" method="POST" class="space-y-4">
            @csrf
            <div>
                <textarea name="rejection_reason" required rows="4" placeholder="Describe why this change request was rejected..." class="w-full p-3 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-850 rounded-xl text-xs text-slate-700 dark:text-slate-200 focus:outline-none focus:border-[#1155CC] transition-all resize-none"></textarea>
            </div>
            
            <div class="flex items-center justify-end gap-2">
                <button type="button" onclick="closeRejectModal()" class="px-4 py-2 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-750 text-slate-600 dark:text-slate-300 rounded-lg text-xs font-bold transition-all">
                    Cancel
                </button>
                <button type="submit" class="px-4 py-2 bg-rose-600 hover:bg-rose-700 text-white rounded-lg text-xs font-bold transition-all shadow-sm">
                    Reject Request
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function openRejectModal(requestId) {
    const modal = document.getElementById('rejectModal');
    const form = document.getElementById('rejectForm');
    
    // Set dynamic form action url
    form.action = `/admin/locations/update-requests/${requestId}/reject`;
    
    modal.classList.remove('hidden');
}

function closeRejectModal() {
    const modal = document.getElementById('rejectModal');
    modal.classList.add('hidden');
}
</script>
@endsection
