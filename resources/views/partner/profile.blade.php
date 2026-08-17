@extends('layouts.partner')

@section('title', 'Business Profile')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-xl font-extrabold tracking-tight text-slate-900">Business Profile Details</h2>
            <p class="text-xs text-slate-455 mt-0.5">Review your registered location partner business parameters.</p>
        </div>
        <a href="{{ route('partner.profile.edit') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold rounded-xl transition-colors shadow-sm">
            <i class="bi bi-pencil-square"></i> Edit Profile
        </a>
    </div>

    <div class="bg-white border border-slate-200 rounded-[32px] p-6 sm:p-8 space-y-8">
        <!-- Logo and header -->
        <div class="flex items-center gap-4 pb-6 border-b border-slate-200">
            <div class="w-16 h-16 rounded-2xl overflow-hidden border border-slate-200 bg-white flex items-center justify-center text-2xl shadow-inner shrink-0">
                @if($profile->logo_path)
                    <img src="{{ Storage::url($profile->logo_path) }}" class="w-full h-full object-cover">
                @else
                    <i class="bi bi-building text-slate-500"></i>
                @endif
            </div>
            <div>
                <h3 class="text-md font-bold text-slate-900">{{ $profile->company_name }}</h3>
                <span class="block text-xs text-slate-455 font-mono mt-0.5">Partner Code: {{ $profile->partner_code }}</span>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 text-xs">
            <div class="space-y-1">
                <span class="block text-[10px] text-slate-455 font-bold uppercase tracking-wider">Contact Person</span>
                <span class="block text-slate-700 font-semibold">{{ $profile->contact_person }}</span>
            </div>
            @if($profile->designation)
                <div class="space-y-1">
                    <span class="block text-[10px] text-slate-455 font-bold uppercase tracking-wider">Designation</span>
                    <span class="block text-slate-700 font-semibold">{{ $profile->designation }}</span>
                </div>
            @endif
            <div class="space-y-1">
                <span class="block text-[10px] text-slate-455 font-bold uppercase tracking-wider">Phone Number</span>
                <span class="block text-slate-700 font-mono font-semibold">{{ $profile->phone }}</span>
            </div>
            <div class="space-y-1">
                <span class="block text-[10px] text-slate-455 font-bold uppercase tracking-wider">Email Address</span>
                <span class="block text-slate-700 font-mono font-semibold">{{ $profile->email }}</span>
            </div>
            @if($profile->website)
                <div class="space-y-1">
                    <span class="block text-[10px] text-slate-455 font-bold uppercase tracking-wider">Website URL</span>
                    <a href="{{ $profile->website }}" target="_blank" class="block text-blue-400 hover:text-blue-300 font-mono font-semibold">{{ $profile->website }}</a>
                </div>
            @endif
            <div class="space-y-1">
                <span class="block text-[10px] text-slate-455 font-bold uppercase tracking-wider">GST Number</span>
                <span class="block text-slate-700 font-mono font-semibold">{{ $profile->gst_number ?: 'Not Registered' }}</span>
            </div>
            <div class="space-y-1 col-span-2">
                <span class="block text-[10px] text-slate-455 font-bold uppercase tracking-wider">Address</span>
                <span class="block text-slate-700">
                    {{ $profile->address_line_1 }}<br>
                    @if($profile->address_line_2) {{ $profile->address_line_2 }}<br> @endif
                    {{ $profile->city }}, {{ $profile->state }} - {{ $profile->postal_code }}<br>
                    {{ $profile->country }}
                </span>
            </div>
            @if($profile->notes)
                <div class="space-y-1 col-span-2 border-t border-slate-200 pt-4">
                    <span class="block text-[10px] text-slate-455 font-bold uppercase tracking-wider">Business Notes</span>
                    <span class="block text-slate-600 leading-relaxed">{{ $profile->notes }}</span>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
