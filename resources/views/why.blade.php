@extends('layouts.app', [
    'title' => 'Why Hyper Adz | Premium Indoor Digital Network',
    'description' => 'See why brands choose Hyper Adz for indoor digital advertising, premium locations, transparent pricing, and client satisfaction.'
])

@section('content')
<div class="subpage-banner" style="background-image: url('{{ asset('images/why-banner.png') }}')">
    <div class="subpage-banner-overlay"></div>
    <div class="container text-center text-white position-relative" style="z-index: 5;">
        <span class="eyebrow" style="color: rgba(255, 255, 255, 0.9); background: rgba(255, 255, 255, 0.12); border: 1px solid rgba(255, 255, 255, 0.25);">Hyper Adz &nbsp;›&nbsp; Why Choose Us</span>
        <h1 class="text-white fw-bold mb-3" style="font-size: clamp(2.5rem, 5vw, 3.5rem); letter-spacing: -0.02em;">Why Hyper Adz</h1>
        <p class="text-white-50 mx-auto mb-0" style="max-width: 600px; font-size: 1.1rem; opacity: 0.85;">Premium indoor display media, transparent campaign analytics, and guaranteed delivery.</p>
    </div>
</div>

<div class="bg-grid-pattern">
    <div class="gradient-blob gradient-blob-1"></div>
    <div class="gradient-blob gradient-blob-2"></div>

    <section class="section-pad pt-5">
        <div class="container">
            <div class="why-grid">
                @foreach([
                    ['01', 'bi-broadcast-pin', 'Largest Indoor Network', 'Scale your campaign across high-intent indoor environments.'],
                    ['02', 'bi-display', '300+ Smart Screens', 'High definition commercial displays placed at optimal visual angles.'],
                    ['03', 'bi-geo-alt', 'Geo Tagged Reports', 'Get location-aware campaign validation and transparent delivery logs.'],
                    ['04', 'bi-gem', 'Premium Locations', 'Target customers where they spend their leisure time: cafes, malls, gyms.'],
                    ['05', 'bi-receipt', 'Transparent Pricing', 'Flexible slots built on clear rates per screen category.'],
                    ['06', 'bi-shield-check', 'Ethical Business', 'Commitments you can trust with verified screen uptimes.'],
                    ['07', 'bi-heart', 'Complete Client Satisfaction', 'Service designed around planning support, creative approval, and campaign reports.']
                ] as [$num, $icon, $title, $copy])
                    <article class="why-card" data-aos="fade-up">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <i class="bi {{ $icon }}"></i>
                            <span class="display-6 font-monospace text-light fw-bold" style="user-select: none;">{{ $num }}</span>
                        </div>
                        <h3>{{ $title }}</h3>
                        <p>{{ $copy }}</p>
                    </article>
                @endforeach
            </div>
        </div>
    </section>
</div>

<x-cta title="Plan your next campaign with us." text="Speak with one of our media network experts to design a campaign tailored to your business." />
@endsection
