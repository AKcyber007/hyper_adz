@php
    $title = $title ?? 'Hyper Adz | Indoor Digital Advertising in Coimbatore';
    $description = $description ?? 'Hyper Adz helps brands run indoor digital advertising campaigns across premium screens, malls, cafes, gyms, apartments, theatres, and retail locations.';
    $canonical = url()->current();
@endphp
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title }}</title>
    <meta name="description" content="{{ $description }}">
    <link rel="canonical" href="{{ $canonical }}">
    <meta property="og:title" content="{{ $title }}">
    <meta property="og:description" content="{{ $description }}">
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ $canonical }}">
    <meta property="og:image" content="{{ asset('images/hyperadz-banner-logo.svg') }}">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $title }}">
    <meta name="twitter:description" content="{{ $description }}">
    <link rel="icon" href="{{ asset('images/favicon.svg') }}" type="image/svg+xml">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,300..0,800;1,300..1,800&family=Sora:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    @stack('styles')
    <link href="{{ asset('css/hyperadz.css') }}" rel="stylesheet">
    <script type="application/ld+json">
        {!! json_encode([
            '@context' => 'https://schema.org',
            '@type' => 'Organization',
            'name' => 'Hyper Adz',
            'url' => url('/'),
            'logo' => asset('images/hyperadz-logo-square.svg'),
            'email' => 'connect.hyperadz@gmail.com',
            'telephone' => '+91 99620 99110',
            'address' => [
                '@type' => 'PostalAddress',
                'streetAddress' => 'Ganapathy',
                'addressLocality' => 'Coimbatore',
                'postalCode' => '641006',
                'addressCountry' => 'IN',
            ],
        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) !!}
    </script>
</head>
<body>
    @include('partials.navbar')
    <main>
        @yield('content')
    </main>
    @include('partials.footer')
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/gsap.min.js"></script>
    @stack('scripts')
    <script src="{{ asset('js/hyperadz.js') }}"></script>

    {{-- WhatsApp Floating CTA --}}
    <a href="https://wa.me/919962099110?text=Hi%20Hyper%20Adz%2C%20I%27d%20like%20to%20know%20more%20about%20your%20indoor%20advertising%20solutions." 
       target="_blank" rel="noopener" class="wa-float" aria-label="Chat on WhatsApp">
        <i class="bi bi-whatsapp"></i>
        <span>Chat with us</span>
    </a>
</body>
</html>
