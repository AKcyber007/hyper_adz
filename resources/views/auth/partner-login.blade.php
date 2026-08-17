<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Partner Login | Hyper Adz</title>
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
    </style>
</head>
<body class="h-full bg-slate-950 flex items-center justify-center p-4 relative overflow-hidden select-none">
    <!-- Glowing Backdrops -->
    <div class="absolute w-[500px] h-[500px] rounded-full bg-blue-600/10 blur-[120px] -top-40 -left-40 animate-pulse duration-[6000ms]"></div>
    <div class="absolute w-[500px] h-[500px] rounded-full bg-indigo-600/10 blur-[120px] -bottom-40 -right-40 animate-pulse duration-[8000ms]"></div>

    <div class="w-full max-w-md relative z-10 space-y-6">
        <!-- Logo -->
        <div class="text-center space-y-2">
            <div class="inline-flex w-14 h-14 rounded-2xl bg-gradient-to-tr from-blue-600 to-indigo-650 items-center justify-center text-white text-2xl shadow-xl shadow-blue-500/10">
                <i class="bi bi-building-fill-check"></i>
            </div>
            <h1 class="text-2xl font-extrabold text-white tracking-tight">Hyper Adz</h1>
            <p class="text-xs text-slate-450 uppercase font-bold tracking-widest">Venue Partner Portal Access</p>
        </div>

        <!-- Glassmorphism Card -->
        <div class="bg-slate-900/50 backdrop-blur-xl border border-slate-800/60 rounded-[32px] p-6 sm:p-8 shadow-2xl space-y-6">
            <div class="space-y-1">
                <h2 class="text-lg font-bold text-white">Login to Account</h2>
                <p class="text-xs text-slate-400">Enter your registered mobile phone number. A secure one-time passcode (OTP) will be sent to your registered Email Address.</p>
            </div>

            <!-- Alerts -->
            @if(session('error'))
                <div class="p-3 bg-red-500/10 border border-red-500/20 text-red-400 text-xs font-semibold rounded-xl flex items-center gap-2">
                    <i class="bi bi-exclamation-triangle-fill"></i>
                    <span>{{ session('error') }}</span>
                </div>
            @endif

            @if(session('success'))
                <div class="p-3 bg-emerald-500/10 border border-emerald-500/20 text-emerald-450 text-xs font-semibold rounded-xl flex items-center gap-2">
                    <i class="bi bi-check-circle-fill"></i>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            <form method="POST" action="{{ route('partner.login.post') }}" class="space-y-4">
                @csrf
                <div class="space-y-1.5">
                    <label for="phone" class="text-[10px] font-bold text-slate-400 uppercase block tracking-wider">Phone Number</label>
                    <div class="relative">
                        <i class="bi bi-telephone-fill absolute left-4 top-1/2 -translate-y-1/2 text-slate-500 text-xs"></i>
                        <input type="tel" id="phone" name="phone" value="{{ old('phone') }}" required placeholder="e.g. 9876543210" class="w-full pl-10 pr-4 py-3 bg-slate-950/50 border border-slate-700/50 rounded-xl text-sm text-white placeholder-slate-600 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-all font-mono">
                    </div>
                </div>

                <div class="flex items-center">
                    <input id="remember" name="remember" type="checkbox" class="h-4 w-4 rounded border-slate-700/50 bg-slate-950/50 text-blue-600 focus:ring-blue-600 focus:ring-offset-slate-900">
                    <label for="remember" class="ml-2 block text-xs text-slate-400 font-medium">
                        Remember me on this device
                    </label>
                </div>

                <button type="submit" class="w-full py-3 bg-blue-600 hover:bg-blue-500 text-white text-sm font-bold rounded-xl transition-all shadow-lg shadow-blue-600/20 flex items-center justify-center gap-2">
                    <i class="bi bi-shield-lock-fill"></i> Request OTP Passcode
                </button>
            </form>

            <div class="text-center pt-2">
                <a href="/" class="text-[10px] font-semibold text-slate-450 hover:text-white transition-all flex items-center justify-center gap-1.5">
                    <i class="bi bi-arrow-left"></i> Return to main site
                </a>
            </div>
        </div>
    </div>
</body>
</html>
