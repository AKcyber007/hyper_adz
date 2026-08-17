@extends('admin.layouts.app')

@section('page_title')
    <i class="bi bi-gear-fill text-[#1155CC]"></i> System Settings
@endsection

@section('content')
<div class="max-w-4xl space-y-8">
    <div class="bg-white dark:bg-slate-800 rounded-3xl p-6 md:p-8 border border-slate-100 dark:border-slate-700/50 shadow-sm space-y-6">
        <div class="flex items-center gap-3">
            <div class="w-12 h-12 rounded-2xl bg-amber-50 dark:bg-amber-950/20 text-amber-600 dark:text-amber-400 flex items-center justify-center">
                <i class="bi bi-tools text-xl"></i>
            </div>
            <div>
                <h3 class="text-lg font-bold text-slate-800 dark:text-slate-100">System Settings Under Construction</h3>
                <p class="text-sm text-slate-400">Settings and customization modules will be integrated in the next phase.</p>
            </div>
        </div>

        <!-- Placeholder items -->
        <div class="border-t border-slate-100 dark:border-slate-800 pt-6 grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="p-4 bg-slate-50 dark:bg-slate-900 border border-slate-100/50 dark:border-slate-850 rounded-2xl space-y-2">
                <div class="flex items-center gap-2 text-sm font-semibold text-slate-700 dark:text-slate-300">
                    <i class="bi bi-bell-fill text-[#1155CC]"></i> Notification Settings
                </div>
                <p class="text-xs text-slate-400">Configure email and push notifications for campaign submissions and screen alerts.</p>
                <span class="inline-block text-[10px] bg-slate-200 dark:bg-slate-800 text-slate-500 px-2 py-0.5 rounded-full font-bold">TODO</span>
            </div>

            <div class="p-4 bg-slate-50 dark:bg-slate-900 border border-slate-100/50 dark:border-slate-850 rounded-2xl space-y-2">
                <div class="flex items-center gap-2 text-sm font-semibold text-slate-700 dark:text-slate-300">
                    <i class="bi bi-display-fill text-[#1155CC]"></i> Display Settings
                </div>
                <p class="text-xs text-slate-400">Set default screen loop intervals, transition timings, and player updates.</p>
                <span class="inline-block text-[10px] bg-slate-200 dark:bg-slate-800 text-slate-500 px-2 py-0.5 rounded-full font-bold">TODO</span>
            </div>

            <div class="p-4 bg-slate-50 dark:bg-slate-900 border border-slate-100/50 dark:border-slate-850 rounded-2xl space-y-2">
                <div class="flex items-center gap-2 text-sm font-semibold text-slate-700 dark:text-slate-300">
                    <i class="bi bi-shield-lock-fill text-[#1155CC]"></i> Security Policies
                </div>
                <p class="text-xs text-slate-400">Manage API keys, rate limiters, session lifetimes, and CORS rules.</p>
                <span class="inline-block text-[10px] bg-slate-200 dark:bg-slate-800 text-slate-500 px-2 py-0.5 rounded-full font-bold">TODO</span>
            </div>

            <div class="p-4 bg-slate-50 dark:bg-slate-900 border border-slate-100/50 dark:border-slate-850 rounded-2xl space-y-2">
                <div class="flex items-center gap-2 text-sm font-semibold text-slate-700 dark:text-slate-300">
                    <i class="bi bi-database-fill text-[#1155CC]"></i> Backup & Sync
                </div>
                <p class="text-xs text-slate-400">Schedule automatic database dumps and media synchronization intervals.</p>
                <span class="inline-block text-[10px] bg-slate-200 dark:bg-slate-800 text-slate-500 px-2 py-0.5 rounded-full font-bold">TODO</span>
            </div>
        </div>
    </div>
</div>
@endsection
