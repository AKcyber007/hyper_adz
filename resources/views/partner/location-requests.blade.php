@extends('layouts.partner')

@section('title', 'Location Requests')

@section('content')
<div class="space-y-8">
    <div>
        <h2 class="text-xl font-extrabold tracking-tight text-slate-900">Location Requests</h2>
        <p class="text-xs text-slate-500 mt-0.5">Track the verification lifecycle of your new venue submissions and custom details edit requests.</p>
    </div>

    <!-- Feedback Alerts -->
    @if(session('success'))
        <div class="p-4 bg-emerald-50 border border-emerald-100 text-emerald-700 rounded-2xl text-xs font-semibold flex items-center gap-2">
            <i class="bi bi-check-circle-fill"></i> {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="p-4 bg-rose-50 border border-rose-100 text-rose-700 rounded-2xl text-xs font-semibold flex items-center gap-2">
            <i class="bi bi-exclamation-circle-fill"></i> {{ session('error') }}
        </div>
    @endif

    <!-- New Location Submissions -->
    <div class="bg-white border border-slate-200 shadow-sm rounded-2xl p-6 sm:p-8 space-y-5">
        <h3 class="text-sm font-bold text-slate-900 border-b border-slate-200 pb-4 flex items-center gap-2">
            <i class="bi bi-geo-alt-fill text-blue-500"></i> New Venue Submissions
        </h3>

        @if($locations->isEmpty())
            <div class="py-8 text-center text-xs text-slate-500">No new venue submissions tracked yet.</div>
        @else
            <div class="space-y-4">
                @foreach($locations as $loc)
                    <div class="bg-slate-50 border border-slate-200 hover:border-slate-300 p-5 rounded-2xl space-y-4 shadow-sm hover:shadow-md transition-all duration-200 ease-in-out">
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                            <div class="flex items-start gap-3">
                                <div class="w-9 h-9 rounded-xl bg-blue-50 border border-blue-100 flex items-center justify-center text-blue-500 text-lg shrink-0">
                                    <i class="bi bi-geo-alt-fill"></i>
                                </div>
                                <div>
                                    <h4 class="text-xs font-bold text-slate-900">{{ $loc->name }}</h4>
                                    <p class="text-[10px] text-slate-500 mt-0.5">Code: <span class="font-mono font-bold">{{ $loc->location_code }}</span> • Submitted: {{ $loc->created_at ? $loc->created_at->format('d M Y, H:i') : 'N/A' }}</p>
                                </div>
                            </div>
                            <div class="shrink-0 flex items-center gap-3">
                                <span class="inline-flex items-center gap-1 text-[10px] font-bold px-2 py-0.5 rounded-full border {{ $loc->status === 'active' || $loc->status === 'approved' ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : ($loc->status === 'rejected' ? 'bg-rose-50 text-rose-700 border-rose-200' : 'bg-indigo-50 text-indigo-700 border-indigo-200') }}">
                                    <span class="w-1.5 h-1.5 rounded-full {{ $loc->status === 'active' || $loc->status === 'approved' ? 'bg-emerald-500' : ($loc->status === 'rejected' ? 'bg-rose-500' : 'bg-indigo-500') }}"></span>
                                    {{ ucfirst($loc->status) }}
                                </span>
                                <a href="{{ route('partner.locations.show', $loc->id) }}" class="px-3 py-1.5 bg-white hover:bg-slate-100 text-[10px] font-bold text-slate-700 rounded-lg transition-colors border border-slate-200 shadow-sm">Details</a>
                            </div>
                        </div>

                        <!-- Feedback Box if rejected -->
                        @if($loc->status === 'rejected')
                            <div class="bg-rose-50 border border-rose-100 rounded-xl p-3.5 text-xs">
                                <p class="text-rose-700 font-bold mb-1 flex items-center gap-1.5"><i class="bi bi-x-circle-fill"></i> Rejection Feedback</p>
                                <p class="text-slate-700 leading-relaxed">{{ $loc->rejection_reason ?? 'Coordinates, category, or footfall validation failed. Please check profile details and resubmit.' }}</p>
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    <!-- Custom Specification Update Requests -->
    <div class="bg-white border border-slate-200 shadow-sm rounded-2xl p-6 sm:p-8 space-y-5">
        <h3 class="text-sm font-bold text-slate-900 border-b border-slate-200 pb-4 flex items-center gap-2">
            <i class="bi bi-pencil-square text-indigo-400"></i> Specification Edit Requests
        </h3>

        @if($updateRequests->isEmpty())
            <div class="py-8 text-center text-xs text-slate-500">No specification edit requests submitted yet.</div>
        @else
            <div class="space-y-4">
                @foreach($updateRequests as $req)
                    <div class="bg-slate-50 border border-slate-200 hover:border-slate-300 p-5 rounded-2xl space-y-4 shadow-sm hover:shadow-md transition-all duration-200 ease-in-out">
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                            <div class="flex items-start gap-3">
                                <div class="w-9 h-9 rounded-xl bg-indigo-50 border border-indigo-100 flex items-center justify-center text-indigo-500 text-lg shrink-0">
                                    <i class="bi bi-patch-question-fill"></i>
                                </div>
                                <div>
                                    <h4 class="text-xs font-bold text-slate-900">{{ $req->location->name ?? 'Unknown Location' }}</h4>
                                    <p class="text-[10px] text-slate-500 mt-0.5">
                                        Type: <span class="font-semibold text-indigo-500">{{ ucfirst(str_replace('_', ' ', $req->request_type)) }}</span> • 
                                        Submitted: {{ $req->created_at->format('d M Y, H:i') }}
                                    </p>
                                </div>
                            </div>
                            <div class="shrink-0 flex items-center gap-3">
                                <span class="inline-flex items-center gap-1 text-[10px] font-bold px-2 py-0.5 rounded-full border {{ $req->status === 'approved' ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : ($req->status === 'rejected' ? 'bg-rose-50 text-rose-700 border-rose-200' : 'bg-indigo-50 text-indigo-700 border-indigo-200') }}">
                                    <span class="w-1.5 h-1.5 rounded-full {{ $req->status === 'approved' ? 'bg-emerald-500' : ($req->status === 'rejected' ? 'bg-rose-500' : 'bg-indigo-500') }}"></span>
                                    {{ ucfirst($req->status) }}
                                </span>
                                <form action="{{ route('partner.location-requests.cancel', $req->id) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to cancel this request?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-slate-400 hover:text-rose-600 hover:bg-rose-50 px-2 py-1.5 rounded-lg transition-colors border border-transparent hover:border-rose-100" title="Cancel Request">
                                        <i class="bi bi-trash3"></i>
                                    </button>
                                </form>
                            </div>
                        </div>

                        <!-- Details of changes -->
                        <div class="bg-white p-4 rounded-xl border border-slate-200 text-xs space-y-2 shadow-sm">
                            @if($req->request_type === 'details_edit' || $req->request_type === 'price_change')
                                @php
                                    $details = json_decode($req->requested_value, true);
                                @endphp
                                @if(is_array($details))
                                    <div class="space-y-1.5">
                                        @foreach($details as $field => $val)
                                            <div>
                                                <span class="text-slate-500 font-semibold uppercase tracking-wider text-[9px]">{{ str_replace('_', ' ', $field) }}:</span>
                                                <span class="text-slate-700 font-mono font-medium">{{ is_array($val) ? json_encode($val) : $val }}</span>
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <div class="text-slate-700">{{ $req->requested_value }}</div>
                                @endif
                            @else
                                <div class="text-slate-700">
                                    Notes: {{ $req->notes ?? 'No additional notes provided.' }}
                                </div>
                            @endif
                        </div>

                        <!-- Feedback Box if rejected -->
                        @if($req->status === 'rejected')
                            <div class="bg-rose-50 border border-rose-100 rounded-xl p-3.5 text-xs">
                                <p class="text-rose-700 font-bold mb-1 flex items-center gap-1.5"><i class="bi bi-x-circle-fill"></i> Rejection Feedback</p>
                                <p class="text-slate-700 leading-relaxed">{{ $req->rejection_reason ?? 'The submitted specifications do not match our validation protocols. Please check your data.' }}</p>
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>
@endsection
