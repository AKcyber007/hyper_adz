@extends('layouts.app', [
    'title' => 'Ad Space Coimbatore | Indoor Advertising Screen Locations | Hyper Adz Network',
    'description' => 'Explore the Hyper Adz indoor advertising network — premium ad screen locations in restaurants, gyms, salons, medical stores and more across Coimbatore. Book ad space today.'
])

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
@endpush

@section('content')
<div class="subpage-banner" style="background-image: url('{{ asset('images/network-banner.png') }}')">
    <div class="subpage-banner-overlay"></div>
    <div class="container text-center text-white position-relative" style="z-index: 5;">
        <span class="eyebrow" style="color: rgba(255, 255, 255, 0.9); background: rgba(255, 255, 255, 0.12); border: 1px solid rgba(255, 255, 255, 0.25);">Hyper Adz &nbsp;›&nbsp; Ad Network</span>
        <h1 class="text-white fw-bold mb-3" style="font-size: clamp(2.5rem, 5vw, 3.5rem); letter-spacing: -0.02em;">Advertising Network</h1>
        <p class="text-white-50 mx-auto mb-0" style="max-width: 600px; font-size: 1.1rem; opacity: 0.85;">Explore premium indoor ad screen locations across Coimbatore's busiest areas.</p>
    </div>
</div>

<div class="bg-grid-pattern">
    <div class="gradient-blob gradient-blob-1"></div>
    <div class="gradient-blob gradient-blob-2"></div>

    <section class="network-section pt-5">
        <div class="container">
            <div class="network-shell" data-aos="fade-up">
                <aside class="network-panel">
                    <h3 class="mb-3">Filter Network</h3>
                    <div class="network-search-box">
                        <i class="bi bi-search"></i>
                        <input type="text" placeholder="Search by area or location..." disabled>
                    </div>
                    
                    <span class="network-cats-label">Categories</span>
                    <div class="network-cats">
                        @foreach(['Malls', 'Apartments', 'Cafes', 'Gyms', 'Theatres', 'Salons', 'Restaurants'] as $cat)
                            <span>{{ $cat }}</span>
                        @endforeach
                    </div>
                    
                    <div class="network-panel-footer">
                        <h4>Location Details</h4>
                        <p class="text-muted small mb-3">Select a location pin on the map to view screen counts, slot availability, and media details.</p>
                        <button class="btn btn-primary w-100" disabled><i class="bi bi-calendar-check"></i> Book Screen</button>
                    </div>
                </aside>
                <div class="network-map-wrapper">
                    <div id="networkMap" class="network-map" aria-label="Hyper Adz network map"></div>
                    <div class="network-map-overlay">
                        <div class="network-overlay-content">
                            <i class="bi bi-geo-alt-fill"></i>
                            <h3>Interactive Map</h3>
                            <p>Our advertising network is being updated. Interactive locations will be available soon.</p>
                            <div class="network-preview-horizontal">
                                <span><i class="bi bi-display"></i> Screens</span>
                                <span><i class="bi bi-lightning-charge"></i> Live Feed</span>
                                <span><i class="bi bi-geo"></i> Map Pin</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<x-cta title="Plan your next campaign with us." text="Speak with one of our media network experts to design a campaign tailored to your business." />
@endsection

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
@endpush
