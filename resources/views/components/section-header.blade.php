@props(['eyebrow' => '', 'title', 'text' => '', 'align' => 'center'])

<div class="section-header text-{{ $align }}" data-aos="fade-up">
    @if($eyebrow)
        <span class="eyebrow">{{ $eyebrow }}</span>
    @endif
    <h2>{{ $title }}</h2>
    @if($text)
        <p>{{ $text }}</p>
    @endif
</div>
