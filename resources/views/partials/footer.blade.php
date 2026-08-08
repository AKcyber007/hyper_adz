<footer class="site-footer">
    <div class="container">
        <div class="row g-4 align-items-start">
            <div class="col-lg-4">
                <img class="footer-logo" src="{{ asset('images/hyperadz-banner-logo.svg') }}" alt="Hyper Adz">
                <p class="mt-3">Hyper Adz connects local brands to high-intent audiences through premium indoor screens across trusted partner venues across Coimbatore.</p>
                <div class="d-flex gap-3 mt-3">
                    <a href="https://www.instagram.com/hyperadz" target="_blank" rel="noopener" class="footer-social" aria-label="Instagram"><i class="bi bi-instagram"></i></a>
                    <a href="https://www.linkedin.com/company/hyperadz" target="_blank" rel="noopener" class="footer-social" aria-label="LinkedIn"><i class="bi bi-linkedin"></i></a>
                    <a href="https://wa.me/919962099110" target="_blank" rel="noopener" class="footer-social" aria-label="WhatsApp"><i class="bi bi-whatsapp"></i></a>
                </div>
            </div>
            <div class="col-6 col-lg-2">
                <h3>Company</h3>
                <a href="{{ route('about') }}" class="d-block mb-2 text-decoration-none">About Us</a>
                <a href="{{ route('why') }}" class="d-block mb-2 text-decoration-none">Why Hyper Adz</a>
                <a href="{{ route('services') }}" class="d-block mb-2 text-decoration-none">Our Services</a>
                <a href="{{ route('partner') }}" class="d-block mb-2 text-decoration-none">Become a Partner</a>
            </div>
            <div class="col-6 col-lg-2">
                <h3>Platform</h3>
                <a href="{{ route('network') }}" class="d-block mb-2 text-decoration-none">Media Network</a>
                <a href="{{ route('contact') }}" class="d-block mb-2 text-decoration-none">Plan a Campaign</a>
                <a href="{{ route('contact') }}" class="d-block mb-2 text-decoration-none">Get a Quote</a>
                <a href="{{ route('contact') }}" class="d-block mb-2 text-decoration-none">Customer Support</a>
            </div>
            <div class="col-lg-4">
                <h3>Contact</h3>
                <p class="mb-2"><i class="bi bi-geo-alt me-2 text-primary"></i> 10, KK Nagar, 8th Street, Police Quarters Road,<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Ganapathy, Coimbatore - 641006, Tamil Nadu</p>
                <a href="mailto:support@hyperadz.in" class="d-block mb-2 text-decoration-none"><i class="bi bi-envelope me-2 text-primary"></i> support@hyperadz.in</a>
                <a href="mailto:connect.hyperadz@gmail.com" class="d-block mb-2 text-decoration-none"><i class="bi bi-envelope me-2 text-primary"></i> connect.hyperadz@gmail.com</a>
                <a href="tel:+919962099110" class="d-block mb-2 text-decoration-none"><i class="bi bi-telephone me-2 text-primary"></i> +91 99620 99110</a>
                <a href="https://wa.me/919962099110" target="_blank" rel="noopener" class="d-block mb-2 text-decoration-none"><i class="bi bi-whatsapp me-2 text-primary"></i> WhatsApp Us</a>
                <p class="mt-2 small" style="opacity:0.6;">GST: 33AIWPM0841K1ZX</p>
            </div>
        </div>
        <div class="footer-bottom">
            <span>© {{ date('Y') }} Hyper Adz | Multitude Solutions. All rights reserved.</span>
            <span>
                <a href="{{ route('privacy') }}" class="text-decoration-none">Privacy Policy</a>
                <a href="{{ route('terms') }}" class="text-decoration-none">Terms &amp; Conditions</a>
                <a href="{{ route('refund') }}" class="text-decoration-none">Refund Policy</a>
                <a href="{{ route('cookie') }}" class="text-decoration-none">Cookie Policy</a>
            </span>
        </div>
    </div>
</footer>
