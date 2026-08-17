@extends('layouts.advertiser')

@section('title', 'Campaign Details')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-3">
            <a href="{{ route('advertiser.my-requests') }}" class="w-8 h-8 rounded-xl bg-white border border-slate-200 flex items-center justify-center text-slate-500 hover:text-slate-900 transition-all shadow-sm">
                <i class="bi bi-arrow-left"></i>
            </a>
            <div>
                <div class="flex items-center gap-2">
                    <h1 class="text-xl font-bold tracking-tight text-slate-900 font-outfit">{{ $campaign->campaign_name }}</h1>
                    <span class="font-mono text-[10px] bg-slate-50 border border-slate-200 px-2 py-0.5 rounded-full text-slate-500 font-bold">
                        {{ $campaign->campaign_code }}
                    </span>
                </div>
                <p class="text-[11px] text-slate-500 mt-1">Submitted on {{ $campaign->created_at->format('d-M-Y H:i') }}</p>
            </div>
        </div>

        @php
            $statusColors = [
                'Draft' => 'bg-slate-100 text-slate-700 border-slate-200',
                'Submitted' => 'bg-amber-50 text-amber-700 border-amber-200',
                'Creative Review' => 'bg-blue-50 text-blue-700 border-blue-200',
                'Approved' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                'Payment Pending' => 'bg-orange-50 text-orange-700 border-orange-200',
                'Payment Completed' => 'bg-teal-50 text-teal-700 border-teal-200',
                'Scheduled' => 'bg-cyan-50 text-cyan-700 border-cyan-200',
                'Running' => 'bg-indigo-50 text-indigo-700 border-indigo-200',
                'Completed' => 'bg-purple-50 text-purple-700 border-purple-200',
                'Report Uploaded' => 'bg-fuchsia-50 text-fuchsia-700 border-fuchsia-200',
                'Rejected (Admin)' => 'bg-rose-50 text-rose-700 border-rose-200',
                'Rejected (Payment Expired)' => 'bg-red-50 text-red-700 border-red-200',
            ];
            $colorClass = $statusColors[$campaign->status] ?? 'bg-slate-50 text-slate-700 border-slate-200';
        @endphp
        <span class="inline-flex items-center gap-1.5 text-xs font-bold px-3 py-1 rounded-full border {{ $colorClass }}">
            {{ $campaign->status }}
        </span>
    </div>

    <!-- Status Stepper -->
    <div class="bg-white border border-slate-200/80 rounded-[32px] p-6 shadow-sm overflow-x-auto">
        <h3 class="text-xs font-bold text-slate-900 uppercase tracking-wider border-b border-slate-100 pb-3 mb-6">Campaign Progress</h3>
        @php
            $steps = [
                'Submitted', 'Creative Review', 'Approved', 'Payment Pending', 'Payment Completed', 'Scheduled', 'Running', 'Completed', 'Report Uploaded'
            ];
            $currentIndex = array_search($campaign->status, $steps);
            if ($currentIndex === false) {
                if ($campaign->status === 'Draft') $currentIndex = -1;
                elseif (str_starts_with($campaign->status, 'Rejected')) $currentIndex = array_search('Submitted', $steps);
            }
        @endphp
        <div class="flex items-center min-w-[800px]">
            @foreach($steps as $index => $step)
                <div class="flex flex-col items-center flex-1 relative">
                    <div class="w-8 h-8 rounded-full flex items-center justify-center font-bold text-[10px] z-10 border-2 
                        {{ $index < $currentIndex ? 'bg-indigo-600 border-indigo-600 text-white' : 
                           ($index === $currentIndex ? 'bg-white border-indigo-600 text-indigo-600' : 'bg-slate-100 border-slate-300 text-slate-400') }}">
                        @if($index < $currentIndex)
                            <i class="bi bi-check-lg text-sm"></i>
                        @else
                            {{ $index + 1 }}
                        @endif
                    </div>
                    <div class="text-[9px] font-bold mt-2 uppercase tracking-wide {{ $index <= $currentIndex ? 'text-slate-800' : 'text-slate-400' }} text-center">
                        {{ $step }}
                    </div>
                    @if($index < count($steps) - 1)
                        <div class="absolute top-4 left-[50%] w-full h-[2px] -z-0 {{ $index < $currentIndex ? 'bg-indigo-600' : 'bg-slate-200' }}"></div>
                    @endif
                </div>
            @endforeach
        </div>
    </div>

    <!-- Rejection Warning Alert -->
    @if($campaign->status === 'Rejected (Admin)')
        <div class="p-4 bg-rose-50 border border-rose-200 rounded-2xl space-y-4">
            <div class="flex gap-3">
                <i class="bi bi-x-circle-fill text-rose-700 text-xl"></i>
                <div>
                    <h4 class="text-sm font-bold text-rose-700">Request Rejected by Admin</h4>
                    <p class="text-xs text-rose-600 mt-1">{{ $campaign->rejection_reason }}</p>
                </div>
            </div>
            <div class="flex justify-end border-t border-rose-200 pt-3">
                <form action="{{ route('advertiser.my-requests.resubmit', $campaign->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <!-- In a real app we might want to redirect them to edit first before resubmitting -->
                    <a href="{{ route('advertiser.my-requests.edit', $campaign->id) }}" class="px-4 py-2 bg-rose-600 hover:bg-rose-700 text-white rounded-xl text-xs font-bold transition-all shadow-md">
                        Revise Details
                    </a>
                </form>
            </div>
        </div>
    @elseif($campaign->status === 'Creative Review')
        <div class="p-4 bg-blue-50 border border-blue-200 rounded-2xl space-y-4">
            <div class="flex gap-3">
                <i class="bi bi-info-circle-fill text-blue-700 text-xl"></i>
                <div>
                    <h4 class="text-sm font-bold text-blue-700">Creative Review Notes</h4>
                    <p class="text-xs text-blue-600 mt-1">{{ $campaign->creative_review_notes ?? 'Changes requested for your campaign. Please revise creative or dates.' }}</p>
                </div>
            </div>
            <div class="flex justify-end border-t border-blue-200 pt-3">
                <a href="{{ route('advertiser.my-requests.edit', $campaign->id) }}" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-xs font-bold transition-all shadow-md">
                    Revise Details
                </a>
            </div>
        </div>
    @elseif($campaign->status === 'Rejected (Payment Expired)')
        <div class="p-4 bg-red-50 border border-red-200 rounded-2xl space-y-2">
            <div class="flex gap-3">
                <i class="bi bi-exclamation-triangle-fill text-red-700 text-xl"></i>
                <div>
                    <h4 class="text-sm font-bold text-red-700">Payment Expired</h4>
                    <p class="text-xs text-red-600 mt-1">Payment was not confirmed by the due date. The campaign was cancelled.</p>
                </div>
            </div>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Specs & Locations -->
        <div class="lg:col-span-2 space-y-6">
            
            <!-- Payment Action Section -->
            @if($campaign->status === 'Payment Pending')
            <div class="bg-indigo-50 border border-indigo-200 rounded-[32px] p-6 space-y-4 shadow-sm">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-sm font-bold text-indigo-900">Payment Required</h3>
                        <p class="text-[11px] text-indigo-700 mt-0.5">Please complete your payment to secure this booking.</p>
                    </div>
                </div>
                <div class="pt-4 border-t border-indigo-200/60">
                    <a href="{{ $campaign->zoho_payment_url ?? '#' }}" class="px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-xs font-bold shadow-md shadow-indigo-600/20 transition-all inline-flex items-center gap-2">
                        <i class="bi bi-credit-card-fill"></i> Make Payment Now
                    </a>
                </div>
            </div>
            @elseif($campaign->payment_status === 'Paid')
            <div class="bg-emerald-50 border border-emerald-200 rounded-2xl p-4 flex items-center gap-3">
                <i class="bi bi-check-circle-fill text-emerald-600 text-lg"></i>
                <div>
                    <span class="text-xs font-bold text-emerald-800">Status: Paid</span>
                    <span class="text-[10px] text-emerald-600 ml-2">Campaign is confirmed and scheduled.</span>
                </div>
            </div>
            @endif

            <!-- Analytics Report Section -->
            @if($campaign->status === 'Report Uploaded' && $campaign->report_path)
            <div class="bg-fuchsia-50 border border-fuchsia-200 rounded-[32px] p-6 space-y-4 shadow-sm flex items-center justify-between">
                <div>
                    <h3 class="text-sm font-bold text-fuchsia-900"><i class="bi bi-bar-chart-fill mr-1"></i> Campaign Analytics Report</h3>
                    <p class="text-[11px] text-fuchsia-700 mt-0.5">Your campaign has concluded and the final report is ready.</p>
                </div>
                <a href="{{ route('advertiser.my-requests.report.download', $campaign->id) }}" class="px-5 py-2.5 bg-fuchsia-600 hover:bg-fuchsia-700 text-white rounded-xl text-xs font-bold shadow-md shadow-fuchsia-600/20 transition-all">
                    Download Report
                </a>
            </div>
            @endif

            <!-- Specifications -->
            <div class="bg-white border border-slate-200/80 rounded-[32px] p-6 space-y-4 shadow-sm">
                <h3 class="text-xs font-bold text-slate-900 uppercase tracking-wider border-b border-slate-100 pb-3">Request Specifications</h3>
                
                <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                    <div>
                        <span class="block text-[9px] uppercase tracking-wider text-slate-400 font-bold">Campaign Type</span>
                        <span class="text-xs font-bold text-slate-800">{{ $campaign->campaign_type }}</span>
                    </div>
                    <div>
                        <span class="block text-[9px] uppercase tracking-wider text-slate-400 font-bold">Industry Sector</span>
                        <span class="text-xs font-bold text-slate-800">{{ $campaign->industry->name ?? 'N/A' }}</span>
                    </div>
                    <div>
                        <span class="block text-[9px] uppercase tracking-wider text-slate-400 font-bold">Calculated Cost</span>
                        <span class="text-xs font-bold text-slate-800 font-mono">₹{{ number_format($campaign->budget, 2) }}</span>
                    </div>
                    <div>
                        <span class="block text-[9px] uppercase tracking-wider text-slate-400 font-bold">Start Date</span>
                        <span class="text-xs font-semibold text-slate-700">{{ $campaign->start_date->format('d-M-Y') }}</span>
                    </div>
                    <div>
                        <span class="block text-[9px] uppercase tracking-wider text-slate-400 font-bold">End Date</span>
                        <span class="text-xs font-semibold text-slate-700">{{ $campaign->end_date->format('d-M-Y') }}</span>
                    </div>
                    <div>
                        <span class="block text-[9px] uppercase tracking-wider text-slate-400 font-bold">Total Days</span>
                        @php
                            $days = $campaign->start_date->diffInDays($campaign->end_date) + 1;
                        @endphp
                        <span class="text-xs font-bold text-slate-800 font-mono">{{ $days }} days</span>
                    </div>
                </div>

                @if($campaign->creative_path)
                    <div class="border-t border-slate-100 pt-4 space-y-2">
                        <span class="block text-[9px] uppercase tracking-wider text-slate-400 font-bold">Creative Asset</span>
                        <div class="flex items-center justify-between p-3.5 bg-slate-50 border border-slate-200 rounded-2xl">
                            <div class="flex items-center gap-2">
                                <i class="bi bi-file-earmark-play-fill text-lg text-indigo-650"></i>
                                <span class="text-xs text-slate-700 font-medium truncate max-w-xs">{{ $campaign->creative_name }}</span>
                            </div>
                            <a href="{{ asset('storage/' . $campaign->creative_path) }}" target="_blank" class="px-3 py-1 bg-white hover:bg-slate-50 text-[10px] font-bold text-slate-700 rounded-lg border border-slate-200 transition-colors shadow-sm">
                                View File
                            </a>
                        </div>
                        @if($campaign->creative_review_notes)
                            <div class="mt-2 p-3 bg-blue-50/50 border border-blue-100 rounded-xl text-[11px] text-blue-800">
                                <span class="font-bold uppercase text-[9px] tracking-wider mb-1 block">Review Notes</span>
                                {{ $campaign->creative_review_notes }}
                            </div>
                        @endif
                    </div>
                    </div>
                @endif
                
                @if($campaign->payment_amount > 0)
                    <div class="border-t border-slate-100 pt-4 space-y-2">
                        <span class="block text-[9px] uppercase tracking-wider text-slate-400 font-bold">Payment Details</span>
                        <div class="grid grid-cols-3 gap-4 text-xs">
                            @if($campaign->payment_status !== 'Paid')
                            <div>
                                <span class="block text-[10px] text-slate-500">Amount Due</span>
                                <span class="font-bold text-slate-900 font-mono">₹{{ number_format($campaign->payment_amount, 2) }}</span>
                            </div>
                            <div>
                                <span class="block text-[10px] text-slate-500">Due Date</span>
                                <span class="font-semibold text-slate-800">{{ \Carbon\Carbon::parse($campaign->payment_due_date)->format('d-M-Y') }}</span>
                            </div>
                            @endif
                            <div>
                                <span class="block text-[10px] text-slate-500">Status</span>
                                <span class="font-semibold {{ $campaign->payment_status === 'Paid' ? 'text-emerald-600' : 'text-orange-600' }}">{{ $campaign->payment_status }}</span>
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Locations -->
            <div class="bg-white border border-slate-200/80 rounded-[32px] p-6 space-y-4 shadow-sm">
                <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                    <h3 class="text-xs font-bold text-slate-900 uppercase tracking-wider">Selected Display Locations</h3>
                    <span class="text-[10px] text-slate-500 font-bold">{{ $campaign->locations->count() }} locations</span>
                </div>
                
                <div class="space-y-2">
                    @foreach($campaign->locations as $loc)
                        <div class="flex items-center justify-between p-3.5 bg-slate-50 border border-slate-200 rounded-2xl">
                            <div>
                                <div class="text-xs font-bold text-slate-800">{{ $loc->name }}</div>
                                <div class="text-[10px] text-slate-500 mt-0.5">{{ $loc->city }}, {{ $loc->state }}</div>
                            </div>
                            <span class="text-xs font-bold text-slate-700 font-mono">₹{{ number_format($loc->price_per_day, 2) }}/day</span>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Activity logs / Timeline -->
        <div class="space-y-6">
            <div class="bg-white border border-slate-200/80 rounded-[32px] p-6 space-y-5 shadow-sm">
                <h3 class="text-xs font-bold text-slate-900 uppercase tracking-wider border-b border-slate-100 pb-3">Lifecycle History</h3>
                
                <div class="relative pl-6 space-y-6 before:absolute before:left-2 before:top-2 before:bottom-2 before:w-0.5 before:bg-slate-200">
                    @forelse($campaign->activityLogs()->orderBy('created_at', 'desc')->get() as $log)
                        <div class="relative space-y-1">
                            <span class="absolute -left-[22px] top-1.5 w-2.5 h-2.5 rounded-full bg-indigo-600 border-2 border-white ring-4 ring-indigo-650/10"></span>
                            <div class="flex items-center justify-between">
                                <span class="text-xs font-bold text-slate-800">{{ $log->action }}</span>
                                <span class="text-[9px] text-slate-500 font-mono">{{ $log->created_at->format('d-M H:i') }}</span>
                            </div>
                            <p class="text-[10px] text-slate-650 leading-relaxed">{{ $log->remarks }}</p>
                            <div class="text-[9px] text-slate-400">Performed by: {{ $log->performed_by }}</div>
                        </div>
                    @empty
                        <div class="py-4 text-center text-[11px] text-slate-400">No logs found.</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
