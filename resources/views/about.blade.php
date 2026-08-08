@extends('layouts.app', [
    'title' => 'About Hyper Adz | Hyperlocal Ad Network Coimbatore | Multitude Solutions',
    'description' => 'Learn about Hyper Adz — an indoor digital advertising network by Multitude Solutions connecting brands with high-intent local audiences through premium partner venues in Coimbatore.'
])

@section('content')
<div class="subpage-banner" style="background-image: url('{{ asset('images/about-banner.png') }}')">
    <div class="subpage-banner-overlay"></div>
    <div class="container text-center text-white position-relative" style="z-index: 5;">
        <span class="eyebrow" style="color: rgba(255, 255, 255, 0.9); background: rgba(255, 255, 255, 0.12); border: 1px solid rgba(255, 255, 255, 0.25);">Hyper Adz &nbsp;›&nbsp; About Us</span>
        <h1 class="text-white fw-bold mb-3" style="font-size: clamp(2.5rem, 5vw, 3.5rem); letter-spacing: -0.02em;">About Hyper Adz</h1>
        <p class="text-white-50 mx-auto mb-0" style="max-width: 600px; font-size: 1.1rem; opacity: 0.85;">An indoor media network designed for modern local advertising across Coimbatore.</p>
    </div>
</div>

<div class="bg-grid-pattern">
    <div class="gradient-blob gradient-blob-1"></div>

    <section class="section-pad pt-5">
        <div class="container">
            <div class="row align-items-center g-5 mb-5">
                <div class="col-lg-6" data-aos="fade-right">
                    <div class="about-image-wrapper">
                        <img src="{{ asset('images/about.png') }}" class="about-image" alt="Hyper Adz Media Network">
                    </div>
                </div>
                <div class="col-lg-6" data-aos="fade-left">
                    <div class="about-card">
                    <h2 class="mb-4">Our Company Story</h2>
                    <p class="mb-3">Hyper Adz is an indoor digital advertising network by <strong>Multitude Solutions</strong>, built to help local businesses reach high-intent audiences through premium partner locations. We connect advertisers, venue partners, and consumers through measurable, location-based media solutions.</p>
                    <p class="mb-4">We transform everyday partner locations — restaurants, cafes, gyms, salons, clinics, and retail stores — into high-value media spaces that work for both advertisers and venue owners.</p>
                    
                    <div class="row g-3">
                        <div class="col-sm-6">
                            <div class="value-box p-3 m-0">
                                <i class="bi bi-eye fs-4"></i>
                                <div class="mt-2">
                                    <h4 class="h6">Our Vision</h4>
                                    <p class="small text-muted mb-0">Build the most trusted and impactful Hyper Local advertising ecosystem in Tamil Nadu, connecting brands with high-intent audiences through intelligent digital visibility.</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="value-box p-3 m-0">
                                <i class="bi bi-bullseye fs-4"></i>
                                <div class="mt-2">
                                    <h4 class="h6">Our Mission</h4>
                                    <p class="small text-muted mb-0">Help businesses grow by delivering location-based indoor advertising solutions while creating value for advertisers, venue partners, and consumers through measurable reach.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                </div>
            </div>

            <div class="mt-5">
                <x-section-header eyebrow="Roadmap" title="Our growth and future directions" text="A look at the phases of development as we scale our premium display network." align="center" />
                
                <div class="timeline">
                    @foreach([
                        'Foundation' => ['01', 'Premium screen network and signage setup across local retail environments.'],
                        'Expansion' => ['02', 'Scaling mapping components and preparing categorised location chips.'],
                        'Platform' => ['03', 'Future booking automation, analytics reporting, and customer console.']
                    ] as $step => [$num, $copy])
                        <div class="timeline-step" data-aos="fade-up">
                            <span class="eyebrow mb-2">Phase {{ $num }}</span>
                            <strong>{{ $step }}</strong>
                            <p>{{ $copy }}</p>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </section>
</div>

<x-cta title="Build visibility in places people already visit." text="Talk to Hyper Adz about your market, audience, and campaign timing." />
@endsection
