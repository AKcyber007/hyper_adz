@extends('layouts.app', [
    'title' => 'Become a Location Partner | Earn from Indoor Ad Screens | Hyper Adz Coimbatore',
    'description' => 'Partner with Hyper Adz and earn passive revenue from your venue. We install and manage indoor digital advertising screens in restaurants, cafes, gyms, salons, clinics and more across Coimbatore.'
])

@section('content')
<div class="subpage-banner" style="background-image: url('{{ asset('images/partner-banner.png') }}')">
    <div class="subpage-banner-overlay"></div>
    <div class="container text-center text-white position-relative" style="z-index: 5;">
        <span class="eyebrow" style="color: rgba(255, 255, 255, 0.9); background: rgba(255, 255, 255, 0.12); border: 1px solid rgba(255, 255, 255, 0.25);">Hyper Adz &nbsp;›&nbsp; Partner Programme</span>
        <h1 class="text-white fw-bold mb-3" style="font-size: clamp(2.5rem, 5vw, 3.5rem); letter-spacing: -0.02em;">Become a Location Partner</h1>
        <p class="text-white-50 mx-auto mb-0" style="max-width: 600px; font-size: 1.1rem; opacity: 0.85;">Host a screen, earn passive revenue, and gain visibility. Zero effort, zero cost.</p>
    </div>
</div>

<div class="bg-grid-pattern">
    <div class="gradient-blob gradient-blob-1"></div>
    <div class="gradient-blob gradient-blob-2"></div>

    {{-- Partner Categories --}}
    <section class="section-pad pt-5">
        <div class="container">

            <div class="section-header text-center mb-4" data-aos="fade-up">
                <span class="eyebrow">We work with</span>
                <h2>Which venues qualify?</h2>
                <p>We partner with high-footfall local businesses across 6 categories in Coimbatore.</p>
            </div>

            <div class="partner-cat-grid" data-aos="fade-up">
                @foreach([
                    ['bi-cup-hot', 'Food & Beverage', 'Restaurants, Cafes, Bakeries, Supermarkets'],
                    ['bi-heart-pulse', 'Health & Fitness', 'Gyms, Fitness Studios, Yoga Centers'],
                    ['bi-stars', 'Beauty & Wellness', 'Salons, SPAs, Hair & Skin Clinics'],
                    ['bi-car-front', 'Auto & Mobility', 'Car & Bike Showrooms, Service Centres'],
                    ['bi-bag', 'Retail & Lifestyle', 'Apparel, Organic & Medical Stores'],
                    ['bi-book', 'Learning & Recreation', 'Education Institutions, Libraries'],
                ] as [$icon, $title, $desc])
                    <div class="partner-cat-card" data-aos="fade-up">
                        <i class="bi {{ $icon }}"></i>
                        <h4>{{ $title }}</h4>
                        <p class="small text-muted mt-2 mb-0">{{ $desc }}</p>
                    </div>
                @endforeach
            </div>

            {{-- Benefits --}}
            <div class="section-header text-center mt-5 mb-4" data-aos="fade-up">
                <span class="eyebrow">Why Partner</span>
                <h2>Benefits for your venue</h2>
            </div>

            <div class="row justify-content-center" data-aos="fade-up">
                <div class="col-lg-8">
                    <ul class="list-unstyled mb-0">
                        @foreach([
                            ['bi-cash-coin', 'Earn Passive Revenue', 'Receive a share of the advertising revenue generated from screens installed at your venue — with no upfront cost.'],
                            ['bi-display', 'We Handle Everything', 'Hyper Adz installs, manages, and maintains the digital screens. You simply host the screen location.'],
                            ['bi-graph-up-arrow', 'Boost Venue Value', 'Premium screens add a professional, modern feel to your space while serving as an additional income source.'],
                            ['bi-shield-check', 'Vetted Ad Content', 'All advertisements are reviewed before display. We ensure only relevant, professional campaigns appear at your venue.'],
                            ['bi-geo-alt', 'Hyper Local Network', 'Become part of Coimbatore\'s growing premium indoor advertising network, building long-term value for your business.'],
                            ['bi-chat-dots', 'Dedicated Support', 'Our team provides ongoing support for screen operations, content scheduling, and any technical issues.'],
                        ] as [$icon, $title, $desc])
                            <li class="d-flex align-items-start mb-4 pb-2 border-bottom">
                                <div class="me-4 text-primary bg-light rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px; flex-shrink: 0;">
                                    <i class="bi {{ $icon }} fs-5"></i>
                                </div>
                                <div>
                                    <h4 class="h5 mb-2">{{ $title }}</h4>
                                    <p class="text-muted mb-0">{{ $desc }}</p>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>

            {{-- How It Works --}}
            <div class="section-header text-center mt-5 mb-4" data-aos="fade-up">
                <span class="eyebrow">Process</span>
                <h2>How the partnership works</h2>
            </div>

            <div class="partner-steps" data-aos="fade-up">
                @foreach([
                    ['01', 'Submit Enquiry', 'Fill in the form below or WhatsApp us. Tell us about your venue, location, and footfall.'],
                    ['02', 'Venue Assessment', 'Our team visits, assesses screen placement potential, and proposes a partnership plan.'],
                    ['03', 'Screen Installation', 'We install and configure the digital screen at zero cost to your venue.'],
                    ['04', 'Start Earning', 'Campaigns go live. Your venue earns revenue from every campaign displayed on your screen.'],
                ] as [$num, $title, $desc])
                    <div class="partner-step">
                        <div class="partner-step-num">{{ $num }}</div>
                        <h4>{{ $title }}</h4>
                        <p>{{ $desc }}</p>
                    </div>
                @endforeach
            </div>

            {{-- Partner Enquiry Form --}}
            <div class="row g-5 mt-4 align-items-start">
                <div class="col-lg-5" data-aos="fade-right">
                    <div class="contact-card">
                        <span class="eyebrow"><i class="bi bi-buildings"></i> Partner Enquiry</span>
                        <h2 class="mt-3">Ready to partner with us?</h2>
                        <p class="text-muted">Submit your venue details and our team will get back to you within one business day.</p>
                        <div class="mt-4">
                            <a href="https://wa.me/919962099110?text=Hi%20Hyper%20Adz%2C%20I%20am%20interested%20in%20becoming%20a%20location%20partner%20for%20my%20venue." target="_blank" rel="noopener" class="btn btn-primary btn-lg w-100 mb-3">
                                <i class="bi bi-whatsapp"></i> WhatsApp Us Now
                            </a>
                            <a href="mailto:support@hyperadz.in?subject=Location Partner Enquiry" class="btn btn-ghost btn-lg w-100">
                                <i class="bi bi-envelope"></i> Email Us
                            </a>
                        </div>
                        <hr class="my-4" style="border-color:var(--ha-border);">
                        <div class="d-flex gap-3 align-items-center">
                            <i class="bi bi-telephone text-primary fs-4"></i>
                            <div>
                                <span class="text-muted small d-block">Call us directly</span>
                                <a href="tel:+919962099110" class="fw-bold text-decoration-none" style="color:var(--ha-ink);">+91 99620 99110</a>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-7" data-aos="fade-left">
                    <form class="contact-form" id="partner-form">
                        @csrf
                        <div id="partner-alert" class="alert d-none mb-4 rounded-3 text-xs" role="alert"></div>
                        
                        <h3 class="mb-4">Venue Partner Enquiry Form</h3>
                        <div class="row g-4">
                            <div class="col-md-6">
                                <label for="partner-name">Your Name <span class="text-danger">*</span></label>
                                <input class="form-control" id="partner-name" name="name" type="text" placeholder="Full Name" required>
                            </div>
                            <div class="col-md-6">
                                <label for="partner-phone">Phone Number <span class="text-danger">*</span></label>
                                <input class="form-control" id="partner-phone" name="phone" type="tel" placeholder="+91 99000 00000" required>
                            </div>
                            <div class="col-md-6">
                                <label for="partner-email">Email Address <span class="text-danger">*</span></label>
                                <input class="form-control" id="partner-email" name="email" type="email" placeholder="you@example.com" required>
                            </div>
                            <div class="col-md-6">
                                <label for="partner-venue-type">Venue Type</label>
                                <select class="form-select" id="partner-venue-type" name="venue_type">
                                    <option value="">Select category</option>
                                    <option>Restaurant / Cafe / Bakery</option>
                                    <option>Gym / Fitness Studio</option>
                                    <option>Salon / SPA / Wellness Clinic</option>
                                    <option>Medical / Dental / Skin Clinic</option>
                                    <option>Retail / Apparel / Supermarket</option>
                                    <option>Education Institution</option>
                                    <option>Auto Showroom / Service</option>
                                    <option>Other</option>
                                </select>
                            </div>
                            <div class="col-12">
                                <label for="partner-venue-name">Venue Name & Address <span class="text-danger">*</span></label>
                                <input class="form-control" id="partner-venue-name" name="venue_name" type="text" placeholder="Venue name and area in Coimbatore" required>
                            </div>
                            <div class="col-md-6">
                                <label for="partner-daily-footfall">Daily Footfall (approx.)</label>
                                <select class="form-select" id="partner-daily-footfall" name="daily_footfall">
                                    <option value="">Select range</option>
                                    <option>Under 50</option>
                                    <option>50 – 150</option>
                                    <option>150 – 500</option>
                                    <option>500+</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="partner-screens">Number of screens you can accommodate</label>
                                <select class="form-select" id="partner-screens" name="screens_count">
                                    <option>1 screen</option>
                                    <option>2 screens</option>
                                    <option>3+ screens</option>
                                    <option>Not sure yet</option>
                                </select>
                            </div>
                            <div class="col-12">
                                <label for="partner-message">Additional Information</label>
                                <textarea class="form-control" id="partner-message" name="message" rows="3" placeholder="Tell us more about your venue, peak hours, or any questions you have..."></textarea>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary btn-lg mt-4" id="partner-submit-btn">
                            <i class="bi bi-send"></i> <span id="partner-submit-text">Submit Partner Enquiry</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </section>
</div>

<x-cta title="Join the Hyper Adz partner network." text="Earn passive income from your venue while contributing to a growing hyper-local advertising ecosystem in Tamil Nadu." />
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('partner-form');
        const alertBox = document.getElementById('partner-alert');
        const submitBtn = document.getElementById('partner-submit-btn');
        const submitText = document.getElementById('partner-submit-text');

        if (form) {
            form.addEventListener('submit', function(event) {
                event.preventDefault();
                
                submitBtn.disabled = true;
                submitText.textContent = 'Submitting...';
                alertBox.className = 'alert d-none';

                // Concatenate message parameters
                const venueType = document.getElementById('partner-venue-type').value;
                const venueName = document.getElementById('partner-venue-name').value;
                const footfall = document.getElementById('partner-daily-footfall').value;
                const screens = document.getElementById('partner-screens').value;
                const additional = document.getElementById('partner-message').value;

                let fullMessage = `Venue Name: ${venueName}\n`;
                if(venueType) fullMessage += `Venue Type: ${venueType}\n`;
                if(footfall)  fullMessage += `Approx Daily Footfall: ${footfall}\n`;
                if(screens)   fullMessage += `Accommodate Screens: ${screens}\n`;
                if(additional) fullMessage += `Additional Details: ${additional}`;

                const formData = {
                    _token: form.querySelector('input[name="_token"]').value,
                    name: document.getElementById('partner-name').value,
                    phone: document.getElementById('partner-phone').value,
                    email: document.getElementById('partner-email').value,
                    company_name: venueName,
                    lead_type: 'location_partner',
                    message: fullMessage,
                    source: 'partner_page'
                };

                fetch('{{ route("leads.store") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(formData)
                })
                .then(response => response.json().then(data => ({ status: response.status, body: data })))
                .then(res => {
                    submitBtn.disabled = false;
                    submitText.textContent = 'Submit Partner Enquiry';

                    if (res.status === 201) {
                        alertBox.className = 'alert alert-success p-3 rounded-3';
                        alertBox.textContent = res.body.message;
                        form.reset();
                    } else {
                        alertBox.className = 'alert alert-danger p-3 rounded-3';
                        if (res.body.errors) {
                            alertBox.textContent = Object.values(res.body.errors).flat().join(' ');
                        } else {
                            alertBox.textContent = res.body.message || 'An error occurred. Please check details.';
                        }
                    }
                })
                .catch(error => {
                    submitBtn.disabled = false;
                    submitText.textContent = 'Submit Partner Enquiry';
                    alertBox.className = 'alert alert-danger p-3 rounded-3';
                    alertBox.textContent = 'Network error. Please try again later.';
                });
            });
        }
    });
</script>
@endpush
