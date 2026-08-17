@extends('layouts.advertiser')

@section('title', 'Create Campaign')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center gap-3">
        <a href="{{ route('advertiser.my-requests') }}" class="w-8 h-8 rounded-xl bg-white border border-slate-200 flex items-center justify-center text-slate-500 hover:text-slate-900 transition-all shadow-sm">
            <i class="bi bi-arrow-left"></i>
        </a>
        <div>
            <h1 class="text-xl font-bold tracking-tight text-slate-900 font-outfit">Create Campaign</h1>
            <p class="text-[11px] text-slate-500">Request slot bookings and campaign placements across our display network.</p>
        </div>
    </div>

    <!-- Form & Calculator layout -->
    <form action="{{ route('advertiser.my-requests.store') }}" method="POST" enctype="multipart/form-data" class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        @csrf
        
        <!-- Left 2 columns: Inputs -->
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white border border-slate-200/80 rounded-[32px] p-6 space-y-5 shadow-sm">
                <h3 class="text-xs font-bold text-slate-800 uppercase tracking-wider border-b border-slate-100 pb-3">Campaign Specifications</h3>
                
                <div class="grid grid-cols-1 gap-4">
                    <!-- Name -->
                    <div class="space-y-1.5">
                        <label class="text-[11px] font-bold text-slate-600">Campaign Name</label>
                        <input type="text" name="campaign_name" required placeholder="e.g. Summer Launch Promotion" class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-900 placeholder-slate-400 focus:outline-none focus:border-indigo-500 transition-all">
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Start Date -->
                        <div class="space-y-1.5">
                            <label class="text-[11px] font-bold text-slate-600">Start Date</label>
                            <input type="date" name="start_date" id="startDate" required min="{{ date('Y-m-d', strtotime('+1 day')) }}" class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-900 focus:outline-none focus:border-indigo-500 transition-all">
                        </div>

                        <!-- End Date -->
                        <div class="space-y-1.5">
                            <label class="text-[11px] font-bold text-slate-600">End Date</label>
                            <input type="date" name="end_date" id="endDate" required min="{{ date('Y-m-d', strtotime('+1 day')) }}" class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-900 focus:outline-none focus:border-indigo-500 transition-all">
                        </div>
                    </div>
                </div>

                <!-- Creative Upload -->
                <div class="space-y-2 border-t border-slate-100 pt-4">
                    <label class="text-[11px] font-bold text-slate-600">Upload Campaign Creative (Video/Image)</label>
                    <input type="file" name="creative" class="w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border file:border-slate-250 file:text-xs file:font-bold file:bg-slate-100 file:text-slate-700 hover:file:bg-slate-200 transition-all">
                    <p class="text-[10px] text-slate-400">Supported formats: MP4, MOV, JPG, PNG (Max 20MB).</p>
                </div>
            </div>

            <!-- Target Location Checklist -->
            <div class="bg-white border border-slate-200/80 rounded-[32px] p-6 space-y-4 shadow-sm">
                <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                    <h3 class="text-xs font-bold text-slate-800 uppercase tracking-wider">Select Target Locations</h3>
                    <span class="text-[10px] font-mono font-bold text-indigo-600" id="selectedLocationsBadge">0 selected</span>
                </div>

                <div class="space-y-2 max-h-[300px] overflow-y-auto pr-1">
                    @foreach($locations as $loc)
                        @php
                            $rate = $loc->price_per_day;
                            $isActive = $loc->status === 'active';
                        @endphp
                        <label class="flex items-start justify-between p-3.5 bg-slate-50 border border-slate-150 rounded-2xl {{ $isActive ? 'cursor-pointer hover:bg-slate-100/50 hover:border-slate-300' : 'opacity-60 cursor-not-allowed' }} transition-all group">
                            <div class="flex gap-3">
                                <input type="checkbox" name="locations[]" value="{{ $loc->id }}" data-rate="{{ $rate }}" {{ $isActive ? '' : 'disabled' }} {{ (is_array(request('locations')) && in_array($loc->id, request('locations'))) ? 'checked' : '' }} class="location-checkbox mt-0.5 rounded text-indigo-600 focus:ring-indigo-500 border-slate-300 {{ $isActive ? '' : 'cursor-not-allowed' }}">
                                <div>
                                    <div class="text-xs font-bold text-slate-800 {{ $isActive ? 'group-hover:text-indigo-600' : '' }} transition-all flex items-center gap-2">
                                        {{ $loc->name }}
                                        @if(!$isActive)
                                            <span class="px-1.5 py-0.5 rounded text-[9px] font-bold uppercase tracking-wider {{ $loc->status === 'maintenance' ? 'bg-amber-100 text-amber-700' : 'bg-rose-100 text-rose-700' }}">
                                                {{ $loc->status }}
                                            </span>
                                        @endif
                                    </div>
                                    <div class="text-[10px] text-slate-500 mt-0.5">{{ $loc->city }}, {{ $loc->state }} • Footfall: {{ number_format($loc->daily_footfall) }}/day</div>
                                </div>
                            </div>
                            <div class="text-right shrink-0">
                                <span class="text-xs font-bold text-slate-800 font-mono">₹{{ number_format($rate, 2) }}</span>
                                <span class="block text-[9px] text-slate-400">/ day</span>
                            </div>
                        </label>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Right 1 column: Summary and Cost Calculator -->
        <div class="space-y-6">
            <div class="bg-indigo-50/60 border border-indigo-100 rounded-[32px] p-6 space-y-6 sticky top-24 shadow-sm">
                <h3 class="text-xs font-bold text-indigo-900 uppercase tracking-wider border-b border-indigo-100 pb-3">Cost Calculator</h3>
                
                <div class="space-y-4">
                    <div class="flex justify-between text-xs">
                        <span class="text-slate-500">Duration:</span>
                        <span class="font-bold text-slate-900 font-mono" id="calcDuration">0 days</span>
                    </div>
                    <div class="flex justify-between text-xs">
                        <span class="text-slate-500">Locations Selected:</span>
                        <span class="font-bold text-slate-900 font-mono" id="calcLocations">0</span>
                    </div>
                    <div class="flex justify-between text-xs">
                        <span class="text-slate-500">Total Rate:</span>
                        <span class="font-bold text-slate-900 font-mono" id="calcTotalRate">₹0.00 / day</span>
                    </div>
                    <hr class="border-indigo-100">
                    <div class="flex justify-between items-end">
                        <span class="text-xs text-slate-600">Calculated Cost:</span>
                        <div class="text-right">
                            <span class="text-lg font-extrabold text-indigo-950 font-mono" id="calcTotalCost">₹0.00</span>
                        </div>
                    </div>
                </div>

                <div class="space-y-2">
                    <button type="submit" name="action" value="submit" class="w-full py-3 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-xs font-bold transition-all shadow-md">
                        Submit for Review
                    </button>
                    <button type="submit" name="action" value="draft" formnovalidate class="w-full py-3 bg-white hover:bg-slate-50 border border-slate-200 text-slate-700 rounded-xl text-xs font-bold transition-all">
                        Save as Draft
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const checkboxes = document.querySelectorAll('.location-checkbox');
    const startDateInput = document.getElementById('startDate');
    const endDateInput = document.getElementById('endDate');
    
    const calcDuration = document.getElementById('calcDuration');
    const calcLocations = document.getElementById('calcLocations');
    const calcTotalRate = document.getElementById('calcTotalRate');
    const calcTotalCost = document.getElementById('calcTotalCost');
    const selectedLocationsBadge = document.getElementById('selectedLocationsBadge');

    function calculate() {
        let selectedCount = 0;
        let dailyRateSum = 0;

        checkboxes.forEach(box => {
            if (box.checked) {
                selectedCount++;
                dailyRateSum += parseFloat(box.dataset.rate || 0);
            }
        });

        // Calculate days
        let days = 0;
        if (startDateInput.value && endDateInput.value) {
            const start = new Date(startDateInput.value);
            const end = new Date(endDateInput.value);
            if (end >= start) {
                const diffTime = Math.abs(end - start);
                days = Math.ceil(diffTime / (1000 * 60 * 60 * 24)) + 1;
            }
        }

        const totalCost = days * dailyRateSum;

        // Update UI
        calcLocations.textContent = selectedCount;
        selectedLocationsBadge.textContent = `${selectedCount} selected`;
        calcDuration.textContent = `${days} days`;
        calcTotalRate.textContent = `₹${dailyRateSum.toFixed(2)} / day`;
        calcTotalCost.textContent = `₹${totalCost.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;
    }

    checkboxes.forEach(box => box.addEventListener('change', calculate));
    startDateInput.addEventListener('change', calculate);
    endDateInput.addEventListener('change', calculate);

    // Initial calculation in case locations were pre-checked via URL
    calculate();
});
</script>
@endsection
