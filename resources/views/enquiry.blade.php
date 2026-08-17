@extends('layouts.app', [
    'title' => 'Enquiry | Hyper Adz',
    'description' => 'Submit your enquiry for indoor digital signage or become a sales partner with Hyper Adz.'
])

@section('content')
<!-- Banner -->
<div class="subpage-banner" style="background-image: url('{{ asset('images/contact-banner.png') }}')">
    <div class="subpage-banner-overlay"></div>
    <div class="container text-center text-white position-relative" style="z-index: 5;">
        <span class="eyebrow" style="color: rgba(255, 255, 255, 0.9); background: rgba(255, 255, 255, 0.12); border: 1px solid rgba(255, 255, 255, 0.25);">Hyper Adz &nbsp;›&nbsp; Enquiry</span>
        <h1 class="text-white fw-bold mb-3" style="font-size: clamp(2.2rem, 5vw, 3.2rem); letter-spacing: -0.02em;">Let's Get Started</h1>
        <p class="text-white-50 mx-auto mb-0" style="max-width: 600px; font-size: 1.1rem; opacity: 0.85;">Tell us what you are interested in and our team will get in touch shortly.</p>
    </div>
</div>

<section class="section-pad pt-5 pb-5 bg-light">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8" data-aos="fade-up">
                <div class="card border-0 shadow-sm rounded-4 p-4 p-md-5 bg-white">
                    <h2 class="fw-bold mb-4" style="color: #0b1c3f;">Enquiry Form</h2>
                    
                    <form id="enquiry-form">
                        @csrf
                        <div id="enquiry-alert" class="alert d-none mb-4 rounded-3 text-sm" role="alert"></div>
                        
                        <div class="row g-4">
                            <div class="col-12">
                                <label for="enquiry-type" class="fw-semibold text-dark mb-2">What are you interested in? <span class="text-danger">*</span></label>
                                <select class="form-select form-select-lg" id="enquiry-type" name="lead_type" required>
                                    <option value="" disabled selected>Select an option...</option>
                                    <option value="digital_signage">Get Digital Signage for My Business</option>
                                    <option value="sales_partner">Become a Sales Partner</option>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label for="enquiry-name" class="fw-semibold text-dark mb-2">Full Name <span class="text-danger">*</span></label>
                                <input class="form-control form-control-lg" id="enquiry-name" name="name" type="text" placeholder="Your Name" required>
                            </div>
                            
                            <div class="col-md-6">
                                <label for="enquiry-phone" class="fw-semibold text-dark mb-2">Phone Number <span class="text-danger">*</span></label>
                                <input class="form-control form-control-lg" id="enquiry-phone" name="phone" type="tel" placeholder="+91 99000 00000" required>
                            </div>
                            
                            <div class="col-12">
                                <label for="enquiry-email" class="fw-semibold text-dark mb-2">Email Address <span class="text-danger">*</span></label>
                                <input class="form-control form-control-lg" id="enquiry-email" name="email" type="email" placeholder="you@example.com" required>
                            </div>
                            
                            <div class="col-12">
                                <label for="enquiry-message" class="fw-semibold text-dark mb-2">Message (Optional)</label>
                                <textarea class="form-control form-control-lg" id="enquiry-message" name="message" rows="4" placeholder="Any additional details..."></textarea>
                            </div>
                        </div>
                        
                        <button type="submit" class="btn btn-primary btn-lg mt-4 w-100 py-3 fw-bold" id="enquiry-submit-btn" style="border-radius: 14px;">
                            <i class="bi bi-send me-2"></i> <span id="enquiry-submit-text">Submit Enquiry</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('enquiry-form');
        const alertBox = document.getElementById('enquiry-alert');
        const submitBtn = document.getElementById('enquiry-submit-btn');
        const submitText = document.getElementById('enquiry-submit-text');

        if (form) {
            form.addEventListener('submit', function(event) {
                event.preventDefault();
                
                submitBtn.disabled = true;
                submitText.textContent = 'Submitting...';
                alertBox.className = 'alert d-none';

                const formData = {
                    _token: form.querySelector('input[name="_token"]').value,
                    lead_type: document.getElementById('enquiry-type').value,
                    name: document.getElementById('enquiry-name').value,
                    phone: document.getElementById('enquiry-phone').value,
                    email: document.getElementById('enquiry-email').value,
                    message: document.getElementById('enquiry-message').value,
                    source: 'enquiry_form'
                };

                fetch('{{ route("leads.store") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(formData)
                })
                .then(response => {
                    if (response.status === 429) {
                        return { status: 429, body: { message: "Too many requests. Please wait a minute before submitting again." } };
                    }
                    return response.json().then(data => ({ status: response.status, body: data }));
                })
                .then(res => {
                    submitBtn.disabled = false;
                    submitText.textContent = 'Submit Enquiry';

                    if (res.status === 201 || res.status === 200) {
                        alertBox.className = 'alert alert-success p-3 rounded-3';
                        alertBox.textContent = res.body.message;
                        form.reset();
                    } else if (res.status === 429) {
                        alertBox.className = 'alert alert-warning p-3 rounded-3';
                        alertBox.textContent = res.body.message;
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
                    submitText.textContent = 'Submit Enquiry';
                    alertBox.className = 'alert alert-danger p-3 rounded-3';
                    alertBox.textContent = 'Network error. Please try again later.';
                });
            });
        }
    });
</script>
@endpush
