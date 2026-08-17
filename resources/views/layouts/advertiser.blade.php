<!DOCTYPE html>
<html lang="en" class="h-full bg-slate-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Advertiser Dashboard') | Hyper Adz</title>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <!-- Tailwind CSS -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body { font-family: 'Outfit', sans-serif; }
    </style>
    @stack('styles')
</head>
<body class="min-h-full flex bg-slate-50 text-slate-800 overflow-x-hidden relative">

    @php
        $user = Auth::guard('advertiser')->user();
        $profile = \App\Models\AdvertiserProfile::where('user_id', $user->id)->first();
    @endphp

    <!-- Sidebar Navigation -->
    <aside class="w-64 border-r border-slate-800 bg-slate-900 hidden md:flex flex-col shrink-0 sticky top-0 h-screen z-20">
        <div class="p-6 border-b border-slate-800 flex items-center gap-3">
            <div class="w-9 h-9 rounded-xl bg-gradient-to-tr from-blue-650 to-indigo-600 flex items-center justify-center text-white text-md shadow-lg shadow-indigo-500/20">
                <i class="bi bi-megaphone-fill"></i>
            </div>
            <div>
                <span class="block text-sm font-extrabold tracking-tight text-white">Hyper Adz</span>
                <span class="block text-[10px] text-slate-400 font-bold uppercase tracking-widest">Advertiser Portal</span>
            </div>
        </div>

        <nav class="flex-grow p-4 space-y-1.5 overflow-y-auto">
            <a href="{{ route('advertiser.dashboard') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-xs font-bold transition-all {{ request()->routeIs('advertiser.dashboard') ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-600/20' : 'text-slate-400 hover:text-white hover:bg-slate-800/60' }}">
                <i class="bi bi-grid-fill text-sm"></i> Dashboard
            </a>
            <a href="{{ route('advertiser.map') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-xs font-bold transition-all {{ request()->routeIs('advertiser.map') ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-600/20' : 'text-slate-400 hover:text-white hover:bg-slate-800/60' }}">
                <i class="bi bi-map-fill text-sm"></i> Locations / Networks
            </a>
            <a href="{{ route('advertiser.my-requests') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-xs font-bold transition-all {{ request()->routeIs('advertiser.my-requests') || request()->routeIs('advertiser.my-requests.create') ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-600/20' : 'text-slate-400 hover:text-white hover:bg-slate-800/60' }}">
                <i class="bi bi-file-earmark-play-fill text-sm"></i> Campaigns & Reports
            </a>
            <a href="{{ route('advertiser.profile') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-xs font-bold transition-all {{ request()->routeIs('advertiser.profile') ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-600/20' : 'text-slate-400 hover:text-white hover:bg-slate-800/60' }}">
                <i class="bi bi-person-vcard-fill text-sm"></i> Company Profile
            </a>
        </nav>

        <div class="p-4 border-t border-slate-800 bg-slate-950/40">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-slate-800 border border-slate-700 flex items-center justify-center text-slate-350 text-xs shrink-0 font-extrabold uppercase">
                    {{ substr($profile->contact_person ?? 'A', 0, 1) }}
                </div>
                <div class="min-w-0 flex-grow">
                    <span class="block text-xs font-bold text-white truncate">{{ $profile->contact_person }}</span>
                    <span class="block text-[10px] text-slate-500 truncate font-mono mt-0.5">{{ $profile->advertiser_code }}</span>
                </div>
            </div>
        </div>
    </aside>

    <!-- Content Area Container -->
    <div class="flex-grow flex flex-col min-w-0">
        <!-- Header -->
        <header class="border-b border-slate-200 bg-white/80 backdrop-blur-md sticky top-0 z-30 px-6 py-4 flex items-center justify-between">
            <!-- Mobile Menu Trigger -->
            <button class="md:hidden text-slate-600 text-xl" onclick="document.getElementById('mobile-sidebar').classList.toggle('hidden')">
                <i class="bi bi-list"></i>
            </button>

            <div class="flex items-center gap-2">
                <span class="text-sm font-extrabold text-slate-950 hidden md:block">Advertiser Operations Hub</span>
                <span class="text-sm font-extrabold text-slate-955 md:hidden">Hyper Adz Client</span>
            </div>

            <!-- Sign Out -->
            <div class="flex items-center gap-4">
                <form method="POST" action="{{ route('advertiser.logout') }}">
                    @csrf
                    <button type="submit" class="px-4 py-2 rounded-xl bg-slate-100 hover:bg-rose-50 text-xs font-bold text-slate-600 hover:text-rose-600 border border-slate-200 flex items-center gap-2 transition-all" title="Sign Out">
                        <i class="bi bi-box-arrow-right"></i>
                        <span>Logout</span>
                    </button>
                </form>
            </div>
        </header>

        <!-- Mobile Sidebar Overlay (Hidden by Default) -->
        <div id="mobile-sidebar" class="fixed inset-0 bg-slate-950/80 backdrop-blur-sm z-50 hidden md:hidden" onclick="this.classList.add('hidden')">
            <div class="w-64 bg-slate-900 h-full flex flex-col" onclick="event.stopPropagation()">
                <div class="p-6 border-b border-slate-850 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-lg bg-indigo-600 flex items-center justify-center text-white text-md">
                            <i class="bi bi-megaphone-fill"></i>
                        </div>
                        <span class="text-sm font-extrabold text-white">Hyper Adz</span>
                    </div>
                    <button class="text-slate-400 hover:text-white text-lg" onclick="document.getElementById('mobile-sidebar').classList.add('hidden')">
                        <i class="bi bi-x"></i>
                    </button>
                </div>
                <nav class="flex-grow p-4 space-y-1.5 overflow-y-auto">
                    <a href="{{ route('advertiser.dashboard') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-xs font-bold {{ request()->routeIs('advertiser.dashboard') ? 'bg-indigo-600 text-white' : 'text-slate-400' }}">
                        <i class="bi bi-grid-fill"></i> Dashboard
                    </a>
                    <a href="{{ route('advertiser.map') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-xs font-bold {{ request()->routeIs('advertiser.map') ? 'bg-indigo-600 text-white' : 'text-slate-400' }}">
                        <i class="bi bi-map-fill"></i> Locations / Networks
                    </a>
                    <a href="{{ route('advertiser.my-requests') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-xs font-bold {{ request()->routeIs('advertiser.my-requests') || request()->routeIs('advertiser.my-requests.create') ? 'bg-indigo-600 text-white' : 'text-slate-400' }}">
                        <i class="bi bi-file-earmark-play-fill"></i> Campaigns & Reports
                    </a>
                    <a href="{{ route('advertiser.profile') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-xs font-bold {{ request()->routeIs('advertiser.profile') ? 'bg-indigo-600 text-white' : 'text-slate-400' }}">
                        <i class="bi bi-person-vcard-fill"></i> Profile
                    </a>
                </nav>
            </div>
        </div>

        <!-- Main Workspace -->
        <main class="flex-grow p-6 sm:p-8 space-y-8 relative z-10 overflow-y-auto">
            <!-- Toast & Session Alerts -->
            @if(session('success'))
                <div class="bg-emerald-500/10 border border-emerald-500/20 rounded-2xl p-4 flex items-center gap-3 text-emerald-400 text-xs font-semibold animate-pulse">
                    <i class="bi bi-check-circle-fill text-lg"></i>
                    <span>{{ session('success') }}</span>
                </div>
            @endif
            @if(session('error'))
                <div class="bg-rose-500/10 border border-rose-500/20 rounded-2xl p-4 flex items-center gap-3 text-rose-400 text-xs font-semibold">
                    <i class="bi bi-exclamation-triangle-fill text-lg"></i>
                    <span>{{ session('error') }}</span>
                </div>
            @endif

            @yield('content')
        </main>

        <!-- Footer -->
        <footer class="border-t border-slate-200 bg-white py-6 text-center text-[10px] text-slate-400">
            <p>&copy; {{ date('Y') }} Hyper Adz. All rights reserved.</p>
        </footer>
    </div>

    @stack('scripts')
</body>
</html>
