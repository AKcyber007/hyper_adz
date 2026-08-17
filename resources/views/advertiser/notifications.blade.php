@extends('layouts.advertiser')

@section('title', 'Notifications')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div>
        <h2 class="text-xl font-extrabold tracking-tight text-white">Notifications</h2>
        <p class="text-xs text-slate-455 mt-0.5">Track verification reviews and system logs regarding your advertiser account.</p>
    </div>

    <div class="bg-slate-900/40 border border-slate-850 rounded-[32px] p-6 sm:p-8 space-y-6">
        @if($notifications->isEmpty())
            <div class="py-16 text-center space-y-3">
                <div class="w-14 h-14 rounded-full bg-slate-950/60 text-slate-500 border border-slate-850 flex items-center justify-center text-xl mx-auto">
                    <i class="bi bi-bell-slash"></i>
                </div>
                <p class="text-xs text-slate-450">No notifications found.</p>
            </div>
        @else
            <div class="space-y-4">
                @foreach($notifications as $notif)
                    <div class="flex items-start gap-4 p-4 bg-slate-950/40 border border-slate-850/60 rounded-2xl text-xs">
                        <div class="w-8 h-8 rounded-xl bg-slate-900 border border-slate-800 flex items-center justify-center text-xs shrink-0">
                            <i class="bi bi-info-circle-fill text-indigo-400"></i>
                        </div>
                        <div class="flex-grow space-y-1">
                            <p class="text-slate-200 leading-relaxed font-semibold">{{ $notif->description }}</p>
                            <span class="block text-[10px] text-slate-500">{{ $notif->created_at ? $notif->created_at->format('d-M-Y H:i') : '' }}</span>
                        </div>
                    </div>
                @endforeach
            </div>

            @if($notifications->hasPages())
                <div class="pt-4">
                    {{ $notifications->links() }}
                </div>
            @endif
        @endif
    </div>
</div>
@endsection
