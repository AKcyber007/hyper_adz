<nav class="navbar navbar-expand-lg site-nav fixed-top" aria-label="Primary navigation">
    <div class="container">
        <a class="navbar-brand" href="{{ route('home') }}" aria-label="Hyper Adz home">
            <img src="{{ asset('images/hyperadz-banner-logo.svg') }}" alt="Hyper Adz">
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#siteNav" aria-controls="siteNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="siteNav">
            <ul class="navbar-nav ms-auto align-items-lg-center">
                <li class="nav-item"><a class="nav-link {{ request()->routeIs('about') ? 'active' : '' }}" href="{{ route('about') }}">About</a></li>
                <li class="nav-item"><a class="nav-link {{ request()->routeIs('services') ? 'active' : '' }}" href="{{ route('services') }}">Services</a></li>
                <li class="nav-item"><a class="nav-link {{ request()->routeIs('network') ? 'active' : '' }}" href="{{ route('network') }}">Network</a></li>
                <li class="nav-item"><a class="nav-link {{ request()->routeIs('why') ? 'active' : '' }}" href="{{ route('why') }}">Why Hyper Adz</a></li>
                <li class="nav-item"><a class="nav-link {{ request()->routeIs('contact') ? 'active' : '' }}" href="{{ route('contact') }}">Contact</a></li>
                <li class="nav-item ms-lg-3"><a class="btn btn-ghost" href="{{ route('login') }}">Login</a></li>
                <li class="nav-item ms-lg-2"><a class="btn btn-primary nav-cta" href="{{ route('contact', ['form' => 'advertiser']) }}">Advertise with Us</a></li>
            </ul>
        </div>
    </div>
</nav>
