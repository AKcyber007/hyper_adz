@extends('layouts.app', [
    'title' => 'Frequently Asked Questions (FAQ) | Hyper Adz',
    'description' => 'Find answers to common questions about Hyper Adz advertising campaigns, location partnerships, onboarding, payments, and campaign reports.'
])

@section('content')
<!-- Hero Section -->
<div class="subpage-banner" style="background-image: url('{{ asset('images/faq-banner.png') }}')">
    <div class="subpage-banner-overlay"></div>
    <div class="container text-center text-white position-relative" style="z-index: 5;">
        <span class="eyebrow" style="color: rgba(255, 255, 255, 0.9); background: rgba(255, 255, 255, 0.12); border: 1px solid rgba(255, 255, 255, 0.25);">Support Center</span>
        <h1 class="text-white fw-bold mb-3" style="font-size: clamp(2.3rem, 5vw, 3.5rem); letter-spacing: -0.02em;">Frequently Asked Questions</h1>
        <p class="text-white-50 mx-auto mb-0" style="max-width: 750px; font-size: 1.1rem; opacity: 0.85; line-height: 1.5;">
            Find answers about advertising campaigns, location partnerships, payments, campaign reports, onboarding, and platform operations.
        </p>
    </div>
</div>

<div class="bg-grid-pattern">
    <div class="gradient-blob gradient-blob-1"></div>
    <div class="gradient-blob gradient-blob-2"></div>

    <section class="section-pad pt-5">
        <div class="container">
            <!-- Search & Mobile Category Dropdown Row -->
            <div class="row mb-5 g-4 align-items-center">
                <!-- Search bar -->
                <div class="col-md-7 col-lg-8">
                    <div class="position-relative">
                        <i class="bi bi-search position-absolute text-muted" style="left: 18px; top: 50%; transform: translateY(-50%); font-size: 1.1rem;"></i>
                        <input type="text" id="faq-search" placeholder="Search FAQs..." class="form-control py-3 shadow-sm border-0 bg-white" style="border-radius: 16px; padding-left: 50px; font-size: 1rem; border: 1px solid var(--ha-border) !important;">
                    </div>
                </div>

                <!-- Mobile Category Dropdown (Visible on Mobile Only) -->
                <div class="col-md-5 col-lg-4 d-md-none">
                    <select id="mobile-category-select" class="form-select py-3 shadow-sm border-0 bg-white" style="border-radius: 16px; font-size: 1rem; border: 1px solid var(--ha-border) !important;">
                        <option value="">Jump to Category</option>
                        @foreach($categories as $category)
                            @if($category->activeFaqs->count() > 0)
                                <option value="#cat-{{ $category->slug }}">{{ $category->name }}</option>
                            @endif
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Two Column Layout -->
            <div class="row g-5">
                <!-- Left Sidebar: Category Navigation (Sticky) -->
                <div class="col-md-4 col-lg-3 d-none d-md-block">
                    <div class="sticky-top" style="top: 100px; z-index: 10;">
                        <div class="bg-white p-4 border rounded-3xl" style="border-color: var(--ha-border) !important; border-radius: 24px;">
                            <h5 class="fw-bold mb-3 pb-2 border-bottom" style="font-size: 0.95rem; text-transform: uppercase; tracking: 0.05em; color: var(--ha-ink);">Categories</h5>
                            <div class="nav flex-column nav-pills" id="faq-categories-nav">
                                @foreach($categories as $index => $category)
                                    @if($category->activeFaqs->count() > 0)
                                        <a href="#cat-{{ $category->slug }}" class="nav-link mb-2 text-start d-flex justify-content-between align-items-center py-2.5 px-3 rounded-3 @if($index === 0) active @endif" style="font-size: 0.9rem; font-weight: 600; transition: all 0.25s;" data-category="{{ $category->slug }}">
                                            <span>{{ $category->name }}</span>
                                            <span class="badge rounded-pill bg-light text-dark border ms-2" style="font-size: 0.75rem;">{{ $category->activeFaqs->count() }}</span>
                                        </a>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Area: FAQs accordions -->
                <div class="col-md-8 col-lg-9">
                    <!-- Search Results Header (Hidden by default) -->
                    <div id="search-meta" class="mb-4 d-none">
                        <h4 class="fw-bold text-slate-800" style="font-size: 1.25rem;">
                            Search Results for "<span id="search-query-display" class="text-primary"></span>"
                        </h4>
                        <p class="text-muted small mb-0"><span id="search-count-display">0</span> match(es) found.</p>
                    </div>

                    <!-- FAQ categories and items -->
                    <div id="faq-content-area" class="space-y-5">
                        @forelse($categories as $category)
                            @if($category->activeFaqs->count() > 0)
                                <div class="faq-category-section mb-5 scroll-margin-top" id="cat-{{ $category->slug }}" data-category-slug="{{ $category->slug }}">
                                    <!-- Category Header -->
                                    <div class="d-flex align-items-center gap-2 mb-3 border-bottom pb-2">
                                        <i class="bi bi-chevron-right text-primary"></i>
                                        <h3 class="fw-bold m-0" style="font-size: 1.5rem; color: var(--ha-ink);">{{ $category->name }}</h3>
                                    </div>
                                    @if($category->description)
                                        <p class="text-muted small mb-4 mt-1">{{ $category->description }}</p>
                                    @endif

                                    <!-- Accordion Group -->
                                    <div class="accordion accordion-flush bg-white rounded-3xl overflow-hidden border shadow-sm" style="border-color: var(--ha-border) !important; border-radius: 20px;" id="accordion-{{ $category->slug }}">
                                        @foreach($category->activeFaqs as $index => $faq)
                                            <div class="accordion-item faq-item" data-question="{{ strtolower($faq->question) }}" data-answer="{{ strtolower(strip_tags($faq->answer)) }}">
                                                <h2 class="accordion-header" id="heading-{{ $faq->id }}">
                                                    <button class="accordion-button collapsed py-3.5 px-4 fw-semibold text-start" style="font-size: 1rem; color: var(--ha-ink); background: transparent;" type="button" data-bs-toggle="collapse" data-bs-target="#collapse-{{ $faq->id }}" aria-expanded="false" aria-controls="collapse-{{ $faq->id }}">
                                                        {{ $faq->question }}
                                                    </button>
                                                </h2>
                                                <div id="collapse-{{ $faq->id }}" class="accordion-collapse collapse" aria-labelledby="heading-{{ $faq->id }}" data-bs-parent="#accordion-{{ $category->slug }}">
                                                    <div class="accordion-body py-3.5 px-4 text-muted border-top" style="line-height: 1.6; font-size: 0.95rem;">
                                                        {!! $faq->answer !!}
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        @empty
                            <div class="text-center py-5 bg-white rounded-3xl border" style="border-color: var(--ha-border) !important;">
                                <i class="bi bi-question-circle text-4xl text-muted block mb-3 opacity-50"></i>
                                <h4 class="fw-bold">No FAQs Available</h4>
                                <p class="text-muted">Check back later for updates.</p>
                            </div>
                        @endforelse

                        <!-- Empty search state -->
                        <div id="empty-search-state" class="text-center py-5 bg-white rounded-3xl border d-none" style="border-color: var(--ha-border) !important; border-radius: 24px;">
                            <i class="bi bi-search text-3xl text-muted block mb-3 opacity-40"></i>
                            <h4 class="fw-bold">No Match Found</h4>
                            <p class="text-muted mb-0">No questions or answers matched your search terms. Try different keywords.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<x-cta title="Have a question not listed here?" text="Submit an enquiry via our Contact page, and our customer success team will assist you with details." />
@endsection

@push('styles')
<style>
    .scroll-margin-top {
        scroll-margin-top: 100px;
    }
    #faq-categories-nav .nav-link {
        color: var(--ha-muted);
        background: transparent;
        border: 1px solid transparent;
    }
    #faq-categories-nav .nav-link:hover {
        background: var(--ha-soft);
        color: var(--ha-blue);
    }
    #faq-categories-nav .nav-link.active {
        background: var(--ha-blue-light) !important;
        color: var(--ha-blue) !important;
        border-color: rgba(17, 85, 204, 0.15) !important;
    }
    .accordion-button:not(.collapsed) {
        color: var(--ha-blue) !important;
        background-color: var(--ha-soft) !important;
        box-shadow: none !important;
    }
    .accordion-button::after {
        font-family: "bootstrap-icons";
        content: "\f282";
        background-image: none !important;
        transform: none !important;
        font-size: 0.95rem;
        color: var(--ha-muted);
    }
    .accordion-button:not(.collapsed)::after {
        content: "\f278";
        color: var(--ha-blue);
    }
    /* Style links within answers */
    .accordion-body a {
        color: var(--ha-blue);
        text-decoration: underline;
        font-weight: 600;
    }
    .accordion-body a:hover {
        color: var(--ha-blue-2);
    }
    /* Style lists inside answer body */
    .accordion-body ul, .accordion-body ol {
        padding-left: 20px;
        margin-top: 10px;
        margin-bottom: 10px;
    }
    .accordion-body li {
        margin-bottom: 5px;
    }
</style>
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Sticky/Scroll Categories highlight
        const sections = document.querySelectorAll('.faq-category-section');
        const navLinks = document.querySelectorAll('#faq-categories-nav .nav-link');

        window.addEventListener('scroll', function () {
            let current = '';
            sections.forEach(section => {
                const sectionTop = section.offsetTop;
                const sectionHeight = section.clientHeight;
                if (pageYOffset >= (sectionTop - 120)) {
                    current = section.getAttribute('id');
                }
            });

            navLinks.forEach(link => {
                link.classList.remove('active');
                if (link.getAttribute('href') === '#' + current) {
                    link.classList.add('active');
                }
            });
        });

        // Mobile Category Selection Redirect/Scroll
        const mobileSelect = document.getElementById('mobile-category-select');
        if (mobileSelect) {
            mobileSelect.addEventListener('change', function () {
                const targetId = this.value;
                if (targetId) {
                    const targetEl = document.querySelector(targetId);
                    if (targetEl) {
                        window.scrollTo({
                            top: targetEl.offsetTop - 90,
                            behavior: 'smooth'
                        });
                    }
                }
            });
        }

        // Live Search Filter (JavaScript Client-side)
        const searchInput = document.getElementById('faq-search');
        const faqItems = document.querySelectorAll('.faq-item');
        const categorySections = document.querySelectorAll('.faq-category-section');
        const emptyState = document.getElementById('empty-search-state');
        const searchMeta = document.getElementById('search-meta');
        const searchQueryDisplay = document.getElementById('search-query-display');
        const searchCountDisplay = document.getElementById('search-count-display');

        if (searchInput) {
            searchInput.addEventListener('input', function () {
                const query = this.value.trim().toLowerCase();
                let matchCount = 0;

                if (query.length > 0) {
                    // Show search details metadata
                    searchMeta.classList.remove('d-none');
                    searchQueryDisplay.textContent = this.value;

                    // Filter Q&As
                    faqItems.forEach(item => {
                        const q = item.getAttribute('data-question') || '';
                        const a = item.getAttribute('data-answer') || '';

                        if (q.includes(query) || a.includes(query)) {
                            item.classList.remove('d-none');
                            matchCount++;

                            // Highlight the matching item by expanding it if needed
                            // (Optional: can leave closed or open first matching)
                        } else {
                            item.classList.add('d-none');
                            // Collapse if hidden
                            const collapseEl = item.querySelector('.accordion-collapse');
                            if (collapseEl && collapseEl.classList.contains('show')) {
                                const bsCollapse = bootstrap.Collapse.getInstance(collapseEl);
                                if (bsCollapse) bsCollapse.hide();
                            }
                        }
                    });

                    searchCountDisplay.textContent = matchCount;

                    // Filter Category visibility based on matches inside
                    categorySections.forEach(section => {
                        const visibleItems = section.querySelectorAll('.faq-item:not(.d-none)');
                        if (visibleItems.length > 0) {
                            section.classList.remove('d-none');
                        } else {
                            section.classList.add('d-none');
                        }
                    });

                    // Manage empty search state
                    if (matchCount === 0) {
                        emptyState.classList.remove('d-none');
                    } else {
                        emptyState.classList.add('d-none');
                    }
                } else {
                    // Reset search
                    searchMeta.classList.add('d-none');
                    emptyState.classList.add('d-none');

                    faqItems.forEach(item => {
                        item.classList.remove('d-none');
                    });
                    categorySections.forEach(section => {
                        section.classList.remove('d-none');
                    });
                }
            });
        }
    });
</script>
@endpush
