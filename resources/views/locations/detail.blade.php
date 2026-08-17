@extends('layouts.app', [
    'title' => $location->name . ' | Hyper Adz Network Venues',
    'description' => 'Detailed display metrics for advertising at ' . $location->name . ' in ' . $location->city . '. Available digital screens and reach data.'
])

@section('content')
<!-- Subpage Banner -->
<div class="subpage-banner" style="background-image: url('{{ asset('images/network-banner.png') }}')">
    <div class="subpage-banner-overlay"></div>
    <div class="container text-center text-white position-relative" style="z-index: 5;">
        <span class="eyebrow" style="color: rgba(255, 255, 255, 0.9); background: rgba(255, 255, 255, 0.12); border: 1px solid rgba(255, 255, 255, 0.25);">
            <a href="{{ route('network') }}" class="text-white text-decoration-none">Network</a> &nbsp;›&nbsp; {{ $location->name }}
        </span>
        <h1 class="text-white fw-bold mb-3" style="font-size: clamp(2rem, 4vw, 3rem); letter-spacing: -0.02em;">{{ $location->name }}</h1>
        <p class="text-white-50 mx-auto mb-0" style="max-width: 600px; font-size: 1rem; opacity: 0.85;">
            <i class="bi bi-geo-alt-fill text-white-50"></i> {{ $location->address }}, {{ $location->city }}, {{ $location->state }} - {{ $location->postal_code }}
        </p>
    </div>
</div>

<div class="bg-light py-5">
    <div class="container">
        <div class="row g-5">
            <!-- Left Side: Details & Screens -->
            <div class="col-lg-8">
                <!-- Location Overview Card -->
                <div class="card border-0 shadow-sm rounded-4 p-4 mb-4 bg-white">
                    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
                        <div>
                            <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-3 py-1.5 rounded-pill text-xs fw-bold">
                                <i class="bi {{ $location->category->icon ?? 'bi-tag' }}"></i> {{ $location->category->name }}
                            </span>
                        </div>
                        <div class="d-flex items-center gap-3">
                            <div class="text-end">
                                <span class="block text-[10px] text-muted uppercase tracking-wider font-bold">Location Status</span>
                                @if($location->status === 'active')
                                    <span class="badge bg-success-subtle text-success border border-success-subtle px-2.5 py-1 rounded-pill text-xxs fw-bold">Active</span>
                                @else
                                    <span class="badge bg-warning-subtle text-warning border border-warning-subtle px-2.5 py-1 rounded-pill text-xxs fw-bold">Maintenance</span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <h4 class="fw-bold mb-3" style="font-family: 'Sora', sans-serif;">About Venue</h4>
                    <p class="text-slate-650 text-sm leading-relaxed mb-4">
                        {{ $location->description ?? 'Premium network partner location suited for targeting local retail, shopping, and high footfall consumer traffic in Coimbatore.' }}
                    </p>

                    <!-- Metrics Badges row -->
                    <div class="row g-3 pt-3 border-top border-light">
                        <div class="col-6 col-sm-3">
                            <div class="p-3 bg-light rounded-3 text-center">
                                <i class="bi bi-people-fill text-primary text-xl mb-1 d-block"></i>
                                <span class="text-xxs text-muted uppercase tracking-wider block font-bold">Daily Footfall</span>
                                <span class="fw-bold text-slate-800 text-sm">{{ number_format($location->daily_footfall) }}</span>
                            </div>
                        </div>
                        <div class="col-6 col-sm-3">
                            <div class="p-3 bg-light rounded-3 text-center">
                                <i class="bi bi-display-fill text-primary text-xl mb-1 d-block"></i>
                                <span class="text-xxs text-muted uppercase tracking-wider block font-bold">Total Screens</span>
                                <span class="fw-bold text-slate-800 text-sm">{{ $location->screens->count() }}</span>
                            </div>
                        </div>
                        <div class="col-6 col-sm-3">
                            <div class="p-3 bg-light rounded-3 text-center">
                                <i class="bi bi-eye-fill text-primary text-xl mb-1 d-block"></i>
                                <span class="text-xxs text-muted uppercase tracking-wider block font-bold">Net Impressions</span>
                                <span class="fw-bold text-[#1155CC] text-sm">{{ number_format($location->screens->where('status', 'active')->sum('daily_impressions')) }}</span>
                            </div>
                        </div>
                        <div class="col-6 col-sm-3">
                            <div class="p-3 bg-light rounded-3 text-center">
                                <i class="bi bi-clock-fill text-primary text-xl mb-1 d-block"></i>
                                <span class="text-xxs text-muted uppercase tracking-wider block font-bold">Operating Hours</span>
                                <span class="fw-bold text-slate-800 text-xs block truncate" title="{{ $location->operating_hours }}">{{ $location->operating_hours ?? '10 AM - 10 PM' }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Available Screens -->
                <div class="mb-4">
                    <h4 class="fw-bold mb-4 flex items-center gap-2" style="font-family: 'Sora', sans-serif;">
                        <i class="bi bi-display text-[#1155CC]"></i> Available Advertising Screens
                    </h4>

                    <div class="row g-4">
                        @forelse($location->screens as $screen)
                            <div class="col-md-6">
                                <div class="card border-0 shadow-sm rounded-4 overflow-hidden bg-white h-100 flex flex-col group">
                                    <!-- Screen Image Header -->
                                    <div class="position-relative aspect-video bg-slate-100 overflow-hidden">
                                        <img src="{{ $screen->primary_image_url }}" alt="{{ $screen->name }}" class="w-full h-full object-cover transition-all duration-500 group-hover:scale-105">
                                        
                                        <!-- Code badges -->
                                        <div class="position-absolute top-3 left-3 d-flex flex-column gap-1.5">
                                            <span class="badge bg-dark/85 text-white text-xxs font-mono font-semibold px-2.5 py-1 rounded shadow">
                                                {{ $screen->screen_code }}
                                            </span>
                                            @if($screen->screen_identifier)
                                                <span class="badge bg-primary text-white text-xxs font-mono font-semibold px-2.5 py-1 rounded shadow">
                                                    {{ $screen->screen_identifier }}
                                                </span>
                                            @endif
                                        </div>

                                        <!-- Status Badge -->
                                        <div class="position-absolute top-3 right-3">
                                            @if($screen->status === 'active')
                                                <span class="badge bg-success text-white text-xxs px-2.5 py-1 rounded-pill font-bold">Active</span>
                                            @elseif($screen->status === 'maintenance')
                                                <span class="badge bg-warning text-dark text-xxs px-2.5 py-1 rounded-pill font-bold">Maintenance</span>
                                            @else
                                                <span class="badge bg-secondary text-white text-xxs px-2.5 py-1 rounded-pill font-bold">Inactive</span>
                                            @endif
                                        </div>
                                    </div>

                                    <!-- Screen body -->
                                    <div class="p-4 flex-grow flex flex-col justify-between">
                                        <div>
                                            <h5 class="fw-bold mb-1 text-slate-850" style="font-family: 'Sora', sans-serif; font-size: 1.05rem;">{{ $screen->name }}</h5>
                                            <span class="badge bg-light text-slate-600 border border-slate-200/50 text-[10px] uppercase font-bold mb-3">
                                                {{ $screen->type->name ?? 'Custom Signage' }}
                                            </span>
                                            <p class="text-muted text-xs leading-relaxed mb-3">
                                                {{ $screen->description ?? 'Sleek visual display mounted to maximize consumer visibility and campaign conversions.' }}
                                            </p>
                                        </div>

                                        <!-- Specs grid -->
                                        <div class="border-top border-light pt-3 mt-auto">
                                            <div class="row g-2 text-[11px] text-slate-650">
                                                <div class="col-6">
                                                    <i class="bi bi-aspect-ratio text-primary"></i> <strong>Orientation:</strong> {{ $screen->orientation }}
                                                </div>
                                                <div class="col-6">
                                                    <i class="bi bi-cpu text-primary"></i> <strong>Resolution:</strong> {{ $screen->resolution ?? '1920x1080' }}
                                                </div>
                                                <div class="col-6">
                                                    <i class="bi bi-file-earmark-code text-primary"></i> <strong>Formats:</strong> {{ $screen->supported_formats }}
                                                </div>
                                                <div class="col-6">
                                                    <i class="bi bi-eye-fill text-[#1155CC] font-bold"></i> <strong>Impressions:</strong> {{ number_format($screen->daily_impressions) }}/day
                                                </div>
                                                @if($screen->max_video_duration)
                                                    <div class="col-12 mt-1.5 font-bold text-xxs text-primary uppercase tracking-wide">
                                                        <i class="bi bi-stopwatch-fill"></i> Supports Video up to {{ $screen->max_video_duration }}s
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="col-12">
                                <div class="p-5 text-center bg-white rounded-4 border border-slate-100 shadow-sm text-muted">
                                    <i class="bi bi-display text-4xl mb-3 d-block opacity-40"></i>
                                    <h6 class="fw-bold text-slate-800">No screen slots registered yet</h6>
                                    <p class="small mb-0">We are currently deploying digital displays in this venue. Check back soon!</p>
                                </div>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- Right Side: Gallery & Contact Card -->
            <div class="col-lg-4">
                <!-- Location Gallery -->
                <div class="card border-0 shadow-sm rounded-4 p-4 mb-4 bg-white">
                    <h5 class="fw-bold mb-3" style="font-family: 'Sora', sans-serif;"><i class="bi bi-images text-primary"></i> Venue Gallery</h5>
                    
                    @if($location->images->isNotEmpty())
                        <div class="row g-2">
                            @foreach($location->images->sortBy('display_order') as $img)
                                <div class="col-6">
                                    <a href="{{ $img->url }}" target="_blank" class="block aspect-video rounded-3 overflow-hidden bg-light border border-light">
                                        <img src="{{ $img->url }}" class="w-full h-full object-cover hover:scale-105 transition-all duration-300">
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="py-4 text-center text-muted border border-dashed rounded-3 bg-light/50">
                            <i class="bi bi-image text-2xl d-block opacity-30 mb-1"></i>
                            <span class="text-xxs">No location photos available</span>
                        </div>
                    @endif
                </div>

                <!-- Advertising Request Promo Card -->
                <div class="card border-0 shadow-sm rounded-4 bg-[#0A1628] text-white p-4">
                    <h5 class="fw-bold mb-3 text-white flex items-center gap-2" style="font-family: 'Sora', sans-serif;">
                        <span class="w-2.5 h-2.5 bg-blue-500 rounded-full animate-pulse"></span> Advertise Here
                    </h5>
                    <p class="text-white-50 text-xs leading-relaxed mb-4">
                        Unlock high impressions by scheduling your campaign on digital signage displays at {{ $location->name }}. Target shopping mall visitors and capture active consumers.
                    </p>
                    <a href="{{ route('contact') }}" class="btn btn-primary w-full py-2.5 font-bold text-xs uppercase tracking-wider rounded-3 shadow">
                        Request Advertising Quote
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
