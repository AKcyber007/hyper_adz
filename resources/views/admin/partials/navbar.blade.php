<header class="h-16 bg-white dark:bg-slate-800 border-b border-slate-200 dark:border-slate-700 flex items-center justify-between px-8 z-20">
    <!-- Left: Breadcrumbs/Title -->
    <div class="flex items-center gap-4">
        <h2 class="text-lg font-bold text-slate-800 dark:text-slate-100 flex items-center gap-2">
            @yield('page_title', 'Dashboard')
        </h2>
    </div>

    <!-- Right: User Dropdown / Actions -->
    <div class="flex items-center gap-6">
        <!-- Quick Actions / Notifications -->
        <div class="relative group">
            <button class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 relative transition-all" aria-label="Notifications">
                <i class="bi bi-bell text-xl"></i>
                @if(Auth::user()->unreadNotifications->count() > 0)
                    <span class="absolute -top-1 -right-1 w-3.5 h-3.5 bg-red-500 rounded-full text-[9px] font-bold text-white flex items-center justify-center border border-white">
                        {{ Auth::user()->unreadNotifications->count() }}
                    </span>
                @endif
            </button>
            <div class="absolute right-0 mt-2 w-80 bg-white border border-slate-200 rounded-2xl shadow-xl opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all z-50 overflow-hidden">
                <div class="p-4 border-b border-slate-100 flex justify-between items-center bg-slate-50">
                    <h4 class="text-xs font-bold text-slate-800">Notifications</h4>
                </div>
                <div class="max-h-64 overflow-y-auto">
                    @forelse(Auth::user()->unreadNotifications as $notification)
                        <div class="p-4 border-b border-slate-50 hover:bg-slate-50 transition-colors">
                            <p class="text-xs font-semibold text-slate-800">{{ $notification->data['message'] ?? 'New Notification' }}</p>
                            @if(isset($notification->data['amount']))
                                <p class="text-[10px] text-slate-500 mt-1">Amount: ₹{{ number_format($notification->data['amount'], 2) }}</p>
                            @endif
                            <span class="text-[9px] text-slate-400 mt-2 block">{{ $notification->created_at->diffForHumans() }}</span>
                        </div>
                    @empty
                        <div class="p-6 text-center text-xs text-slate-400">
                            No new notifications
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <div class="h-8 w-px bg-slate-200 dark:bg-slate-700"></div>

        <!-- Admin Profile dropdown/logout -->
        <div class="flex items-center gap-3">
            <div class="text-right">
                <span class="block text-sm font-semibold text-slate-700 dark:text-slate-200">{{ Auth::user()->name }}</span>
                <span class="block text-[10px] text-slate-400 font-bold uppercase">{{ Auth::user()->roles->first()?->name ?? 'Admin' }}</span>
            </div>
            
            <!-- User Avatar Initial -->
            <div class="w-9 h-9 rounded-xl bg-[#1155CC]/10 text-[#1155CC] font-bold text-sm flex items-center justify-center border border-[#1155CC]/20">
                {{ substr(Auth::user()->name, 0, 1) }}
            </div>

            <!-- Logout Form -->
            <form method="POST" action="{{ route('logout') }}" class="ml-2">
                @csrf
                <button type="submit" class="p-2 text-slate-400 hover:text-red-600 dark:hover:text-red-400 transition-all" title="Log Out" aria-label="Log Out">
                    <i class="bi bi-box-arrow-right text-lg"></i>
                </button>
            </form>
        </div>
    </div>
</header>
