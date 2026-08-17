@props(['icon', 'title', 'text', 'label' => 'Discuss this service', 'link' => route('contact')])

<article class="service-card" data-aos="fade-up">
    <span class="icon-pill"><i class="bi {{ $icon }}"></i></span>
    <h3>{{ $title }}</h3>
    <p>{{ $text }}</p>
    <a href="{{ $link }}">{{ $label }} <i class="bi bi-arrow-right"></i></a>
</article>
