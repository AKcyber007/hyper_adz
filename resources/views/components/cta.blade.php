@props(['title', 'text', 'button' => 'Start a Conversation', 'href' => null])

<section class="cta-band" data-aos="fade-up">
    <div class="container">
        <div class="cta-glass-container">
            <div class="cta-inner">
                <div>
                    <span class="eyebrow text-white mb-2" style="background: rgba(255, 255, 255, 0.15); border-color: rgba(255, 255, 255, 0.25);">Campaign Ready</span>
                    <h2>{{ $title }}</h2>
                    <p>{{ $text }}</p>
                </div>
                <a class="btn btn-light btn-lg" href="{{ $href ?? route('contact') }}"><i class="bi bi-arrow-right-circle"></i> {{ $button }}</a>
            </div>
        </div>
    </div>
</section>
