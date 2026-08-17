@extends('admin.layouts.app', [
    'title' => 'Activity Logs | Hyper Adz Admin'
])

@section('content')
<div class="max-w-5xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex items-center gap-3">
        <a href="{{ route('admin.screens.index') }}" class="w-10 h-10 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 dark:bg-slate-800 dark:hover:bg-slate-700 dark:text-slate-300 flex items-center justify-center transition-all">
            <i class="bi bi-arrow-left text-lg"></i>
        </a>
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white flex items-center gap-2">
                <i class="bi bi-journal-text text-[#1155CC]"></i> Activity Logs
            </h1>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Audit log tracking modifications to screens inventory and location mapping details.</p>
        </div>
    </div>

    <!-- Logs List -->
    <div class="bg-white dark:bg-slate-800 rounded-3xl border border-slate-100 dark:border-slate-700/50 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full border-collapse text-left">
                <thead>
                    <tr class="border-b border-slate-100 dark:border-slate-750 bg-slate-50/50 dark:bg-slate-850/20">
                        <th class="px-6 py-4 text-xs font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider">User</th>
                        <th class="px-6 py-4 text-xs font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider">Action</th>
                        <th class="px-6 py-4 text-xs font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider">Log Details</th>
                        <th class="px-6 py-4 text-xs font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider">Date & Time</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-750">
                    @forelse($logs as $log)
                        <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-850/5 transition-all text-sm text-slate-700 dark:text-slate-350">
                            <!-- Operator User -->
                            <td class="px-6 py-4 shrink-0">
                                <div class="flex items-center gap-2.5">
                                    <div class="w-8 h-8 rounded-full bg-slate-100 dark:bg-slate-900 flex items-center justify-center font-bold text-slate-700 dark:text-slate-300">
                                        {{ $log->user ? strtoupper(substr($log->user->name, 0, 1)) : 'S' }}
                                    </div>
                                    <div>
                                        <span class="block font-semibold">{{ $log->user->name ?? 'System Seeder' }}</span>
                                        <span class="block text-[10px] text-slate-400">{{ $log->user->email ?? 'seeder@hyperadz.local' }}</span>
                                    </div>
                                </div>
                            </td>

                            <!-- Action type badge -->
                            <td class="px-6 py-4">
                                @if($log->action === 'created')
                                    <span class="inline-flex items-center px-2 py-0.5 text-xs font-bold rounded-md bg-emerald-50 text-emerald-700 dark:bg-emerald-950/20 dark:text-emerald-400">
                                        CREATED
                                    </span>
                                @elseif($log->action === 'updated')
                                    <span class="inline-flex items-center px-2 py-0.5 text-xs font-bold rounded-md bg-blue-50 text-[#1155CC] dark:bg-blue-950/20 dark:text-blue-400">
                                        UPDATED
                                    </span>
                                @elseif($log->action === 'deleted')
                                    <span class="inline-flex items-center px-2 py-0.5 text-xs font-bold rounded-md bg-red-50 text-red-700 dark:bg-red-950/20 dark:text-red-400">
                                        DELETED
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 text-xs font-bold rounded-md bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-400">
                                        {{ strtoupper($log->action) }}
                                    </span>
                                @endif
                            </td>

                            <!-- Description -->
                            <td class="px-6 py-4 font-medium max-w-xs sm:max-w-md truncate-2-lines text-xs text-slate-500 dark:text-slate-400">
                                {{ $log->description }}
                            </td>

                            <!-- Timestamp -->
                            <td class="px-6 py-4 font-mono text-[11px] text-slate-400">
                                <div>{{ $log->created_at->format('M d, Y h:i A') }}</div>
                                <div class="text-[10px] text-slate-500 mt-0.5 font-sans">{{ $log->created_at->diffForHumans() }}</div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-12 text-center text-slate-450 italic">
                                <div class="max-w-xs mx-auto space-y-2">
                                    <i class="bi bi-clock-history text-3xl text-slate-300"></i>
                                    <p class="font-bold text-slate-700 dark:text-slate-350">No activity logged yet</p>
                                    <p class="text-xs">Activities will appear here once screens are created or configured.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($logs->hasPages())
            <div class="px-6 py-4 border-t border-slate-100 dark:border-slate-750 bg-slate-50/30 dark:bg-slate-850/10">
                {{ $logs->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
