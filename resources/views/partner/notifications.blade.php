@extends('layouts.partner')

@section('title', 'Notifications')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div>
        <h2 class="text-xl font-extrabold tracking-tight text-slate-900">Notifications Center</h2>
        <p class="text-xs text-slate-455 mt-0.5">Logs and notifications regarding account status, location audits, and screens verification.</p>
    </div>

    <div class="bg-white border border-slate-200 rounded-[32px] p-6 sm:p-8 space-y-6">
        @if($notifications->isEmpty())
            <div class="py-16 text-center space-y-3">
                <div class="w-14 h-14 rounded-full bg-white/60 text-slate-500 border border-slate-200 flex items-center justify-center text-xl mx-auto">
                    <i class="bi bi-bell-slash"></i>
                </div>
                <p class="text-xs text-slate-500">No notifications found.</p>
            </div>
        @else
            <div class="space-y-4">
                @foreach($notifications as $notif)
                    <div class="flex items-start gap-4 p-4 bg-slate-100 border border-slate-200 rounded-2xl text-xs">
                        <div class="w-8 h-8 rounded-xl bg-white border border-slate-200 flex items-center justify-center text-xs shrink-0">
                            @if(str_contains($notif->description, 'Approved') || str_contains($notif->description, 'activated'))
                                <i class="bi bi-check-circle-fill text-emerald-450"></i>
                            @elseif(str_contains($notif->description, 'Rejected') || str_contains($notif->description, 'suspended'))
                                <i class="bi bi-exclamation-circle-fill text-rose-455"></i>
                            @else
                                <i class="bi bi-info-circle-fill text-blue-450"></i>
                            @endif
                        </div>
                        <div class="flex-grow space-y-1">
                            <p class="text-slate-700 leading-relaxed font-semibold">{{ $notif->description }}</p>
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
