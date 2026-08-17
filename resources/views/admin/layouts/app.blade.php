<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-slate-50 dark:bg-slate-900">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? 'Hyper Adz Admin' }}</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Sora:wght@400;600;700;800&display=swap" rel="stylesheet">

    <!-- Bootstrap Icons for Sidebar / Admin Portal -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

    <!-- Scripts and CSS -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Page-specific styles injected by child views -->
    @stack('styles')

    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }
        h1, h2, h3, h4, h5, h6 {
            font-family: 'Sora', sans-serif;
        }
    </style>
</head>
<body class="h-full font-sans antialiased text-slate-900 dark:text-slate-100">
    <div class="flex min-h-screen">
        <!-- Sidebar Navigation -->
        @include('admin.partials.sidebar')

        <!-- Main Panel -->
        <div class="flex flex-col flex-1 min-w-0 pl-64">
            <!-- Top Navbar -->
            @include('admin.partials.navbar')

            <!-- Page Content -->
            <main class="flex-1 p-6 md:p-8">
                @yield('content')
            </main>

            <!-- Footer -->
            @include('admin.partials.footer')
        </div>
    </div>

    <!-- Page-specific scripts injected by child views -->
    @stack('scripts')
</body>
</html>
