@extends('admin.layouts.app', [
    'title' => 'Review Advertising Request | Hyper Adz Admin'
])

@section('content')
<div class="space-y-6">
    <!-- Header/Back -->
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.advertising.requests') }}" class="w-8 h-8 rounded-xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 flex items-center justify-center text-slate-500 dark:text-slate-400 hover:text-slate-800 dark:hover:text-white transition-all shadow-sm">
                <i class="bi bi-arrow-left"></i>
            </a>
            <div>
                <div class="flex items-center gap-2">
                    <h1 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white">{{ $campaign->campaign_name }}</h1>
                    <span class="font-mono text-xs bg-slate-100 dark:bg-slate-700 px-2.5 py-0.5 rounded-full text-slate-500 dark:text-slate-300 font-bold border border-slate-200 dark:border-slate-600">
                        {{ $campaign->campaign_code }}
                    </span>
                </div>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Submitted on {{ $campaign->created_at->format('d-M-Y H:i') }}</p>
            </div>
        </div>

        @php
            $statusColors = [
                'Draft' => 'bg-slate-100 text-slate-700 border-slate-200',
                'Submitted' => 'bg-amber-50 text-amber-700 border-amber-200 dark:bg-amber-900/20 dark:text-amber-400 dark:border-amber-900/30',
                'Creative Review' => 'bg-blue-50 text-blue-700 border-blue-200 dark:bg-blue-900/20 dark:text-blue-400 dark:border-blue-900/30',
                'Approved' => 'bg-emerald-50 text-emerald-700 border-emerald-200 dark:bg-emerald-900/20 dark:text-emerald-400 dark:border-emerald-900/30',
                'Payment Pending' => 'bg-orange-50 text-orange-700 border-orange-200 dark:bg-orange-900/20 dark:text-orange-400 dark:border-orange-900/30',
                'Payment Completed' => 'bg-teal-50 text-teal-700 border-teal-200 dark:bg-teal-900/20 dark:text-teal-400 dark:border-teal-900/30',
                'Scheduled' => 'bg-cyan-50 text-cyan-700 border-cyan-200 dark:bg-cyan-900/20 dark:text-cyan-400 dark:border-cyan-900/30',
                'Running' => 'bg-indigo-50 text-indigo-700 border-indigo-200 dark:bg-indigo-900/20 dark:text-indigo-400 dark:border-indigo-900/30',
                'Completed' => 'bg-purple-50 text-purple-700 border-purple-200 dark:bg-purple-900/20 dark:text-purple-400 dark:border-purple-900/30',
                'Report Uploaded' => 'bg-fuchsia-50 text-fuchsia-700 border-fuchsia-200 dark:bg-fuchsia-900/20 dark:text-fuchsia-400 dark:border-fuchsia-900/30',
                'Rejected (Admin)' => 'bg-rose-50 text-rose-700 border-rose-200 dark:bg-rose-900/20 dark:text-rose-400 dark:border-rose-900/30',
                'Rejected (Payment Expired)' => 'bg-red-50 text-red-700 border-red-200 dark:bg-red-900/20 dark:text-red-400 dark:border-red-900/30',
            ];
            $colorClass = $statusColors[$campaign->status] ?? 'bg-slate-50 text-slate-700 border-slate-200';
        @endphp
        <span class="inline-flex items-center gap-1.5 text-xs font-bold px-3 py-1 rounded-full border {{ $colorClass }}">
            {{ $campaign->status }}
        </span>
    </div>

    <!-- Status Stepper -->
    <div class="bg-white dark:bg-slate-800 border border-slate-200/80 dark:border-slate-700 rounded-[32px] p-6 shadow-sm overflow-x-auto">
        <h3 class="text-xs font-bold text-slate-900 dark:text-slate-100 uppercase tracking-wider border-b border-slate-100 dark:border-slate-700 pb-3 mb-6">Campaign Progress</h3>
        @php
            $steps = [
                'Submitted', 'Creative Review', 'Payment Pending', 'Scheduled', 'Running', 'Completed', 'Report Uploaded'
            ];
            $currentIndex = array_search($campaign->status, $steps);
            if ($campaign->status === 'Approved') $currentIndex = array_search('Payment Pending', $steps);
            if ($campaign->status === 'Payment Completed') $currentIndex = array_search('Scheduled', $steps);

            if ($currentIndex === false) {
                if ($campaign->status === 'Draft') $currentIndex = -1;
                elseif (str_starts_with($campaign->status, 'Rejected')) $currentIndex = array_search('Submitted', $steps);
            }
        @endphp
        <div class="flex items-center min-w-[800px]">
            @foreach($steps as $index => $step)
                <div class="flex flex-col items-center flex-1 relative">
                    <div class="w-8 h-8 rounded-full flex items-center justify-center font-bold text-[10px] z-10 border-2 
                        {{ $index < $currentIndex ? 'bg-[#1155CC] border-[#1155CC] text-white' : 
                           ($index === $currentIndex ? 'bg-white dark:bg-slate-800 border-[#1155CC] text-[#1155CC]' : 'bg-slate-100 dark:bg-slate-700 border-slate-300 dark:border-slate-600 text-slate-400') }}">
                        @if($index < $currentIndex)
                            <i class="bi bi-check-lg text-sm"></i>
                        @else
                            {{ $index + 1 }}
                        @endif
                    </div>
                    <div class="text-[9px] font-bold mt-2 uppercase tracking-wide {{ $index <= $currentIndex ? 'text-slate-800 dark:text-slate-200' : 'text-slate-400 dark:text-slate-500' }} text-center">
                        {{ $step }}
                    </div>
                    @if($index < count($steps) - 1)
                        <div class="absolute top-4 left-[50%] w-full h-[2px] -z-0 {{ $index < $currentIndex ? 'bg-[#1155CC]' : 'bg-slate-200 dark:bg-slate-700' }}"></div>
                    @endif
                </div>
            @endforeach
        </div>
    </div>

    <!-- Rejection Alert -->
    @if(str_starts_with($campaign->status, 'Rejected'))
        <div class="p-4 bg-rose-50 border border-rose-100 dark:bg-rose-950/10 dark:border-rose-900/30 text-rose-700 dark:text-rose-400 rounded-2xl text-xs font-semibold flex items-center gap-2">
            <i class="bi bi-x-circle-fill text-lg"></i> 
            <div>
                <strong>Rejection Reason:</strong> {{ $campaign->rejection_reason }}
                <div class="text-[10px] mt-1 font-normal opacity-80">Campaign was rejected. Advertiser must resubmit the draft.</div>
            </div>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Specs & Locations -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Specifications card -->
            <div class="bg-white dark:bg-slate-800 rounded-3xl border border-slate-100 dark:border-slate-700/50 shadow-sm p-6 space-y-5">
                <h3 class="text-sm font-bold text-slate-800 dark:text-slate-100 border-b border-slate-100 dark:border-slate-700 pb-3">Request Specifications</h3>
                
                <div class="grid grid-cols-2 md:grid-cols-3 gap-4 text-xs">
                    <div>
                        <span class="block text-[9px] uppercase tracking-wider text-slate-400 font-bold mb-0.5">Advertiser</span>
                        <span class="font-bold text-slate-800 dark:text-slate-200">{{ $campaign->advertiser->company_name ?? 'N/A' }}</span>
                        <span class="block text-[10px] text-slate-400 font-mono mt-0.5">{{ $campaign->advertiser->advertiser_code ?? '' }}</span>
                    </div>
                    <div>
                        <span class="block text-[9px] uppercase tracking-wider text-slate-400 font-bold mb-0.5">Campaign Type</span>
                        <span class="font-bold text-slate-800 dark:text-slate-200">{{ $campaign->campaign_type }}</span>
                    </div>
                    <div>
                        <span class="block text-[9px] uppercase tracking-wider text-slate-400 font-bold mb-0.5">Industry Sector</span>
                        <span class="font-bold text-slate-800 dark:text-slate-200">{{ $campaign->industry->name ?? 'N/A' }}</span>
                    </div>
                    <div>
                        <span class="block text-[9px] uppercase tracking-wider text-slate-400 font-bold mb-0.5">Start Date</span>
                        <span class="font-semibold text-slate-750 dark:text-slate-350">{{ $campaign->start_date->format('d-M-Y') }}</span>
                    </div>
                    <div>
                        <span class="block text-[9px] uppercase tracking-wider text-slate-400 font-bold mb-0.5">End Date</span>
                        <span class="font-semibold text-slate-750 dark:text-slate-350">{{ $campaign->end_date->format('d-M-Y') }}</span>
                    </div>
                    <div>
                        <span class="block text-[9px] uppercase tracking-wider text-slate-400 font-bold mb-0.5">Estimated Cost</span>
                        <span class="font-extrabold text-slate-850 dark:text-white font-mono">₹{{ number_format($campaign->budget, 2) }}</span>
                    </div>
                </div>

                @if($campaign->creative_path)
                    <div class="border-t border-slate-100 dark:border-slate-700 pt-4 space-y-2">
                        <span class="block text-[9px] uppercase tracking-wider text-slate-400 font-bold">Creative File Asset</span>
                        <div class="flex items-center justify-between p-3.5 bg-slate-50 dark:bg-slate-900 border border-slate-150 dark:border-slate-800 rounded-2xl">
                            <div class="flex items-center gap-2">
                                <i class="bi bi-file-earmark-play-fill text-lg text-[#1155CC]"></i>
                                <span class="text-xs text-slate-700 dark:text-slate-300 font-medium truncate max-w-xs">{{ $campaign->creative_name }}</span>
                            </div>
                            <a href="{{ asset('storage/' . $campaign->creative_path) }}" target="_blank" class="px-3 py-1 bg-white hover:bg-slate-50 dark:bg-slate-800 dark:hover:bg-slate-750 text-[10px] font-bold text-slate-700 dark:text-slate-300 rounded-lg border border-slate-200 dark:border-slate-700 transition-colors shadow-sm">
                                View File
                            </a>
                        </div>
                    </div>
                @endif
                
                @if($campaign->creative_review_notes)
                    <div class="border-t border-slate-100 dark:border-slate-700 pt-4 space-y-2">
                        <span class="block text-[9px] uppercase tracking-wider text-slate-400 font-bold">Creative Review Notes</span>
                        <div class="p-3 bg-blue-50/50 dark:bg-blue-900/10 border border-blue-100 dark:border-blue-900/30 rounded-xl text-xs text-blue-800 dark:text-blue-300">
                            {{ $campaign->creative_review_notes }}
                        </div>
                    </div>
                @endif

                @if($campaign->payment_amount > 0)
                    <div class="border-t border-slate-100 dark:border-slate-700 pt-4 space-y-2">
                        <span class="block text-[9px] uppercase tracking-wider text-slate-400 font-bold">Payment Details</span>
                        <div class="grid grid-cols-3 gap-4 text-xs">
                            @if($campaign->payment_status !== 'Paid')
                            <div>
                                <span class="block text-[10px] text-slate-500">Amount Due</span>
                                <span class="font-bold text-slate-900 dark:text-white font-mono">₹{{ number_format($campaign->payment_amount, 2) }}</span>
                            </div>
                            <div>
                                <span class="block text-[10px] text-slate-500">Due Date</span>
                                <span class="font-semibold text-slate-800 dark:text-slate-200">{{ \Carbon\Carbon::parse($campaign->payment_due_date)->format('d-M-Y') }}</span>
                            </div>
                            @endif
                            <div>
                                <span class="block text-[10px] text-slate-500">Status</span>
                                <span class="font-semibold {{ $campaign->payment_status === 'Paid' ? 'text-emerald-600' : 'text-orange-600' }}">{{ $campaign->payment_status }}</span>
                            </div>
                        </div>
                    </div>
                @endif
                
                @if($campaign->report_path)
                    <div class="border-t border-slate-100 dark:border-slate-700 pt-4 space-y-2">
                        <span class="block text-[9px] uppercase tracking-wider text-slate-400 font-bold">Analytics Report</span>
                        <div class="flex items-center justify-between p-3.5 bg-fuchsia-50 dark:bg-fuchsia-900/10 border border-fuchsia-150 dark:border-fuchsia-900/30 rounded-2xl">
                            <div class="flex items-center gap-2">
                                <i class="bi bi-file-earmark-bar-graph-fill text-lg text-fuchsia-600 dark:text-fuchsia-400"></i>
                                <span class="text-xs text-fuchsia-900 dark:text-fuchsia-100 font-medium truncate max-w-xs">{{ $campaign->report_name }}</span>
                            </div>
                            <a href="{{ asset('storage/' . $campaign->report_path) }}" target="_blank" class="px-3 py-1 bg-white dark:bg-slate-800 text-[10px] font-bold text-slate-700 dark:text-slate-300 rounded-lg shadow-sm border border-slate-200 dark:border-slate-700 transition-colors">
                                Download
                            </a>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Target Locations -->
            <div class="bg-white dark:bg-slate-800 rounded-3xl border border-slate-100 dark:border-slate-700/50 shadow-sm p-6 space-y-4">
                <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-700 pb-3">
                    <h3 class="text-sm font-bold text-slate-800 dark:text-slate-100">Selected Network Locations</h3>
                    <span class="text-xs text-slate-500 font-bold">{{ $campaign->locations->count() }} Locations</span>
                </div>
                
                <div class="space-y-2">
                    @foreach($campaign->locations as $loc)
                        <div class="flex items-center justify-between p-3.5 bg-slate-50 dark:bg-slate-900 border border-slate-150 dark:border-slate-800 rounded-2xl">
                            <div>
                                <div class="text-xs font-bold text-slate-800 dark:text-slate-200">{{ $loc->name }}</div>
                                <div class="text-[10px] text-slate-450 mt-0.5">{{ $loc->city }}, {{ $loc->state }} • Footfall: {{ number_format($loc->daily_footfall) }}/day</div>
                            </div>
                            <span class="text-xs font-bold text-slate-700 dark:text-slate-300 font-mono">₹{{ number_format($loc->price_per_day, 2) }}/day</span>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Right Side: Actions and Activity Logs -->
        <div class="space-y-6">
            <!-- Review Action Center -->
            @if(in_array($campaign->status, ['Submitted', 'Creative Review']))
                <div class="bg-white dark:bg-slate-800 rounded-3xl border border-slate-100 dark:border-slate-700/50 shadow-sm p-6 space-y-4">
                    <h3 class="text-sm font-bold text-slate-800 dark:text-slate-100 border-b border-slate-100 dark:border-slate-700 pb-3">Approval Decision</h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mb-2">Provide optional notes, then approve or reject the campaign.</p>
                    
                    <form action="{{ route('admin.advertising.requests.approve', $campaign->id) }}" method="POST" class="space-y-3 pt-2">
                        @csrf
                        <textarea name="creative_review_notes" rows="2" placeholder="Notes for advertiser (optional)" class="w-full p-3 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl text-xs text-slate-700 dark:text-slate-300 focus:border-[#1155CC] focus:outline-none"></textarea>
                        
                        <div class="flex gap-2">
                            <button type="submit" class="flex-1 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-bold transition-all shadow-sm">
                                Approve
                            </button>
                            <button type="button" onclick="openRejectModal()" class="flex-1 py-2.5 bg-rose-600 hover:bg-rose-700 text-white rounded-xl text-xs font-bold transition-all shadow-sm">
                                Reject
                            </button>
                        </div>
                    </form>
                </div>
            @elseif($campaign->status === 'Payment Pending')
                <div class="bg-white dark:bg-slate-800 rounded-3xl border border-slate-100 dark:border-slate-700/50 shadow-sm p-6 space-y-4">
                    <h3 class="text-sm font-bold text-slate-800 dark:text-slate-100 border-b border-slate-100 dark:border-slate-700 pb-3">Payment Confirmation</h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400">Once payment is received from advertiser, confirm it to schedule the campaign.</p>
                    <form action="{{ route('admin.advertising.requests.confirm-payment', $campaign->id) }}" method="POST" class="pt-2">
                        @csrf
                        @method('PUT')
                        <button type="submit" class="w-full py-2.5 bg-[#1155CC] hover:bg-blue-700 text-white rounded-xl text-xs font-bold transition-all shadow-sm">
                            <i class="bi bi-check-circle-fill mr-1"></i> Confirm Payment Received
                        </button>
                    </form>
                    <button type="button" onclick="openReverseModal()" class="w-full py-2.5 bg-white border border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-750 text-slate-700 dark:text-slate-300 rounded-xl text-xs font-bold transition-all shadow-sm flex items-center justify-center gap-1.5 mt-2">
                        <i class="bi bi-arrow-counterclockwise"></i> Reverse Approval / Request Changes
                    </button>
                </div>
            @elseif($campaign->status === 'Completed' && !$campaign->report_path)
                <div class="bg-white dark:bg-slate-800 rounded-3xl border border-slate-100 dark:border-slate-700/50 shadow-sm p-6 space-y-4">
                    <h3 class="text-sm font-bold text-slate-800 dark:text-slate-100 border-b border-slate-100 dark:border-slate-700 pb-3">Upload Report</h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400">Campaign finished. Upload the final analytics report.</p>
                    <form action="{{ route('admin.advertising.requests.upload-report', $campaign->id) }}" method="POST" enctype="multipart/form-data" class="pt-2 space-y-3">
                        @csrf
                        <input type="file" name="report" required class="w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border file:border-slate-200 file:text-xs file:font-bold file:bg-slate-100 file:text-slate-700 hover:file:bg-slate-200 transition-all">
                        <button type="submit" class="w-full py-2.5 bg-fuchsia-600 hover:bg-fuchsia-700 text-white rounded-xl text-xs font-bold transition-all shadow-sm">
                            <i class="bi bi-upload mr-1"></i> Upload Report
                        </button>
                    </form>
                </div>
            @endif

            <!-- History log timeline -->
            <div class="bg-white dark:bg-slate-800 rounded-3xl border border-slate-100 dark:border-slate-700/50 shadow-sm p-6 space-y-5">
                <h3 class="text-sm font-bold text-slate-800 dark:text-slate-100 border-b border-slate-100 dark:border-slate-700 pb-3">Campaign Timeline</h3>
                
                <div class="relative pl-6 space-y-6 before:absolute before:left-2 before:top-2 before:bottom-2 before:w-0.5 before:bg-slate-100 dark:before:bg-slate-700">
                    @forelse($campaign->activityLogs()->orderBy('created_at', 'desc')->get() as $log)
                        <div class="relative space-y-1">
                            <span class="absolute -left-[22px] top-1.5 w-2.5 h-2.5 rounded-full bg-[#1155CC] border-2 border-white dark:border-slate-800 ring-4 ring-[#1155CC]/10"></span>
                            <div class="flex items-center justify-between">
                                <span class="text-xs font-bold text-slate-800 dark:text-slate-100">{{ $log->action }}</span>
                                <span class="text-[9px] text-slate-400 font-mono">{{ $log->created_at->format('d-M H:i') }}</span>
                            </div>
                            <p class="text-[10px] text-slate-500 dark:text-slate-400 leading-relaxed">{{ $log->remarks }}</p>
                            <div class="text-[9px] text-slate-400 dark:text-slate-500">By: {{ $log->performed_by }}</div>
                        </div>
                    @empty
                        <div class="py-4 text-center text-xs text-slate-450">No log entries.</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Rejection Modal -->
<div id="rejectModal" class="fixed inset-0 bg-slate-950/40 backdrop-blur-sm z-50 flex items-center justify-center hidden">
    <div class="bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800 rounded-3xl p-6 max-w-md w-full mx-4 space-y-4 shadow-2xl">
        <h3 class="text-base font-bold text-slate-800 dark:text-slate-100">Reject Advertising Request</h3>
        <p class="text-xs text-slate-500 dark:text-slate-400">Please provide a brief reason for rejecting this advertising campaign booking request.</p>
        
        <form action="{{ route('admin.advertising.requests.reject', $campaign->id) }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <textarea name="rejection_reason" required rows="4" placeholder="Describe why this request was rejected..." class="w-full p-3 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-850 rounded-xl text-xs text-slate-700 dark:text-slate-200 focus:outline-none focus:border-[#1155CC] transition-all resize-none"></textarea>
            </div>
            
            <div class="flex items-center justify-end gap-2">
                <button type="button" onclick="closeRejectModal()" class="px-4 py-2 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-750 text-slate-655 dark:text-slate-300 rounded-lg text-xs font-bold transition-all">
                    Cancel
                </button>
                <button type="submit" class="px-4 py-2 bg-rose-600 hover:bg-rose-700 text-white rounded-lg text-xs font-bold transition-all shadow-sm">
                    Reject Request
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Reverse Approval Modal -->
<div id="reverseModal" class="fixed inset-0 bg-slate-950/40 backdrop-blur-sm z-50 flex items-center justify-center hidden">
    <div class="bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800 rounded-3xl p-6 max-w-md w-full mx-4 space-y-4 shadow-2xl">
        <h3 class="text-base font-bold text-slate-800 dark:text-slate-100">Reverse Approval / Request Changes</h3>
        <p class="text-xs text-slate-500 dark:text-slate-400">Specify whether to move the campaign back to review or reject it, along with a mandatory comment/reason.</p>
        
        <form action="{{ route('admin.advertising.requests.reverse-approval', $campaign->id) }}" method="POST" class="space-y-4">
            @csrf
            
            <div class="space-y-2">
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300">Action Type</label>
                <div class="flex gap-4">
                    <label class="flex items-center gap-2 text-xs text-slate-750 dark:text-slate-300 cursor-pointer">
                        <input type="radio" name="target_status" value="creative_review" checked class="text-[#1155CC] focus:ring-[#1155CC]">
                        Move to Creative Review
                    </label>
                    <label class="flex items-center gap-2 text-xs text-slate-750 dark:text-slate-300 cursor-pointer">
                        <input type="radio" name="target_status" value="rejected" class="text-[#1155CC] focus:ring-[#1155CC]">
                        Reject Campaign
                    </label>
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Reason / Notes (Mandatory)</label>
                <textarea name="reason" required rows="4" placeholder="Describe what changes are needed or the reason for rejection..." class="w-full p-3 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-850 rounded-xl text-xs text-slate-700 dark:text-slate-200 focus:outline-none focus:border-[#1155CC] transition-all resize-none"></textarea>
            </div>
            
            <div class="flex items-center justify-end gap-2">
                <button type="button" onclick="closeReverseModal()" class="px-4 py-2 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-750 text-slate-655 dark:text-slate-300 rounded-lg text-xs font-bold transition-all">
                    Cancel
                </button>
                <button type="submit" class="px-4 py-2 bg-[#1155CC] hover:bg-blue-700 text-white rounded-lg text-xs font-bold transition-all shadow-sm">
                    Confirm Reversal
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function openRejectModal() {
    const modal = document.getElementById('rejectModal');
    modal.classList.remove('hidden');
}

function closeRejectModal() {
    const modal = document.getElementById('rejectModal');
    modal.classList.add('hidden');
}

function openReverseModal() {
    const modal = document.getElementById('reverseModal');
    modal.classList.remove('hidden');
}

function closeReverseModal() {
    const modal = document.getElementById('reverseModal');
    modal.classList.add('hidden');
}
</script>
@endsection
