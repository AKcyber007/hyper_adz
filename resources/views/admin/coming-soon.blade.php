@extends('admin.layouts.app')

@section('page_title')
    <i class="bi bi-clock-history text-[#1155CC]"></i> {{ $title ?? 'Module Coming Soon' }}
@endsection

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="bg-[#0A1628] border border-slate-850 rounded-[32px] p-12 text-center space-y-6 shadow-xl relative overflow-hidden">
        <!-- Subtle decorative glow -->
        <div class="absolute right-0 bottom-0 w-60 h-60 bg-blue-500/5 rounded-full blur-3xl translate-x-10 translate-y-10"></div>
        
        <div class="w-16 h-16 rounded-full bg-slate-900 text-slate-500 border border-slate-800 flex items-center justify-center text-2xl mx-auto shadow-inner">
            <i class="bi bi-cone-striped text-amber-500"></i>
        </div>
        
        <div class="space-y-2">
            <h3 class="text-xl font-bold text-white">{{ $title ?? 'Module' }} — Coming Soon</h3>
            <p class="text-sm text-slate-400 max-w-md mx-auto leading-relaxed">
                The layout engine, active scheduling modules, and pipeline tools for {{ strtolower($title ?? 'this module') }} are currently being built by our engineering team.
            </p>
        </div>

        <div class="pt-4">
            <a href="{{ route('admin.dashboard') }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-[#1155CC] hover:bg-blue-600 text-white text-sm font-semibold rounded-xl transition-all shadow-lg shadow-blue-500/20">
                <i class="bi bi-arrow-left"></i> Return to Dashboard
            </a>
        </div>
    </div>
</div>
@endsection
