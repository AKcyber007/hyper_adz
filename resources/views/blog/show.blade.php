@extends('layouts.app', [
    'title' => ($blog->seo_title ?? $blog->title) . ' | Hyper Adz Blog',
    'description' => $blog->seo_description ?? $blog->short_description
])

@section('content')
<article class="bg-grid-pattern py-5">
    <div class="gradient-blob gradient-blob-1"></div>
    <div class="gradient-blob gradient-blob-2"></div>

    <div class="container position-relative" style="z-index: 5;">
        <!-- Breadcrumbs -->
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-decoration-none text-muted">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('blog.index') }}" class="text-decoration-none text-muted">Blog</a></li>
                <li class="breadcrumb-item active text-primary font-bold" aria-current="page">{{ Str::limit($blog->title, 40) }}</li>
            </ol>
        </nav>

        <div class="row g-5">
            <!-- Left Side: Main Blog Content -->
            <div class="col-lg-8">
                <!-- Blog Main Box -->
                <div class="bg-white p-4 p-md-5 border rounded-3xl shadow-sm space-y-4" style="border-color: var(--ha-border) !important; border-radius: 28px;">
                    <!-- Metadata Header -->
                    <div class="text-muted text-xxs font-bold uppercase tracking-wider mb-2 flex flex-wrap items-center gap-2">
                        <span>BY {{ $blog->author_name }}</span>
                        <span>•</span>
                        <span>{{ $blog->publish_date ? $blog->publish_date->format('M d, Y') : $blog->created_at->format('M d, Y') }}</span>
                        <span>•</span>
                        <span class="text-primary"><i class="bi bi-clock me-1"></i> {{ $blog->reading_time }}</span>
                    </div>

                    <!-- Heading -->
                    <h1 class="fw-bold mb-4" style="font-size: clamp(1.8rem, 4vw, 2.5rem); line-height: 1.25; color: var(--ha-ink);">
                        {{ $blog->title }}
                    </h1>

                    <!-- Featured Image -->
                    @if($blog->featured_image)
                        <div class="mb-4 overflow-hidden rounded-2xl border" style="max-height: 450px;">
                            <img src="{{ asset('storage/' . $blog->featured_image) }}" class="w-full h-full object-cover" alt="{{ $blog->title }}">
                        </div>
                    @endif

                    <!-- Detailed Content (Rich HTML output) -->
                    <div class="blog-rendered-content pt-3" style="line-height: 1.8; font-size: 1.05rem; color: #334155;">
                        {!! $blog->content !!}
                    </div>

                    <!-- Share Section -->
                    <div class="border-top pt-4 mt-5 flex items-center justify-between flex-wrap gap-3">
                        <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Share This Article:</span>
                        <div class="d-flex gap-2">
                            <!-- Share to WhatsApp -->
                            <a href="https://api.whatsapp.com/send?text={{ urlencode($blog->title . ' - ' . url()->current()) }}" target="_blank" rel="noopener" class="btn btn-outline-success btn-sm rounded-xl px-3 py-2 flex items-center gap-1.5 font-semibold text-xs" style="border-color: #25D366; color: #25D366;">
                                <i class="bi bi-whatsapp"></i> WhatsApp
                            </a>
                            <!-- Share to LinkedIn -->
                            <a href="https://www.linkedin.com/sharing/share-offsite/?url={{ urlencode(url()->current()) }}" target="_blank" rel="noopener" class="btn btn-outline-primary btn-sm rounded-xl px-3 py-2 flex items-center gap-1.5 font-semibold text-xs" style="border-color: #0A66C2; color: #0A66C2;">
                                <i class="bi bi-linkedin"></i> LinkedIn
                            </a>
                            <!-- Share to Twitter -->
                            <a href="https://twitter.com/intent/tweet?text={{ urlencode($blog->title) }}&url={{ urlencode(url()->current()) }}" target="_blank" rel="noopener" class="btn btn-outline-dark btn-sm rounded-xl px-3 py-2 flex items-center gap-1.5 font-semibold text-xs" style="border-color: #000; color: #000;">
                                <i class="bi bi-twitter-x"></i> Twitter
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Side: Sidebar (Author profile card / call to action) -->
            <div class="col-lg-4">
                <div class="sticky-top" style="top: 100px; z-index: 10; space-y-6;">
                    <!-- Author / Hyper Adz Pitch Box -->
                    <div class="bg-white p-4 border rounded-3xl shadow-sm text-center mb-4" style="border-color: var(--ha-border) !important; border-radius: 24px;">
                        <div class="w-16 h-16 bg-blue-50 text-[#1155CC] rounded-full flex items-center justify-center text-3xl mx-auto mb-3 shadow-inner">
                            <i class="bi bi-person-badge"></i>
                        </div>
                        <h5 class="fw-bold mb-1" style="color: var(--ha-ink);">{{ $blog->author_name }}</h5>
                        <p class="text-slate-400 text-xxs font-bold uppercase tracking-wider mb-3">Hyper Adz Author</p>
                        <p class="text-muted text-sm leading-relaxed mb-0">
                            Sharing thought leadership on indoor signage, programmatic networks, and modern advertising metrics across Coimbatore.
                        </p>
                    </div>

                    <!-- Banner Pitch Card -->
                    <div class="bg-dark text-white p-4 rounded-3xl shadow-lg border-0 position-relative overflow-hidden text-center" style="border-radius: 24px; background: linear-gradient(135deg, #0A1628 0%, #1A2E4C 100%) !important;">
                        <div class="absolute right-0 bottom-0 w-24 h-24 bg-blue-500/5 rounded-full blur-2xl"></div>
                        <i class="bi bi-display text-4xl text-primary mb-3 block"></i>
                        <h4 class="fw-bold mb-2 text-white" style="font-size: 1.15rem;">Advertise with Hyper Adz</h4>
                        <p class="text-white-50 text-xs leading-relaxed mb-4">
                            Get your brand featured on our premium cloud-connected digital screen networks in high footfall locations.
                        </p>
                        <a href="{{ route('contact') }}" class="btn btn-primary btn-sm rounded-xl py-2 px-3 font-bold w-100">Plan a Campaign</a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Related Articles Section (Latest published excluding current) -->
        @if($related->count() > 0)
            <div class="mt-5 pt-4">
                <h3 class="fw-bold mb-4 flex items-center gap-2" style="font-size: 1.6rem; color: var(--ha-ink);">
                    <i class="bi bi-bookmarks text-primary"></i> Latest Insights
                </h3>
                <div class="row g-4">
                    @foreach($related as $rel)
                        <div class="col-md-6 col-lg-4">
                            <div class="card h-100 border-0 shadow-sm overflow-hidden hover-card" style="border-radius: 20px; background: #FFF; border: 1px solid rgba(17, 85, 204, 0.05) !important;">
                                <!-- Image Wrap -->
                                <div class="position-relative overflow-hidden" style="height: 180px;">
                                    @if($rel->featured_image)
                                        <img src="{{ asset('storage/' . $rel->featured_image) }}" class="w-full h-full object-cover" alt="{{ $rel->title }}">
                                    @else
                                        <div class="w-full h-full bg-slate-100 flex items-center justify-center">
                                            <i class="bi bi-image text-slate-400 text-3xl"></i>
                                        </div>
                                    @endif
                                    <span class="badge bg-light text-dark position-absolute border py-1 px-2 font-bold" style="bottom: 10px; left: 10px; font-size: 0.68rem; border-radius: 6px;">
                                        {{ $rel->reading_time }}
                                    </span>
                                </div>

                                <!-- Body -->
                                <div class="card-body p-4 d-flex flex-column justify-content-between">
                                    <div>
                                        <div class="text-muted text-xxs font-bold uppercase tracking-wider mb-2 flex items-center gap-1">
                                            <span>{{ $rel->publish_date ? $rel->publish_date->format('M d, Y') : $rel->created_at->format('M d, Y') }}</span>
                                        </div>
                                        <h5 class="card-title fw-bold mb-2" style="font-size: 1rem; line-height: 1.4; color: var(--ha-ink);">
                                            <a href="{{ route('blog.show', $rel->slug) }}" class="text-decoration-none text-dark hover:text-primary transition-all">{{ $rel->title }}</a>
                                        </h5>
                                        <p class="card-text text-muted text-xs line-clamp-3 mb-4" style="line-height: 1.5;">{{ $rel->short_description }}</p>
                                    </div>
                                    <div>
                                        <a href="{{ route('blog.show', $rel->slug) }}" class="text-primary text-decoration-none font-bold text-xs">
                                            Read Article <i class="bi bi-arrow-right ms-0.5"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</article>
@endsection

@push('styles')
<style>
    .hover-card:hover {
        transform: translateY(-5px);
        box-shadow: var(--ha-shadow-sm) !important;
    }
    /* Rendered HTML styling for blog contents */
    .blog-rendered-content h2 {
        font-size: 1.5rem;
        font-weight: 700;
        margin-top: 25px;
        margin-bottom: 12px;
        color: var(--ha-ink);
    }
    .blog-rendered-content h3 {
        font-size: 1.25rem;
        font-weight: 700;
        margin-top: 20px;
        margin-bottom: 10px;
        color: var(--ha-ink);
    }
    .blog-rendered-content p {
        margin-bottom: 16px;
        line-height: 1.75;
        font-size: 1rem;
        color: #334155;
    }
    .blog-rendered-content ul, .blog-rendered-content ol {
        margin-bottom: 16px;
        padding-left: 24px;
    }
    .blog-rendered-content li {
        margin-bottom: 6px;
    }
    .blog-rendered-content img {
        max-width: 100%;
        height: auto;
        border-radius: 12px;
        margin: 20px 0;
    }
    .blog-rendered-content iframe {
        max-width: 100%;
        border-radius: 12px;
        margin: 20px 0;
    }
    .breadcrumb-item + .breadcrumb-item::before {
        content: var(--bs-breadcrumb-divider, "›") !important;
        font-size: 1.1rem;
        line-height: 1;
        vertical-align: middle;
    }
</style>
@endpush
