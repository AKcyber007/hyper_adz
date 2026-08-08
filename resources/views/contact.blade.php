@extends('layouts.app', [
    'title' => 'Contact Hyper Adz | Plan an Indoor Digital Campaign',
    'description' => 'Contact Hyper Adz in Coimbatore for indoor digital advertising, signage rental, screen sales, and campaign planning.'
])

@section('content')
<div class="subpage-banner" style="background-image: url('{{ asset('images/contact-banner.png') }}')">
    <div class="subpage-banner-overlay"></div>
    <div class="container text-center text-white position-relative" style="z-index: 5;">
        <span class="eyebrow" style="color: rgba(255, 255, 255, 0.9); background: rgba(255, 255, 255, 0.12); border: 1px solid rgba(255, 255, 255, 0.25);">Hyper Adz &nbsp;›&nbsp; Contact Us</span>
        <h1 class="text-white fw-bold mb-3" style="font-size: clamp(2.5rem, 5vw, 3.5rem); letter-spacing: -0.02em;">Contact Us</h1>
        <p class="text-white-50 mx-auto mb-0" style="max-width: 600px; font-size: 1.1rem; opacity: 0.85;">Share your campaign goal and the Hyper Adz team will help shape the right indoor media plan.</p>
    </div>
</div>

<div class="bg-grid-pattern">
    <div class="gradient-blob gradient-blob-1"></div>
    <div class="gradient-blob gradient-blob-2"></div>

    <section class="section-pad pt-5">
        <div class="container">
            <div class="row g-5">
                <div class="col-lg-5" data-aos="fade-right">
                    <div class="contact-card">
                        <h2>Get in Touch</h2>
                        <p class="text-muted mb-4">Have questions about screen availability, campaign planning, or venue partnerships? Reach out directly — we're happy to help.</p>
                        
                        <a href="tel:+919962099110" class="contact-link">
                            <i class="bi bi-telephone"></i>
                            <div>
                                <span class="text-muted small d-block">Call Us</span>
                                <strong>+91 99620 99110</strong>
                            </div>
                        </a>
                        
                        <a href="https://wa.me/919962099110" target="_blank" rel="noopener" class="contact-link">
                            <i class="bi bi-whatsapp"></i>
                            <div>
                                <span class="text-muted small d-block">WhatsApp</span>
                                <strong>+91 99620 99110</strong>
                            </div>
                        </a>

                        <a href="mailto:support@hyperadz.in" class="contact-link">
                            <i class="bi bi-envelope"></i>
                            <div>
                                <span class="text-muted small d-block">Primary Email</span>
                                <strong>support@hyperadz.in</strong>
                            </div>
                        </a>

                        <a href="mailto:connect.hyperadz@gmail.com" class="contact-link">
                            <i class="bi bi-envelope"></i>
                            <div>
                                <span class="text-muted small d-block">Alt Email</span>
                                <strong>connect.hyperadz@gmail.com</strong>
                            </div>
                        </a>
                        
                        <div class="contact-link border-0">
                            <i class="bi bi-geo-alt"></i>
                            <div>
                                <span class="text-muted small d-block">Visit Us</span>
                                <strong>10, KK Nagar, 8th Street, Police Quarters Road,<br>Ganapathy, Coimbatore – 641006, Tamil Nadu</strong>
                            </div>
                        </div>
                        
                        <div class="map-placeholder">
                            <i class="bi bi-map-fill fs-2 mb-2"></i>
                            <span>Coimbatore, Tamil Nadu</span>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-7" data-aos="fade-left">
                    <form class="contact-form">
                        <div class="row g-4">
                            <div class="col-md-6">
                                <label>Full Name</label>
                                <input class="form-control" type="text" placeholder="John Doe">
                            </div>
                            <div class="col-md-6">
                                <label>Phone Number</label>
                                <input class="form-control" type="tel" placeholder="+91 99000 00000">
                            </div>
                            <div class="col-md-6">
                                <label>Email Address</label>
                                <input class="form-control" type="email" placeholder="john@example.com">
                            </div>
                            <div class="col-md-6">
                                <label>Campaign Type</label>
                                <select class="form-select">
                                    <option>Indoor digital advertising</option>
                                    <option>Signage screen sales</option>
                                    <option>Event screen rental</option>
                                </select>
                            </div>
                            <div class="col-12">
                                <label>Message / Campaign Details</label>
                                <textarea class="form-control" rows="5" placeholder="Tell us about your brand, budget, and campaign goals..."></textarea>
                            </div>
                        </div>
                        <button type="button" class="btn btn-primary btn-lg mt-4"><i class="bi bi-send"></i> Submit Request</button>
                    </form>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection
