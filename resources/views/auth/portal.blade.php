<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Access Portal | Hyper Adz</title>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <!-- Tailwind CSS -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body {
            font-family: 'Outfit', sans-serif;
        }
        .portal-card {
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .portal-card:hover {
            transform: translateY(-8px);
            border-color: rgba(59, 130, 246, 0.5) !important;
            box-shadow: 0 20px 40px rgba(10, 102, 255, 0.15) !important;
        }
    </style>
</head>
<body class="min-h-full bg-slate-950 flex flex-col justify-between p-4 sm:p-6 relative overflow-x-hidden text-slate-100 select-none">
    <!-- Premium Backdrop Glows -->
    <div class="absolute w-[600px] h-[600px] rounded-full bg-blue-600/5 blur-[120px] -top-80 -left-60 pointer-events-none"></div>
    <div class="absolute w-[600px] h-[600px] rounded-full bg-indigo-600/5 blur-[120px] bottom-0 right-0 pointer-events-none"></div>

    <!-- Header Logo -->
    <header class="w-full max-w-7xl mx-auto py-6 flex items-center justify-between relative z-10">
        <a href="/" class="flex items-center gap-3 no-underline">
            <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-blue-600 to-indigo-650 flex items-center justify-center text-white text-lg">
                <i class="bi bi-display-fill"></i>
            </div>
            <div>
                <span class="block text-sm font-extrabold tracking-tight text-white">Hyper Adz</span>
                <span class="block text-[9px] text-slate-400 font-bold uppercase tracking-widest">DOOH Network Coimbatore</span>
            </div>
        </a>
        <a href="/" class="text-xs font-semibold text-slate-400 hover:text-white transition-all flex items-center gap-1">
            <i class="bi bi-arrow-left"></i> Main Site
        </a>
    </header>

    <!-- Main Workspace Selector -->
    <main class="w-full max-w-6xl mx-auto py-12 sm:py-16 relative z-10 flex-grow flex flex-col justify-center">
        <div class="text-center max-w-2xl mx-auto mb-12 sm:mb-16 space-y-3">
            <span class="px-3 py-1 bg-blue-500/10 text-blue-450 border border-blue-500/20 rounded-full text-[10px] font-bold uppercase tracking-wider">
                Hyper Adz Portals
            </span>
            <h1 class="text-3xl sm:text-4xl font-extrabold text-white tracking-tight">Select Access Gateway</h1>
            <p class="text-xs sm:text-sm text-slate-400">Choose the appropriate workspace below to sign in and manage your campaigns, display locations, or system console operations.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 lg:gap-8">
            <!-- Card 1: Advertiser Portal -->
            <div class="portal-card bg-slate-900/40 backdrop-blur-xl border border-slate-800/80 rounded-[32px] p-6 sm:p-8 flex flex-col justify-between space-y-6">
                <div class="space-y-4">
                    <div class="w-12 h-12 rounded-2xl bg-blue-600/10 border border-blue-500/20 flex items-center justify-center text-blue-450 text-xl">
                        <i class="bi bi-megaphone-fill"></i>
                    </div>
                    <div class="space-y-1.5">
                        <h3 class="text-lg font-bold text-white">Advertiser Portal</h3>
                        <p class="text-xs text-slate-400 leading-relaxed">Launch DOOH advertisements, target premium screens, submit enquiries, and track active campaign performance indicators.</p>
                    </div>
                </div>
                <a href="{{ route('advertiser.login') }}" class="w-full py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-2xl text-xs font-bold transition-all text-center flex items-center justify-center gap-2 shadow-lg shadow-blue-500/5">
                    Enter Advertiser Portal <i class="bi bi-chevron-right text-[10px]"></i>
                </a>
            </div>

            <!-- Card 2: Partner Portal -->
            <div class="portal-card bg-slate-900/40 backdrop-blur-xl border border-slate-800/80 rounded-[32px] p-6 sm:p-8 flex flex-col justify-between space-y-6">
                <div class="space-y-4">
                    <div class="w-12 h-12 rounded-2xl bg-indigo-600/10 border border-indigo-500/20 flex items-center justify-center text-indigo-450 text-xl">
                        <i class="bi bi-building-fill-check"></i>
                    </div>
                    <div class="space-y-1.5">
                        <h3 class="text-lg font-bold text-white">Location Partner Portal</h3>
                        <p class="text-xs text-slate-400 leading-relaxed">Manage your host venue listings, track active display screen health, and monitor network monetization metrics.</p>
                    </div>
                </div>
                <a href="{{ route('partner.login') }}" class="w-full py-3 bg-indigo-600 hover:bg-indigo-700 text-white rounded-2xl text-xs font-bold transition-all text-center flex items-center justify-center gap-2 shadow-lg shadow-indigo-500/5">
                    Enter Partner Portal <i class="bi bi-chevron-right text-[10px]"></i>
                </a>
            </div>

            <!-- Card 3: Admin Console -->
            <div class="portal-card bg-slate-900/40 backdrop-blur-xl border border-slate-800/80 rounded-[32px] p-6 sm:p-8 flex flex-col justify-between space-y-6">
                <div class="space-y-4">
                    <div class="w-12 h-12 rounded-2xl bg-slate-800 border border-slate-700 flex items-center justify-center text-slate-300 text-xl">
                        <i class="bi bi-shield-lock-fill"></i>
                    </div>
                    <div class="space-y-1.5">
                        <h3 class="text-lg font-bold text-white">Admin Console</h3>
                        <p class="text-xs text-slate-400 leading-relaxed">Secure environment for system administrators to manage inventory, assign permissions, and oversee approvals.</p>
                    </div>
                </div>
                <a href="{{ route('admin.login') }}" class="w-full py-3 bg-slate-800 hover:bg-slate-700 text-slate-200 hover:text-white rounded-2xl text-xs font-bold transition-all text-center flex items-center justify-center gap-2 border border-slate-700">
                    Access Admin Console <i class="bi bi-chevron-right text-[10px]"></i>
                </a>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="w-full max-w-7xl mx-auto py-6 border-t border-slate-900/60 text-center text-[10px] text-slate-500 relative z-10">
        <p>&copy; {{ date('Y') }} Hyper Adz. All rights reserved.</p>
    </footer>
</body>
</html>
