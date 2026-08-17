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
            <!-- Advertiser Services -->
            <h2 class="h4 fw-bold mb-4" style="color: #0b1c3f;">For Advertisers</h2>
            <div class="row g-4 mb-5">
                <div class="col-md-6 col-xl-6">
                    <x-service-card icon="bi-badge-ad" title="Indoor Digital Advertising" text="Place campaigns on indoor screens across high-footfall venues and retail environments." label="Start Advertising" link="{{ route('contact') }}" />
                </div>
                <div class="col-md-6 col-xl-6">
                    <x-service-card icon="bi-megaphone" title="Targeted Brand Promotion" text="Create memorable visibility for your brand in audience-rich indoor spaces." label="Plan Campaign" link="{{ route('contact') }}" />
                </div>
            </div>

            <!-- Location Partner Services -->
            <h2 class="h4 fw-bold mb-4" style="color: #0b1c3f;">For Location Partners</h2>
            <div class="row g-4 mb-5">
                <div class="col-md-6 col-xl-6">
                    <x-service-card icon="bi-building-up" title="Host a Screen for Passive Income" text="Turn your venue's footfall into passive income by hosting our premium displays." label="Become a Partner" link="{{ route('contact', ['form' => 'partner']) }}" />
                </div>
                <div class="col-md-6 col-xl-6">
                    <x-service-card icon="bi-cloud-check" title="Managed Screen Network" text="We install, maintain, and manage the cloud-connected screens at zero cost to you." label="Learn More" link="{{ route('contact', ['form' => 'partner']) }}" />
                </div>
            </div>

            <!-- Digital Signage Buyer Services -->
            <h2 class="h4 fw-bold mb-4" style="color: #0b1c3f;">For Digital Signage Buyers</h2>
            <div class="row g-4 mb-5">
                <div class="col-md-6 col-xl-6">
                    <x-service-card icon="bi-display" title="Digital Signage Sales" text="Source modern commercial display systems for your own business communications." label="Get a Quote" link="{{ route('enquiry') }}" />
                </div>
                <div class="col-md-6 col-xl-6">
                    <x-service-card icon="bi-calendar2-event" title="Digital Signage Rental" text="Rent screens for product launches, activations, events, and seasonal promotions." label="Enquire Now" link="{{ route('enquiry') }}" />
                </div>
            </div>

            <!-- Sales Partner Services -->
            <h2 class="h4 fw-bold mb-4" style="color: #0b1c3f;">For Sales Partners</h2>
            <div class="row g-4">
                <div class="col-md-6 col-xl-6">
                    <x-service-card icon="bi-person-lines-fill" title="Advertiser Sourcing" text="Bring in advertisers to our platform and earn a recurring commission on campaigns." label="Partner With Us" link="{{ route('enquiry') }}" />
                </div>
                <div class="col-md-6 col-xl-6">
                    <x-service-card icon="bi-shop" title="Location Sourcing" text="Help us expand our network by onboarding new premium venues and earn incentives." label="Start Earning" link="{{ route('enquiry') }}" />
                </div>
            </div>
        </div>
    </section>
</div>

<x-cta title="Plan your next indoor digital campaign." text="Share your campaign goal and Hyper Adz will help identify the right locations, formats, and rollout plan." />
@endsection
