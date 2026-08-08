@extends('layouts.app', [
    'title' => 'DOOH Advertising Coimbatore | Indoor Digital Signage Services | Hyper Adz',
    'description' => 'Indoor digital advertising, DOOH placements, screen rental, and campaign management across Coimbatore. Advertise in gyms, cafes, salons, clinics and more — Hyper Adz.'
])

@section('content')
<div class="subpage-banner" style="background-image: url('{{ asset('images/services-banner.png') }}')">
    <div class="subpage-banner-overlay"></div>
    <div class="container text-center text-white position-relative" style="z-index: 5;">
        <span class="eyebrow" style="color: rgba(255, 255, 255, 0.9); background: rgba(255, 255, 255, 0.12); border: 1px solid rgba(255, 255, 255, 0.25);">Hyper Adz &nbsp;›&nbsp; Our Services</span>
        <h1 class="text-white fw-bold mb-3" style="font-size: clamp(2.5rem, 5vw, 3.5rem); letter-spacing: -0.02em;">Our Services</h1>
        <p class="text-white-50 mx-auto mb-0" style="max-width: 600px; font-size: 1.1rem; opacity: 0.85;">Digital signage services tailored for campaigns, venues, and modern brands.</p>
    </div>
</div>

<div class="bg-grid-pattern">
    <div class="gradient-blob gradient-blob-1"></div>
    <div class="gradient-blob gradient-blob-2"></div>

    <section class="section-pad pt-5">
        <div class="container">
            <div class="row g-4">
                @foreach([
                    ['bi-badge-ad', 'Indoor Digital Advertising', 'Place campaigns on indoor screens across high-footfall venues and retail environments.'],
                    ['bi-display', 'Digital Signage Sales', 'Source modern commercial display systems for businesses and venue partners.'],
                    ['bi-calendar2-event', 'Digital Signage Rental', 'Rent screens for product launches, activations, events, and seasonal promotions.'],
                    ['bi-bar-chart-line', 'Campaign Analytics', 'Prepare campaigns for clear performance reporting, summaries, and geo tagged validation.'],
                    ['bi-cloud-check', 'Cloud Connected Displays', 'Plan screen networks that can be updated and managed centrally.'],
                    ['bi-megaphone', 'Brand Promotion', 'Create memorable visibility for brands in audience-rich indoor spaces.']
                ] as [$icon, $title, $text])
                    <div class="col-md-6 col-xl-4">
                        <x-service-card :icon="$icon" :title="$title" :text="$text" label="Request Plan" />
                    </div>
                @endforeach
            </div>
        </div>
    </section>
</div>

<x-cta title="Plan your next indoor digital campaign." text="Share your campaign goal and Hyper Adz will help identify the right locations, formats, and rollout plan." />
@endsection
