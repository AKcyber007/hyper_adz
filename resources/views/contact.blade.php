@extends('layouts.app', [
    'title' => 'Contact Hyper Adz | Plan an Indoor Digital Campaign',
    'description' => 'Contact Hyper Adz in Coimbatore for indoor digital advertising, signage rental, screen sales, and campaign planning.'
])

@section('content')
@php
    $formType = request()->query('form');
@endphp

@if($formType === 'advertiser')
    <!-- Advertiser-focused Banner -->
    <div class="subpage-banner" style="background-image: url('{{ asset('images/advertiser-contact-banner.png') }}')">
        <div class="subpage-banner-overlay"></div>
        <div class="container text-center text-white position-relative" style="z-index: 5;">
            <span class="eyebrow" style="color: rgba(255, 255, 255, 0.9); background: rgba(255, 255, 255, 0.12); border: 1px solid rgba(255, 255, 255, 0.25);">Hyper Adz &nbsp;›&nbsp; Advertise</span>
            <h1 class="text-white fw-bold mb-3" style="font-size: clamp(2.2rem, 5vw, 3.2rem); letter-spacing: -0.02em;">Advertise With Us</h1>
            <p class="text-white-50 mx-auto mb-0" style="max-width: 600px; font-size: 1.1rem; opacity: 0.85;">Launch high-impact indoor digital campaigns. Get in touch to schedule your screens.</p>
        </div>
    </div>
@elseif($formType === 'partner')
    <!-- Partner-focused Banner -->
    <div class="subpage-banner" style="background-image: url('{{ asset('images/partner-contact-banner.png') }}')">
        <div class="subpage-banner-overlay"></div>
        <div class="container text-center text-white position-relative" style="z-index: 5;">
            <span class="eyebrow" style="color: rgba(255, 255, 255, 0.9); background: rgba(255, 255, 255, 0.12); border: 1px solid rgba(255, 255, 255, 0.25);">Hyper Adz &nbsp;›&nbsp; Partner</span>
            <h1 class="text-white fw-bold mb-3" style="font-size: clamp(2.2rem, 5vw, 3.2rem); letter-spacing: -0.02em;">Become a Location Partner</h1>
            <p class="text-white-50 mx-auto mb-0" style="max-width: 600px; font-size: 1.1rem; opacity: 0.85;">Install premium smart screens at your venue and earn passive revenue with zero effort.</p>
        </div>
    </div>
@else
    <!-- Default Contact Banner -->
    <div class="subpage-banner" style="background-image: url('{{ asset('images/contact-banner.png') }}')">
        <div class="subpage-banner-overlay"></div>
        <div class="container text-center text-white position-relative" style="z-index: 5;">
            <span class="eyebrow" style="color: rgba(255, 255, 255, 0.9); background: rgba(255, 255, 255, 0.12); border: 1px solid rgba(255, 255, 255, 0.25);">Hyper Adz &nbsp;›&nbsp; Contact Us</span>
            <h1 class="text-white fw-bold mb-3" style="font-size: clamp(2.2rem, 5vw, 3.2rem); letter-spacing: -0.02em;">Contact Us</h1>
            <p class="text-white-50 mx-auto mb-0" style="max-width: 600px; font-size: 1.1rem; opacity: 0.85;">Share your campaign goal and the Hyper Adz team will help shape the right indoor media plan.</p>
        </div>
    </div>
@endif

<style>
    .choice-card {
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }
    .choice-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04) !important;
    }
    .back-btn {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        color: #0066cc;
        text-decoration: none;
        font-weight: 600;
        margin-bottom: 25px;
        transition: color 0.2s ease;
    }
    .back-btn:hover {
        color: #004499;
        text-decoration: underline;
    }
</style>

@if(empty($formType))
    {{-- Hero CTA Selection Cards: Only visible on the initial Contact page --}}
    <section class="py-5" style="background: linear-gradient(135deg, #f8faff 0%, #eef2ff 100%);">
        <div class="container">
            <div class="row justify-content-center text-center mb-5" data-aos="fade-up">
                <div class="col-lg-8">
                    <h2 class="fw-bold mb-3" style="color: #0b1c3f; font-family: 'Sora', sans-serif; font-size: clamp(1.6rem, 3vw, 2.4rem);">How would you like to work with us?</h2>
                    <p class="text-muted mx-auto mb-0" style="max-width: 550px;">Choose the path that fits your goal — we'll guide you from here.</p>
                </div>
            </div>
            <div class="row justify-content-center g-4 pb-4">
                <!-- Card 1: Advertise With Us -->
                <div class="col-md-5 col-lg-4" data-aos="fade-right" data-aos-delay="100">
                    <div class="card choice-card h-100 border-0 p-5 d-flex flex-column justify-content-between" style="border-radius: 28px; background: #ffffff; box-shadow: 0 4px 24px rgba(79,70,229,0.10);">
                        <div class="text-center">
                            <div class="mb-4 mx-auto d-flex align-items-center justify-content-center" style="width: 80px; height: 80px; background: linear-gradient(135deg,#0066cc,#00a2ff); color: white; border-radius: 24px; font-size: 2.2rem; box-shadow: 0 8px 20px rgba(0,102,204,0.25);">
                                <i class="bi bi-megaphone-fill"></i>
                            </div>
                            <h3 class="fw-bold mb-3" style="color: #0b1c3f; font-family: 'Sora', sans-serif; font-size: 1.3rem;">Advertise With Us</h3>
                            <p class="text-muted small leading-relaxed px-2 mb-4">Promote your brand on our premium indoor digital screen network. Reach high-intent local audiences across Coimbatore's best cafés, salons, clinics, and gyms.</p>
                            <ul class="list-unstyled text-start text-muted small px-2 mb-0 space-y-1">
                                <li class="mb-1"><i class="bi bi-check-circle-fill text-primary me-2"></i>Choose your locations on a live map</li>
                                <li class="mb-1"><i class="bi bi-check-circle-fill text-primary me-2"></i>Set campaign dates &amp; budget</li>
                                <li><i class="bi bi-check-circle-fill text-primary me-2"></i>Upload your creative — we handle the rest</li>
                            </ul>
                        </div>
                        <div class="text-center mt-4 pt-3">
                            <a href="{{ route('contact', ['form' => 'advertiser']) }}" id="cta-advertise"
                               class="btn btn-primary w-100 py-3 fw-bold d-flex align-items-center justify-content-center gap-2"
                               style="border-radius: 14px; font-size: 0.95rem;">
                                <i class="bi bi-arrow-right-circle-fill"></i> Start Advertising
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Card 2: Become a Location Partner -->
                <div class="col-md-5 col-lg-4" data-aos="fade-left" data-aos-delay="200">
                    <div class="card choice-card h-100 border-0 p-5 d-flex flex-column justify-content-between" style="border-radius: 28px; background: #ffffff; box-shadow: 0 4px 24px rgba(5,150,105,0.08);">
                        <div class="text-center">
                            <div class="mb-4 mx-auto d-flex align-items-center justify-content-center" style="width: 80px; height: 80px; background: linear-gradient(135deg,#059669,#10b981); color: white; border-radius: 24px; font-size: 2.2rem; box-shadow: 0 8px 20px rgba(5,150,105,0.20);">
                                <i class="bi bi-building-up"></i>
                            </div>
                            <h3 class="fw-bold mb-3" style="color: #0b1c3f; font-family: 'Sora', sans-serif; font-size: 1.3rem;">Become a Location Partner</h3>
                            <p class="text-muted small leading-relaxed px-2 mb-4">Turn your venue's footfall into passive income. Host our digital signage screens and attract modern advertisers to your café, gym, salon, or clinic.</p>
                            <ul class="list-unstyled text-start text-muted small px-2 mb-0">
                                <li class="mb-1"><i class="bi bi-check-circle-fill text-emerald-500 me-2"></i>Free screen installation &amp; maintenance</li>
                                <li class="mb-1"><i class="bi bi-check-circle-fill text-emerald-500 me-2"></i>Earn passive revenue from advertisers</li>
                                <li><i class="bi bi-check-circle-fill text-emerald-500 me-2"></i>Full control via your partner portal</li>
                            </ul>
                        </div>
                        <div class="text-center mt-4 pt-3">
                            <a href="{{ route('contact', ['form' => 'partner']) }}" id="cta-partner"
                               class="btn w-100 py-3 fw-bold d-flex align-items-center justify-content-center gap-2"
                               style="border-radius: 14px; font-size: 0.95rem; background: #059669; color: white; border: none;">
                                <i class="bi bi-arrow-right-circle-fill"></i> Become a Partner
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            </div>
            
            <!-- New Personas CTA -->
            <div class="row justify-content-center mt-4" data-aos="fade-up" data-aos-delay="300">
                <div class="col-lg-10">
                    <div class="card border-0 p-4 p-md-5 text-center" style="border-radius: 24px; background: linear-gradient(135deg, #1e3a8a, #3b82f6); color: white; box-shadow: 0 10px 30px rgba(59, 130, 246, 0.2);">
                        <h3 class="fw-bold mb-3" style="font-family: 'Sora', sans-serif;">Partner With Hyper Adz or Get Digital Signage</h3>
                        <p class="mb-4 mx-auto" style="max-width: 600px; opacity: 0.9;">Are you looking to buy/rent digital signage for your own business, or do you want to become a Sales Partner and earn commission?</p>
                        <a href="{{ route('enquiry') }}" class="btn btn-light btn-lg fw-bold px-5" style="border-radius: 12px; color: #1e3a8a;">
                            Get Started
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>
@else
    {{-- Enquiry Form Section: Only shows when a form has been chosen --}}
    <section class="section-pad pt-5">
        <div class="container">
            <!-- Back to Selection Link -->
            <a href="{{ route('contact') }}" class="back-btn">
                <i class="bi bi-arrow-left"></i> Back to Selection
            </a>

            <div class="row g-5">
                <div class="col-lg-5" data-aos="fade-right">
                    <div class="contact-card">
                        <h2>{{ $formType === 'partner' ? 'Partner Enquiry' : 'Advertiser Enquiry' }}</h2>
                        <p class="text-muted mb-4">Fill the form and our team will get back to you within 24 hours.</p>
                        
                        <a href="tel:+919962099110" class="contact-link">
                            <i class="bi bi-telephone"></i>
                            <div><span class="text-muted small d-block">Call Us</span><strong>+91 99620 99110</strong></div>
                        </a>
                        <a href="https://wa.me/919962099110" target="_blank" rel="noopener" class="contact-link">
                            <i class="bi bi-whatsapp"></i>
                            <div><span class="text-muted small d-block">WhatsApp</span><strong>+91 99620 99110</strong></div>
                        </a>
                        <a href="mailto:support@hyperadz.in" class="contact-link">
                            <i class="bi bi-envelope"></i>
                            <div><span class="text-muted small d-block">Primary Email</span><strong>support@hyperadz.in</strong></div>
                        </a>
                        <div class="contact-link border-0">
                            <i class="bi bi-geo-alt"></i>
                            <div><span class="text-muted small d-block">Visit Us</span><strong>10, KK Nagar, 8th Street, Police Quarters Road,<br>Ganapathy, Coimbatore – 641006, Tamil Nadu</strong></div>
                        </div>

                        <!-- Form Context-Specific Image Mockup -->
                        <div class="mt-4 pt-2">
                            @if($formType === 'partner')
                                <img src="{{ asset('images/about_mission_showcase.png') }}" class="img-fluid rounded-4 shadow-sm" alt="Location Partner Screen Mockup" style="border: 1px solid rgba(0,0,0,0.06);">
                            @else
                                <img src="{{ asset('images/about_us_showcase.png') }}" class="img-fluid rounded-4 shadow-sm" alt="Advertiser Signage Screen" style="border: 1px solid rgba(0,0,0,0.06);">
                            @endif
                        </div>
                    </div>
                </div>
                <div class="col-lg-7" data-aos="fade-left">
                    <form class="contact-form" id="contact-us-form">
                        @csrf
                        <div id="contact-alert" class="alert d-none mb-4 rounded-3 text-xs" role="alert"></div>
                        <div class="row g-4">
                            <div class="col-md-6">
                                <label for="contact-name">Full Name <span class="text-danger">*</span></label>
                                <input class="form-control" id="contact-name" name="name" type="text" placeholder="John Doe" required>
                            </div>
                            <div class="col-md-6">
                                <label for="contact-phone">Phone Number <span class="text-danger">*</span></label>
                                <input class="form-control" id="contact-phone" name="phone" type="tel" placeholder="+91 99000 00000" required>
                            </div>
                            <div class="col-12">
                                <label for="contact-email">Email Address <span class="text-danger">*</span></label>
                                <input class="form-control" id="contact-email" name="email" type="email" placeholder="john@example.com" required>
                            </div>
                            
                            <!-- Hidden input for locking the form type context -->
                            <input type="hidden" id="contact-campaign-type" name="campaign_type" value="{{ $formType === 'partner' ? 'location_partner' : 'advertiser' }}">

                            <div class="col-12">
                                <label for="contact-message">Message / Details</label>
                                <textarea class="form-control" id="contact-message" name="message" rows="5" placeholder="Tell us about your brand, venue, or goals..."></textarea>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary btn-lg mt-4" id="contact-submit-btn">
                            <i class="bi bi-send"></i> <span id="contact-submit-text">Submit Enquiry</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </section>
@endif

<!-- Brands Slider Section -->
<x-brands-slider />

@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('contact-us-form');
        const alertBox = document.getElementById('contact-alert');
        const submitBtn = document.getElementById('contact-submit-btn');
        const submitText = document.getElementById('contact-submit-text');
        const leadTypeInput = document.getElementById('contact-campaign-type');

        if (form) {
            form.addEventListener('submit', function(event) {
                event.preventDefault();
                
                // Show loading status
                submitBtn.disabled = true;
                submitText.textContent = 'Submitting...';
                alertBox.className = 'alert d-none';

                const formData = {
                    _token: form.querySelector('input[name="_token"]').value,
                    name: document.getElementById('contact-name').value,
                    phone: document.getElementById('contact-phone').value,
                    email: document.getElementById('contact-email').value,
                    lead_type: leadTypeInput ? leadTypeInput.value : 'advertiser',
                    message: document.getElementById('contact-message').value,
                    source: 'contact_form'
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
                    submitText.textContent = 'Submit Request';

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
                    submitText.textContent = 'Submit Request';
                    alertBox.className = 'alert alert-danger p-3 rounded-3';
                    alertBox.textContent = 'Network error. Please try again later.';
                });
            });
        }
    });
</script>
@endpush
