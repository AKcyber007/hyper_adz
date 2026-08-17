@extends('admin.layouts.app', [
    'title' => 'Advertising Requests | Hyper Adz Admin'
])

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white">Advertising Requests</h1>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Review and process digital indoor display campaign slot booking requests.</p>
        </div>
    </div>

    <!-- Alerts -->
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
    <div class="flex overflow-x-auto border-b border-slate-200 dark:border-slate-800 scrollbar-hide">
        <a href="{{ route('admin.advertising.requests', ['status' => 'Review']) }}" class="px-5 py-3 border-b-2 font-bold text-sm flex items-center gap-2 transition-all shrink-0 {{ $status === 'Review' ? 'border-[#1155CC] text-[#1155CC] dark:text-blue-400 dark:border-blue-400' : 'border-transparent text-slate-450 hover:text-slate-700 dark:hover:text-slate-250' }}">
            <i class="bi bi-inbox-fill"></i>
            <span>Campaign Review</span>
            @php
                $pendingCount = \App\Models\Campaign::whereIn('status', ['Submitted', 'Creative Review'])->count();
            @endphp
            @if($pendingCount > 0)
                <span class="text-[10px] font-bold bg-[#1155CC] text-white px-2 py-0.5 rounded-full shrink-0 shadow-sm">
                    {{ $pendingCount }}
                </span>
            @endif
        </a>
        <a href="{{ route('admin.advertising.requests', ['status' => 'Payment Pending']) }}" class="px-5 py-3 border-b-2 font-semibold text-sm flex items-center gap-2 transition-all shrink-0 {{ $status === 'Payment Pending' ? 'border-[#1155CC] text-[#1155CC] dark:text-blue-400 dark:border-blue-400' : 'border-transparent text-slate-450 hover:text-slate-700 dark:hover:text-slate-250' }}">
            <i class="bi bi-credit-card-fill"></i>
            <span>Pending Payment</span>
        </a>
        <a href="{{ route('admin.advertising.requests', ['status' => 'Scheduled']) }}" class="px-5 py-3 border-b-2 font-semibold text-sm flex items-center gap-2 transition-all shrink-0 {{ $status === 'Scheduled' ? 'border-[#1155CC] text-[#1155CC] dark:text-blue-400 dark:border-blue-400' : 'border-transparent text-slate-450 hover:text-slate-700 dark:hover:text-slate-250' }}">
            <i class="bi bi-calendar-check-fill"></i>
            <span>Scheduled</span>
        </a>
        <a href="{{ route('admin.advertising.requests', ['status' => 'Running']) }}" class="px-5 py-3 border-b-2 font-semibold text-sm flex items-center gap-2 transition-all shrink-0 {{ $status === 'Running' ? 'border-[#1155CC] text-[#1155CC] dark:text-blue-400 dark:border-blue-400' : 'border-transparent text-slate-450 hover:text-slate-700 dark:hover:text-slate-250' }}">
            <i class="bi bi-play-circle-fill"></i>
            <span>Running</span>
        </a>
        <a href="{{ route('admin.advertising.requests', ['status' => 'Completed']) }}" class="px-5 py-3 border-b-2 font-semibold text-sm flex items-center gap-2 transition-all shrink-0 {{ $status === 'Completed' ? 'border-[#1155CC] text-[#1155CC] dark:text-blue-400 dark:border-blue-400' : 'border-transparent text-slate-450 hover:text-slate-700 dark:hover:text-slate-250' }}">
            <i class="bi bi-check-all"></i>
            <span>Completed</span>
        </a>
    </div>

    <!-- Campaigns Table -->
    <div class="bg-white dark:bg-slate-800 rounded-3xl border border-slate-100 dark:border-slate-700/50 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full border-collapse text-left">
                <thead>
                    <tr class="border-b border-slate-100 dark:border-slate-750 bg-slate-50/50 dark:bg-slate-850/20">
                        <th class="px-6 py-4 text-xs font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider">Campaign Code</th>
                        <th class="px-6 py-4 text-xs font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider">Advertiser</th>
                        <th class="px-6 py-4 text-xs font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider">Campaign Name</th>
                        <th class="px-6 py-4 text-xs font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider">Type</th>
                        <th class="px-6 py-4 text-xs font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider">Date Range</th>
                        <th class="px-6 py-4 text-xs font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider">Budget</th>
                        <th class="px-6 py-4 text-xs font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider">Status</th>
                        @if(str_starts_with($status, 'Rejected'))
                            <th class="px-6 py-4 text-xs font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider">Rejection Reason</th>
                        @endif
                        <th class="px-6 py-4 text-xs font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-750">
                    @forelse($campaigns as $camp)
                        <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-850/5 transition-all text-xs text-slate-700 dark:text-slate-350">
                            <!-- Code -->
                            <td class="px-6 py-4 whitespace-nowrap font-mono text-[10px] text-slate-400 font-bold">
                                {{ $camp->campaign_code }}
                            </td>

                            <!-- Advertiser -->
                            <td class="px-6 py-4 font-bold text-slate-800 dark:text-slate-200">
                                {{ $camp->advertiser->company_name ?? 'Unknown Advertiser' }}
                                <div class="text-[10px] text-slate-400 font-mono mt-0.5">{{ $camp->advertiser->advertiser_code ?? '' }}</div>
                            </td>

                            <!-- Campaign Name -->
                            <td class="px-6 py-4 font-bold text-slate-700 dark:text-slate-300">
                                {{ $camp->campaign_name }}
                            </td>

                            <!-- Type -->
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center gap-1 text-[10px] font-bold bg-indigo-50 dark:bg-indigo-950/20 text-[#1155CC] dark:text-indigo-400 border border-indigo-100 dark:border-indigo-900/30 px-2.5 py-0.5 rounded-full">
                                    {{ $camp->campaign_type }}
                                </span>
                            </td>

                            <!-- Date Range -->
                            <td class="px-6 py-4 whitespace-nowrap text-slate-500 dark:text-slate-400">
                                {{ $camp->start_date->format('M d') }} - {{ $camp->end_date->format('M d, Y') }}
                            </td>

                            <!-- Budget -->
                            <td class="px-6 py-4 whitespace-nowrap font-bold text-slate-800 dark:text-slate-200">
                                ₹{{ number_format($camp->budget, 2) }}
                            </td>

                            <!-- Status Badge -->
                            <td class="px-6 py-4 whitespace-nowrap">
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
                                    $colorClass = $statusColors[$camp->status] ?? 'bg-slate-50 text-slate-700 border-slate-200';
                                @endphp
                                <span class="inline-flex items-center gap-1 text-[10px] font-bold px-2 py-0.5 rounded-full border {{ $colorClass }}">
                                    {{ $camp->status }}
                                </span>
                            </td>

                            <!-- Rejection Reason (only on rejected tab) -->
                            @if(str_starts_with($status, 'Rejected'))
                                <td class="px-6 py-4 text-rose-600 dark:text-rose-400 italic">
                                    {{ $camp->rejection_reason ?? 'No reason stated' }}
                                </td>
                            @endif

                            <!-- Detail link -->
                            <td class="px-6 py-4 text-right whitespace-nowrap">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('admin.advertising.requests.show', $camp->id) }}" class="px-3 py-1.5 bg-[#1155CC] hover:bg-blue-700 text-white rounded-lg text-xs font-bold transition-all shadow-sm">
                                        Review
                                    </a>
                                    @if(in_array($camp->status, ['Scheduled', 'Running', 'Completed', 'Report Uploaded']))
                                        <button type="button" onclick="document.getElementById('reportModal{{ $camp->id }}').classList.remove('hidden')" class="px-3 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 dark:bg-slate-700 dark:hover:bg-slate-600 dark:text-slate-200 border border-slate-200 dark:border-slate-600 rounded-lg text-xs font-bold transition-all shadow-sm">
                                            Reports
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>

                        <!-- Report Modal -->
                        @if(in_array($camp->status, ['Scheduled', 'Running', 'Completed', 'Report Uploaded']))
                        <div id="reportModal{{ $camp->id }}" class="fixed inset-0 z-[100] hidden">
                            <!-- Backdrop -->
                            <div class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm transition-opacity" onclick="document.getElementById('reportModal{{ $camp->id }}').classList.add('hidden')"></div>
                            
                            <!-- Modal Content -->
                            <div class="fixed inset-0 z-[101] overflow-y-auto">
                                <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
                                    <div class="relative transform overflow-hidden rounded-2xl bg-white dark:bg-slate-900 text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg border border-slate-200 dark:border-slate-800">
                                        <form action="{{ route('admin.advertising.requests.upload-report', $camp->id) }}" method="POST" enctype="multipart/form-data">
                                            @csrf
                                            <div class="bg-white dark:bg-slate-900 px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                                                <div class="flex justify-between items-center mb-5">
                                                    <h3 class="text-lg font-bold text-slate-900 dark:text-white" id="modal-title">Upload Report</h3>
                                                    <button type="button" class="text-slate-400 hover:text-slate-500 transition-colors" onclick="document.getElementById('reportModal{{ $camp->id }}').classList.add('hidden')">
                                                        <i class="bi bi-x-lg text-lg"></i>
                                                    </button>
                                                </div>
                                                <div class="mt-2 space-y-4">
                                                    <p class="text-sm text-slate-500 dark:text-slate-400">
                                                        Upload a performance report or supporting document for campaign <strong>{{ $camp->campaign_code }}</strong>.
                                                    </p>
                                                    
                                                    @if($camp->report_path)
                                                        <div class="p-3 bg-blue-50 dark:bg-blue-900/20 text-blue-700 dark:text-blue-300 rounded-xl text-xs font-medium border border-blue-100 dark:border-blue-800/30">
                                                            <i class="bi bi-info-circle-fill me-1"></i> A report is already uploaded (<a href="{{ asset('storage/'.$camp->report_path) }}" target="_blank" class="underline font-bold text-[#1155CC] dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 transition-colors">View</a>). Uploading a new file will replace it.
                                                        </div>
                                                    @endif
                                                    
                                                    <div>
                                                        <label class="text-xs font-bold text-slate-500 dark:text-slate-400 block mb-2 uppercase tracking-wide">Select File (PDF, ZIP, JPG, PNG, DOC, XLS, CSV)</label>
                                                        <div class="relative">
                                                            <input type="file" name="report" required class="block w-full text-sm text-slate-500 dark:text-slate-400 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-[#1155CC]/10 file:text-[#1155CC] hover:file:bg-[#1155CC]/20 dark:file:bg-blue-900/30 dark:file:text-blue-400 dark:hover:file:bg-blue-900/50 transition-all border border-slate-200 dark:border-slate-700 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#1155CC]/50 file:cursor-pointer cursor-pointer">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="bg-slate-50 dark:bg-slate-800/50 px-4 py-4 sm:flex sm:flex-row-reverse sm:px-6 border-t border-slate-100 dark:border-slate-800">
                                                <button type="submit" class="inline-flex w-full justify-center rounded-xl bg-[#1155CC] px-4 py-2.5 text-sm font-bold text-white shadow-sm hover:bg-blue-700 transition-colors sm:ml-3 sm:w-auto shadow-blue-500/20">
                                                    Upload Report
                                                </button>
                                                <button type="button" class="mt-3 inline-flex w-full justify-center rounded-xl bg-white dark:bg-slate-800 px-4 py-2.5 text-sm font-bold text-slate-700 dark:text-slate-300 shadow-sm ring-1 ring-inset ring-slate-300 dark:ring-slate-700 hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors sm:mt-0 sm:w-auto" onclick="document.getElementById('reportModal{{ $camp->id }}').classList.add('hidden')">
                                                    Cancel
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif

                    @empty
                        <tr>
                            <td colspan="{{ $status === 'Rejected' ? 8 : 7 }}" class="px-6 py-12 text-center text-slate-450">
                                <div class="max-w-xs mx-auto space-y-2 py-4">
                                    <i class="bi bi-file-earmark-play text-3xl text-slate-300 dark:text-slate-700"></i>
                                    <p class="font-bold text-slate-700 dark:text-slate-350">No {{ strtolower($status) }} requests</p>
                                    <p class="text-[11px]">Advertising requests submitted by advertisers will appear here for review.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($campaigns->hasPages())
            <div class="px-6 py-4 border-t border-slate-100 dark:border-slate-750 bg-slate-50/30 dark:bg-slate-850/10">
                {{ $campaigns->appends(['status' => $status])->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
