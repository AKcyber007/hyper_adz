<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify OTP | Advertiser | Hyper Adz</title>
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
    <!-- Glow Backdrops -->
    <div class="absolute w-[500px] h-[500px] rounded-full bg-blue-600/10 blur-[120px] -top-40 -left-40 animate-pulse duration-[6000ms]"></div>
    <div class="absolute w-[500px] h-[500px] rounded-full bg-indigo-600/10 blur-[120px] -bottom-40 -right-40 animate-pulse duration-[8000ms]"></div>

    <div class="w-full max-w-md relative z-10 space-y-6">
        <!-- Header -->
        <div class="text-center space-y-2">
            <h1 class="text-2xl font-extrabold text-white tracking-tight">Verify Identity</h1>
            <p class="text-xs text-slate-450 uppercase font-bold tracking-widest">Advertiser Portal Verification</p>
        </div>

        <!-- Verification Card -->
        <div class="bg-slate-900/50 backdrop-blur-xl border border-slate-800/60 rounded-[32px] p-6 sm:p-8 shadow-2xl space-y-6">
            <div class="space-y-1">
                <h2 class="text-lg font-bold text-white">Enter Security Code</h2>
                <p class="text-xs text-slate-400">A 6-digit one-time passcode was dispatched to your email linked to <strong class="text-slate-205 font-mono">{{ $phone }}</strong>.</p>
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

            <form method="POST" action="{{ route('advertiser.login.verify.post') }}" class="space-y-4">
                @csrf
                <input type="hidden" name="phone" value="{{ $phone }}">

                <div class="space-y-1.5">
                    <label for="otp_code" class="text-[10px] font-bold text-slate-400 uppercase block tracking-wider">Verification Passcode</label>
                    <div class="relative">
                        <i class="bi bi-shield-fill-check absolute left-4 top-3.5 text-slate-500 text-sm"></i>
                        <input type="text" name="otp_code" id="otp_code" required autofocus maxlength="6" placeholder="e.g. 582194" class="w-full pl-11 pr-4 py-3 bg-slate-950 border border-slate-850 rounded-2xl text-sm text-white focus:outline-none focus:border-blue-600 focus:ring-1 focus:ring-blue-600 transition-all font-mono tracking-[4px] text-center text-base">
                    </div>
                    <span class="block text-[9px] text-slate-500">The passcode expires in 10 minutes. Maximum 5 validation retries allowed.</span>
                </div>

                <button type="submit" class="w-full py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-2xl text-xs font-bold transition-all shadow-lg shadow-blue-500/10 flex items-center justify-center gap-2">
                    <i class="bi bi-check-lg"></i> Verify Code & Log In
                </button>
            </form>

            <div class="flex items-center justify-between text-[10px] pt-2">
                <a href="{{ route('advertiser.login') }}" class="font-semibold text-slate-450 hover:text-white transition-all">
                    <i class="bi bi-chevron-left"></i> Use a different phone
                </a>
            </div>
        </div>
    </div>
</body>
</html>
