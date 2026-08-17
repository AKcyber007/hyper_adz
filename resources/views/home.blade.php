@extends('layouts.app', [
    'title' => 'Hyper Adz | Indoor Advertising Coimbatore | Digital Signage & DOOH Network',
    'description' => 'Hyper Adz connects local brands with high-intent audiences through premium indoor screens in restaurants, cafes, gyms, salons and clinics across Coimbatore. Get a quote today.'
])

@section('content')
<!-- Hero Section -->
<section class="hero-section bg-grid-pattern">
    <div class="gradient-blob gradient-blob-1"></div>
    <div class="gradient-blob gradient-blob-2"></div>
    <div class="container">
        <div class="row align-items-center g-5">
            <div class="col-lg-7" data-aos="fade-right">
                <span class="eyebrow"><i class="bi bi-cpu"></i> By Multitude Solutions</span>
                <h1>Connect Your Brand to High-Intent Local Audiences</h1>

                <div class="hero-actions">
                    <a href="{{ route('network') }}" class="btn btn-primary btn-lg"><i class="bi bi-geo-alt"></i> Explore Network</a>
                    <a href="{{ route('network') }}" class="btn btn-ghost btn-lg"><i class="bi bi-chat-dots"></i> Plan Campaign</a>
                </div>
                <div class="hero-trust-indicators">
                    <div class="trust-item"><i class="bi bi-check-circle-fill"></i> Premium partner venues</div>
                    <div class="trust-item"><i class="bi bi-check-circle-fill"></i> Measurable reach</div>
                    <div class="trust-item"><i class="bi bi-check-circle-fill"></i> Location-aware reporting</div>
                </div>
            </div>
            <div class="col-lg-5" data-aos="fade-left">
                <div class="hero-showcase-wrapper">
                    <div class="hero-glow-backdrop"></div>
                    <div class="hero-glass-container float-slow">
                        <div class="hero-img-box">
                            <img src="{{ asset('images/hero.png') }}" class="hero-image" alt="Digital Signage Advertising Screen">
                        </div>
                        
                        <!-- Floating Badges -->
                        <div class="floating-badge badge-live float-delayed">
                            <i class="bi bi-broadcast"></i>
                            <div>
                                <span class="floating-badge-title">Live Screen</span>
                                <span class="floating-badge-value">Brookefields Mall</span>
                            </div>
                        </div>
                        
                        <div class="floating-badge badge-cloud float-slow">
                            <i class="bi bi-cloud-check"></i>
                            <div>
                                <span class="floating-badge-title">Status</span>
                                <span class="floating-badge-value">Cloud Connected</span>
                            </div>
                        </div>
                        
                        <div class="floating-badge badge-screens float-delayed">
                            <i class="bi bi-display"></i>
                            <div>
                                <span class="floating-badge-title">Display Fleet</span>
                                <span class="floating-badge-value">300+ Screens</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Trusted By / Statistics Section -->
<section class="section-pad pt-0 stats-section-with-bg">
    <div class="stats-bg-slider">
        <img src="{{ asset('images/slide-mall.png') }}" class="stats-bg-image active" alt="Malls">
        <img src="{{ asset('images/slide-cafe.png') }}" class="stats-bg-image" alt="Cafes">
        <img src="{{ asset('images/slide-apartment.png') }}" class="stats-bg-image" alt="Apartments">
        <img src="{{ asset('images/slide-outdoor.png') }}" class="stats-bg-image" alt="Outdoor">
        <img src="{{ asset('images/slide-tstandee.png') }}" class="stats-bg-image" alt="T-Standees">
        <img src="{{ asset('images/slide-wallmount.png') }}" class="stats-bg-image" alt="Wall Mounts">
    </div>
    <div class="container">
        <div class="stats-grid" data-aos="fade-up">
            <div class="stats-card">
                <span class="stats-num" data-val="300" data-suffix="+">0</span>
                <span class="stats-label">Cloud Connected Screens</span>
            </div>
            <div class="stats-card">
                <span class="stats-num" data-val="1" data-prefix="#">0</span>
                <span class="stats-label">Largest Indoor Network</span>
            </div>
            <div class="stats-card">
                <span class="stats-num" data-val="100" data-suffix="%">0</span>
                <span class="stats-label">Geo Tagged Reports</span>
            </div>
            <div class="stats-card">
                <span class="stats-num" data-val="7" data-suffix="+">0</span>
                <span class="stats-label">Premium Location Types</span>
            </div>
        </div>
    </div>
</section>




<!-- Who We Serve Section -->
<section class="section-pad soft-band">
    <div class="container">
        <x-section-header eyebrow="Who We Serve" title="Built for businesses, venues, and partners" text="Hyper Adz serves advertisers, venue partners, signage buyers, and sales partners." />
        <div class="row g-5 align-items-start">
            <div class="col-lg-6" data-aos="fade-right">
                <div class="about-card h-100">
                    <span class="eyebrow"><i class="bi bi-megaphone"></i> For Advertisers</span>
                    <h3 class="mb-3">Reach the right audience at the right moment</h3>
                    <p class="mb-4">Run geo-targeted indoor campaigns across our premium partner network. Ideal for:</p>
                    <div class="d-flex flex-wrap gap-2">
                        @foreach(['Car & Bike Showrooms', 'Jewellery Brands', 'Education Institutions', 'Apparel & Fashion', 'D2C & B2C Brands', 'Advertising Agencies', 'Marketing Companies', 'SMB Owners'] as $type)
                            <span class="badge rounded-pill px-3 py-2" style="background:var(--ha-blue-light);color:var(--ha-blue);font-weight:700;font-size:0.82rem;">{{ $type }}</span>
                        @endforeach
                    </div>
                    <div class="mt-4">
                        <a href="{{ route('contact') }}" class="btn btn-primary"><i class="bi bi-megaphone"></i> Advertise with Hyper Adz</a>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-6" data-aos="fade-left">
                <div class="about-card h-100">
                    <span class="eyebrow"><i class="bi bi-geo-alt"></i> For Location Partners</span>
                    <h3 class="mb-3">Earn from your venue space with zero effort</h3>
                    <p class="mb-4">We install and manage the screens. You earn passive revenue from your venue location. We work with:</p>
                    <div class="partner-cat-grid" style="grid-template-columns: repeat(3, 1fr); margin: 0 0 20px;">
                        @foreach([
                            ['bi-cup-hot', 'Food & Beverage'],
                            ['bi-heart-pulse', 'Health & Fitness'],
                            ['bi-stars', 'Beauty & Wellness'],
                            ['bi-car-front', 'Auto & Mobility'],
                            ['bi-bag', 'Retail & Lifestyle'],
                            ['bi-book', 'Learning & Recreation'],
                        ] as [$icon, $label])
                            <div class="partner-cat-card" style="padding:16px 12px;">
                                <i class="bi {{ $icon }}"></i>
                                <h4>{{ $label }}</h4>
                            </div>
                        @endforeach
                    </div>
                    <a href="{{ route('partner') }}" class="btn btn-ghost"><i class="bi bi-buildings"></i> Become a Location Partner</a>
                </div>
            </div>

            <div class="col-lg-6" data-aos="fade-right" data-aos-delay="100">
                <div class="about-card h-100">
                    <span class="eyebrow"><i class="bi bi-display"></i> For Digital Signage Buyers</span>
                    <h3 class="mb-3">Smart communication for your own business</h3>
                    <p class="mb-4">Promote offers, display menus, and modernize your environment with your own digital signage. Ideal for:</p>
                    <div class="d-flex flex-wrap gap-2">
                        @foreach(['Restaurants & Cafes', 'Retail Showrooms', 'Clinics & Hospitals', 'Supermarkets', 'Gyms & Salons', 'Corporate Offices'] as $type)
                            <span class="badge rounded-pill px-3 py-2" style="background:var(--ha-blue-light);color:var(--ha-blue);font-weight:700;font-size:0.82rem;">{{ $type }}</span>
                        @endforeach
                    </div>
                    <div class="mt-4">
                        <a href="{{ route('enquiry') }}" class="btn btn-ghost"><i class="bi bi-shop"></i> Get Digital Signage</a>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-6" data-aos="fade-left" data-aos-delay="100">
                <div class="about-card h-100">
                    <span class="eyebrow"><i class="bi bi-briefcase"></i> For Sales Partners</span>
                    <h3 class="mb-3">Earn commission by expanding the network</h3>
                    <p class="mb-4">Collaborate with Hyper Adz to bring in new advertisers or locations and earn commission from successful business.</p>
                    <ul class="list-unstyled text-start text-muted mb-4 space-y-2">
                        <li><i class="bi bi-check-circle-fill text-primary me-2"></i>Source Advertisers for our network</li>
                        <li><i class="bi bi-check-circle-fill text-primary me-2"></i>Source new Location Partners</li>
                        <li><i class="bi bi-check-circle-fill text-primary me-2"></i>Earn commission on successful deals</li>
                    </ul>
                    <div class="mt-4">
                        <a href="{{ route('enquiry') }}" class="btn btn-ghost"><i class="bi bi-person-lines-fill"></i> Become a Sales Partner</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>




<!-- How It Works Section -->
<section class="section-pad soft-band">
    <div class="container">
        <x-section-header eyebrow="Process" title="How to launch your indoor campaign" text="Four simple steps to place your brand directly in front of your customer base." />
        <div class="row g-4 justify-content-center">
            @foreach([
                ['Step 1', 'Choose Location', 'bi-geo-alt', 'Select from premium locations like Brookefields Mall, retail circuits, gyms, and cafes across the city.'],
                ['Step 2', 'Select Screen', 'bi-display', 'Choose specific display counts, display sizes (1080P/LED), visual angles, and slots.'],
                ['Step 3', 'Upload Creative', 'bi-cloud-upload', 'Upload your high-definition image or video creatives directly to the platform for instant approval.'],
                ['Step 4', 'Launch Campaign', 'bi-rocket-takeoff', 'Go live on the selected date and monitor performance with location-aware reports.']
            ] as [$step, $title, $icon, $desc])
                <div class="col-md-6 col-lg-3">
                    <div class="timeline-step h-100" data-aos="fade-up">
                        <span class="eyebrow mb-2">{{ $step }}</span>
                        <div class="d-flex align-items-center gap-3 mb-3">
                            <i class="bi {{ $icon }} text-primary fs-3"></i>
                            <strong>{{ $title }}</strong>
                        </div>
                        <p>{{ $desc }}</p>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>

<!-- Brands Slider Section -->
<x-brands-slider />


<!-- Latest Insights Section -->
@if(isset($latestBlogs) && $latestBlogs->count() > 0)
<section class="section-pad soft-band">
    <div class="container">
        <x-section-header eyebrow="Blog & Insights" title="Latest Insights" text="Read articles on marketing strategies, digital out-of-home advertising trends, and partner venue spotlights." />
        
        <div class="row g-4">
            @foreach($latestBlogs as $blog)
                <div class="col-md-6 col-lg-4">
                    <div class="card h-100 border-0 shadow-sm overflow-hidden hover-card" style="border-radius: 24px; background: #FFF; border: 1px solid rgba(17, 85, 204, 0.05) !important; transition: all 0.3s;">
                        <!-- Image Wrap -->
                        <div class="position-relative overflow-hidden" style="height: 200px;">
                            @if($blog->featured_image)
                                <img src="{{ asset('storage/' . $blog->featured_image) }}" class="w-full h-full object-cover hover-scale" alt="{{ $blog->title }}" style="transition: transform 0.5s;">
                            @else
                                <div class="w-full h-full bg-slate-100 flex items-center justify-center">
                                    <i class="bi bi-image text-slate-400 text-3xl"></i>
                                </div>
                            @endif
                            <span class="badge bg-light text-dark position-absolute border py-1.5 px-2.5 font-bold" style="bottom: 12px; left: 12px; font-size: 0.7rem; border-radius: 8px;">
                                {{ $blog->reading_time }}
                            </span>
                        </div>

                        <!-- Card Body -->
                        <div class="card-body p-4 d-flex flex-column justify-content-between">
                            <div>
                                <div class="text-muted text-xxs font-bold uppercase tracking-wider mb-2">
                                    {{ $blog->publish_date ? $blog->publish_date->format('M d, Y') : $blog->created_at->format('M d, Y') }}
                                </div>
                                <h4 class="card-title fw-bold mb-2" style="font-size: 1.125rem; line-height: 1.4; color: var(--ha-ink);">
                                    <a href="{{ route('blog.show', $blog->slug) }}" class="text-decoration-none text-dark hover:text-primary transition-all">{{ $blog->title }}</a>
                                </h4>
                                <p class="card-text text-muted text-sm line-clamp-3 mb-4" style="line-height: 1.5;">{{ $blog->short_description }}</p>
                            </div>
                            <div>
                                <a href="{{ route('blog.show', $blog->slug) }}" class="text-primary text-decoration-none font-bold text-sm hover-arrow">
                                    Read More <i class="bi bi-arrow-right ms-1"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>
@endif

<!-- CTA Section -->
<x-cta title="Plan your next indoor digital campaign." text="Share your campaign goal and Hyper Adz will help identify the right locations, formats, and rollout plan." />


@endsection
