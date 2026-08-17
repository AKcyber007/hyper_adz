<footer class="site-footer">
    <div class="container">
        <div class="row g-4 align-items-start">
            <div class="col-lg-4">
                <img class="footer-logo" src="{{ isset($globalBranding) && $globalBranding->footer_logo_path ? asset('storage/'.$globalBranding->footer_logo_path) : asset('images/hyperadz-banner-logo.svg') }}" alt="Hyper Adz">
                <p class="mt-3">{{ isset($globalSettings) && $globalSettings->company_description ? $globalSettings->company_description : 'Hyper Adz connects local brands to high-intent audiences through premium indoor screens across trusted partner venues across Coimbatore.' }}</p>
                <div class="d-flex gap-3 mt-3">
                    @if(isset($globalSocialLinks) && $globalSocialLinks->count() > 0)
                        @foreach($globalSocialLinks as $link)
                            @php
                                $icon = 'bi-link';
                                $platform = strtolower($link->platform);
                                if(str_contains($platform, 'instagram')) $icon = 'bi-instagram';
                                elseif(str_contains($platform, 'linkedin')) $icon = 'bi-linkedin';
                                elseif(str_contains($platform, 'facebook')) $icon = 'bi-facebook';
                                elseif(str_contains($platform, 'whatsapp')) $icon = 'bi-whatsapp';
                                elseif(str_contains($platform, 'twitter') || str_contains($platform, 'x')) $icon = 'bi-twitter-x';
                                elseif(str_contains($platform, 'youtube')) $icon = 'bi-youtube';
                            @endphp
                            <a href="{{ $link->url }}" target="_blank" rel="noopener" class="footer-social" aria-label="{{ $link->platform }}"><i class="bi {{ $icon }}"></i></a>
                        @endforeach
                    @else
                        <a href="https://www.instagram.com/hyperadz" target="_blank" rel="noopener" class="footer-social" aria-label="Instagram"><i class="bi bi-instagram"></i></a>
                        <a href="https://www.linkedin.com/company/hyperadz" target="_blank" rel="noopener" class="footer-social" aria-label="LinkedIn"><i class="bi bi-linkedin"></i></a>
                        <a href="https://wa.me/919962099110" target="_blank" rel="noopener" class="footer-social" aria-label="WhatsApp"><i class="bi bi-whatsapp"></i></a>
                    @endif
                </div>
            </div>
            <div class="col-6 col-lg-2">
                <h3>Company</h3>
                <a href="{{ route('about') }}" class="d-block mb-2 text-decoration-none">About Us</a>
                <a href="{{ route('why') }}" class="d-block mb-2 text-decoration-none">Why Hyper Adz</a>
                <a href="{{ route('services') }}" class="d-block mb-2 text-decoration-none">Our Services</a>
                <a href="{{ route('blog.index') }}" class="d-block mb-2 text-decoration-none">Blog</a>
            </div>
            <div class="col-6 col-lg-2">
                <h3>Platform</h3>
                <a href="{{ route('network') }}" class="d-block mb-2 text-decoration-none">Media Network</a>
                <a href="{{ route('faqs') }}" class="d-block mb-2 text-decoration-none">FAQs</a>
                <a href="{{ route('contact') }}" class="d-block mb-2 text-decoration-none">Plan a Campaign</a>
                <a href="{{ route('contact') }}" class="d-block mb-2 text-decoration-none">Get a Quote</a>
                <a href="{{ route('contact') }}" class="d-block mb-2 text-decoration-none">Customer Support</a>
            </div>
            <div class="col-lg-4">
                <h3>Contact</h3>
                @if(isset($globalSettings) && $globalSettings->address)
                    <p class="mb-2"><i class="bi bi-geo-alt me-2 text-primary"></i> {!! nl2br(e($globalSettings->address)) !!}</p>
                @else
                    <p class="mb-2"><i class="bi bi-geo-alt me-2 text-primary"></i> 10, KK Nagar, 8th Street, Police Quarters Road,<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Ganapathy, Coimbatore - 641006, Tamil Nadu</p>
                @endif

                @if(isset($globalSettings) && $globalSettings->primary_email)
                    <a href="mailto:{{ $globalSettings->primary_email }}" class="d-block mb-2 text-decoration-none"><i class="bi bi-envelope me-2 text-primary"></i> {{ $globalSettings->primary_email }}</a>
                @endif
                
                @if(isset($globalSettings) && $globalSettings->secondary_email)
                    <a href="mailto:{{ $globalSettings->secondary_email }}" class="d-block mb-2 text-decoration-none"><i class="bi bi-envelope me-2 text-primary"></i> {{ $globalSettings->secondary_email }}</a>
                @elseif(!isset($globalSettings))
                    <a href="mailto:support@hyperadz.in" class="d-block mb-2 text-decoration-none"><i class="bi bi-envelope me-2 text-primary"></i> support@hyperadz.in</a>
                    <a href="mailto:connect.hyperadz@gmail.com" class="d-block mb-2 text-decoration-none"><i class="bi bi-envelope me-2 text-primary"></i> connect.hyperadz@gmail.com</a>
                @endif

                @if(isset($globalSettings) && $globalSettings->phone)
                    <a href="tel:{{ preg_replace('/[^0-9+]/', '', $globalSettings->phone) }}" class="d-block mb-2 text-decoration-none"><i class="bi bi-telephone me-2 text-primary"></i> {{ $globalSettings->phone }}</a>
                @elseif(!isset($globalSettings))
                    <a href="tel:+919962099110" class="d-block mb-2 text-decoration-none"><i class="bi bi-telephone me-2 text-primary"></i> +91 99620 99110</a>
                @endif

                @if(isset($globalSettings) && $globalSettings->whatsapp)
                    <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $globalSettings->whatsapp) }}" target="_blank" rel="noopener" class="d-block mb-2 text-decoration-none"><i class="bi bi-whatsapp me-2 text-primary"></i> WhatsApp Us</a>
                @elseif(!isset($globalSettings))
                    <a href="https://wa.me/919962099110" target="_blank" rel="noopener" class="d-block mb-2 text-decoration-none"><i class="bi bi-whatsapp me-2 text-primary"></i> WhatsApp Us</a>
                @endif

                @if(isset($globalSettings) && $globalSettings->gst_number)
                    <p class="mt-2 small" style="opacity:0.6;">GST: {{ $globalSettings->gst_number }}</p>
                @elseif(!isset($globalSettings))
                    <p class="mt-2 small" style="opacity:0.6;">GST: 33AIWPM0841K1ZX</p>
                @endif
                
                @if(isset($globalSettings) && $globalSettings->business_hours)
                    <p class="mt-2 small" style="opacity:0.6;">Hours: {{ $globalSettings->business_hours }}</p>
                @endif
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
