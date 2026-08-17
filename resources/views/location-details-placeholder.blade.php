@extends('layouts.app', [
    'title' => 'Location Details | Hyper Adz Network',
    'description' => 'Location details preview.'
])

@section('content')
<div class="subpage-banner" style="background-image: url('{{ asset('images/network-banner.png') }}')">
    <div class="subpage-banner-overlay"></div>
    <div class="container text-center text-white position-relative" style="z-index: 5;">
        <h1 class="text-white fw-bold mb-3" style="font-size: clamp(2.2rem, 4vw, 3rem); letter-spacing: -0.02em;">Location Details</h1>
        <p class="text-white-50 mx-auto mb-0" style="max-width: 600px; font-size: 1.1rem; opacity: 0.85;">Details for dynamic venue: <code>{{ $slug }}</code></p>
    </div>
</div>

<div class="container py-5 my-5 text-center">
    <div class="max-w-md mx-auto p-5 border rounded-3 shadow-sm bg-white" style="max-width: 500px;">
        <i class="bi bi-info-circle text-primary" style="font-size: 3rem;"></i>
        <h3 class="fw-bold mt-3 mb-2" style="font-family: 'Sora', sans-serif;">Venue Details Page Placeholder</h3>
        <p class="text-muted small mb-4">You navigated to the placeholder page for slug: <strong>{{ $slug }}</strong>. The complete venue portfolio, inventory counts, screen lists, and booking slots will be built here in future phases.</p>
        <a href="{{ route('network') }}" class="btn btn-primary px-4"><i class="bi bi-arrow-left"></i> Back to Ad Network</a>
    </div>
</div>
@endsection
