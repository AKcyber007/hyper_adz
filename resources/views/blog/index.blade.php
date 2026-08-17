@extends('layouts.app', [
    'title' => 'Hyper Adz Blog | Advertising Insights & Trends',
    'description' => 'Industry insights, advertising trends, campaign strategies, location marketing, and business growth resources from Hyper Adz.'
])

@section('content')
<!-- Premium Custom Banner (Inspired by Wejha design) -->
<div class="py-5 bg-grid-pattern" style="background-color: #0B1320; min-height: 380px; display: flex; align-items: center; position: relative;">
    <div class="subpage-banner-overlay" style="background: radial-gradient(circle at 80% 50%, rgba(17, 85, 204, 0.12) 0%, transparent 70%);"></div>
    <div class="container position-relative" style="z-index: 5;">
        <span class="text-[11px] fw-bold text-[#C4F135] uppercase tracking-widest d-block mb-3" style="letter-spacing: 0.18em; font-size: 0.72rem;">OUR POV — THE BLOG</span>
        
        <h1 class="text-white fw-extrabold mb-3" style="font-size: clamp(3rem, 6vw, 4.8rem); line-height: 1.05; letter-spacing: -0.03em;">
            <span class="d-block text-white" style="font-family: 'Sora', sans-serif; font-weight: 800;">Hyper Adz</span>
            <span class="d-block text-[#C4F135] fst-italic" style="font-family: 'Sora', Georgia, serif; font-weight: 700; margin-top: -5px;">Blog.</span>
        </h1>
        
        <p class="text-white-50 mt-4 mb-0" style="max-width: 680px; font-size: 1.15rem; opacity: 0.8; line-height: 1.6; font-weight: 400;">
            Thought leadership, industry insights, and original perspectives on the evolution of digital out-of-home advertising, marketing strategies, and local business growth.
        </p>
    </div>
</div>

<div class="bg-grid-pattern">
    <div class="gradient-blob gradient-blob-1"></div>
    <div class="gradient-blob gradient-blob-2"></div>

    <section class="section-pad pt-5">
        <div class="container">
            <!-- Search & Actions Row -->
            <div class="row mb-5 g-4 align-items-center">
                <div class="col-md-8 col-lg-9">
                    <form method="GET" action="{{ route('blog.index') }}" class="position-relative">
                        <i class="bi bi-search position-absolute text-muted" style="left: 18px; top: 50%; transform: translateY(-50%); font-size: 1.1rem;"></i>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search blogs, articles, strategies..." class="form-control py-3 shadow-sm border-0 bg-white" style="border-radius: 16px; padding-left: 50px; font-size: 1rem; border: 1px solid var(--ha-border) !important;">
                    </form>
                </div>
                @if(request()->filled('search'))
                    <div class="col-md-4 col-lg-3">
                        <a href="{{ route('blog.index') }}" class="btn btn-ghost py-3 w-100 rounded-3" style="border-radius: 16px; border: 1px solid var(--ha-border) !important; background:#fff;"><i class="bi bi-x-circle me-1.5"></i> Clear Search</a>
                    </div>
                @endif
            </div>

            <!-- Featured Blog Post Section (Only shown on page 1 without search) -->
            @if($featured && !request()->filled('search') && !request()->has('page'))
                <div class="row g-4 mb-5" data-aos="fade-up">
                    <div class="col-12">
                        <div class="card border-0 shadow-lg overflow-hidden position-relative" style="border-radius: 32px; background: #FFF; min-height: 400px; border: 1px solid rgba(17, 85, 204, 0.08) !important;">
                            <div class="row g-0 h-100">
                                <div class="col-lg-6 position-relative min-vh-30" style="min-height: 350px;">
                                    @if($featured->featured_image)
                                        <img src="{{ asset('storage/' . $featured->featured_image) }}" class="w-full h-full object-cover position-absolute" alt="{{ $featured->title }}" style="object-position: center; inset: 0;">
                                    @else
                                        <div class="w-full h-full bg-slate-100 flex items-center justify-center position-absolute" style="inset: 0;">
                                            <i class="bi bi-journal-text text-slate-350 text-5xl"></i>
                                        </div>
                                    @endif
                                    <span class="badge bg-primary position-absolute py-2.5 px-3 uppercase tracking-wider text-xxs font-bold shadow-md" style="top: 20px; left: 20px; border-radius: 8px;">FEATURED ARTICLE</span>
                                </div>
                                <div class="col-lg-6 d-flex flex-column justify-content-center p-5">
                                    <div class="text-muted text-xxs font-bold mb-2 flex items-center gap-2">
                                        <span>BY {{ strtoupper($featured->author_name) }}</span>
                                        <span>•</span>
                                        <span>{{ $featured->publish_date ? $featured->publish_date->format('M d, Y') : $featured->created_at->format('M d, Y') }}</span>
                                        <span>•</span>
                                        <span class="text-primary"><i class="bi bi-clock me-1"></i> {{ $featured->reading_time }}</span>
                                    </div>
                                    <h2 class="fw-bold mb-3 h2-serif" style="color: var(--ha-ink); font-size: 1.85rem; line-height: 1.3;">
                                        <a href="{{ route('blog.show', $featured->slug) }}" class="text-decoration-none text-dark hover:text-primary transition-all">{{ $featured->title }}</a>
                                    </h2>
                                    <p class="text-muted mb-4" style="font-size: 1rem; line-height: 1.6;">{{ $featured->short_description }}</p>
                                    <div>
                                        <a href="{{ route('blog.show', $featured->slug) }}" class="btn btn-primary px-4 py-2.5 rounded-xl font-bold transition-all"><i class="bi bi-book-open me-2"></i> Read Article</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Search Status metadata -->
            @if(request()->filled('search'))
                <div class="mb-4">
                    <h3 class="fw-bold text-slate-800" style="font-size: 1.3rem;">
                        Search results for "<span class="text-primary">{{ request('search') }}</span>"
                    </h3>
                    <p class="text-muted small">{{ $blogs->total() }} matches found.</p>
                </div>
            @endif

            <!-- Main Articles Grid -->
            <div class="row g-4" id="blogs-grid">
                @forelse($blogs as $blog)
                    <div class="col-md-6 col-lg-4">
                        <div class="card h-100 border-0 shadow-sm overflow-hidden hover-card" style="border-radius: 24px; background: #FFF; border: 1px solid rgba(17, 85, 204, 0.05) !important; transition: all 0.3s;">
                            <!-- Image Wrap -->
                            <div class="position-relative overflow-hidden" style="height: 220px;">
                                @if($blog->featured_image)
                                    <img src="{{ asset('storage/' . $blog->featured_image) }}" class="w-full h-full object-cover hover-scale" alt="{{ $blog->title }}" style="transition: transform 0.5s;">
                                @else
                                    <div class="w-full h-full bg-slate-100 flex items-center justify-center">
                                        <i class="bi bi-image text-slate-400 text-3xl"></i>
                                    </div>
                                @endif
                                <span class="badge bg-light text-dark position-absolute border py-1.5 px-2.5 font-bold" style="bottom: 12px; left: 12px; font-size: 0.72rem; border-radius: 8px;">
                                    <i class="bi bi-clock me-1 text-primary"></i> {{ $blog->reading_time }}
                                </span>
                            </div>

                            <!-- Body -->
                            <div class="card-body p-4 d-flex flex-column justify-content-between">
                                <div>
                                    <div class="text-muted text-xxs font-bold uppercase tracking-wider mb-2 flex items-center gap-1.5">
                                        <span>BY {{ $blog->author_name }}</span>
                                        <span>•</span>
                                        <span>{{ $blog->publish_date ? $blog->publish_date->format('M d, Y') : $blog->created_at->format('M d, Y') }}</span>
                                    </div>
                                    <h4 class="card-title fw-bold mb-2.5" style="font-size: 1.15rem; line-height: 1.4; color: var(--ha-ink);">
                                        <a href="{{ route('blog.show', $blog->slug) }}" class="text-decoration-none text-dark hover:text-primary transition-all">{{ $blog->title }}</a>
                                    </h4>
                                    <p class="card-text text-muted text-sm line-clamp-3 mb-4" style="line-height: 1.5;">{{ $blog->short_description }}</p>
                                </div>
                                <div>
                                    <a href="{{ route('blog.show', $blog->slug) }}" class="text-primary text-decoration-none font-bold text-sm hover-arrow">
                                        Read More <i class="bi bi-arrow-right ms-1"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12 text-center py-5 bg-white rounded-3xl border" style="border-color: var(--ha-border) !important; border-radius: 24px;">
                        <i class="bi bi-journal-x text-4xl text-muted block mb-3 opacity-40"></i>
                        <h4 class="fw-bold">No Articles Found</h4>
                        <p class="text-muted mb-0">We couldn't find any published blog articles matching your parameters.</p>
                    </div>
                @endforelse
            </div>

            <!-- Pagination Links -->
            @if($blogs->hasPages())
                <div class="d-flex justify-content-center mt-5">
                    {{ $blogs->links() }}
                </div>
            @endif
        </div>
    </section>
</div>

<x-cta title="Want to connect your business?" text="Hyper Adz connects brands with premium indoor locations across high traffic venues. Launch a campaign today." />
@endsection

@push('styles')
<style>
    .hover-card:hover {
        transform: translateY(-8px);
        box-shadow: var(--ha-shadow) !important;
    }
    .hover-card:hover .hover-scale {
        transform: scale(1.06);
    }
    .h2-serif a {
        font-family: 'Sora', sans-serif;
    }
    .hover-arrow i {
        transition: transform 0.25s;
    }
    .hover-arrow:hover i {
        transform: translateX(4px);
    }
</style>
@endpush
