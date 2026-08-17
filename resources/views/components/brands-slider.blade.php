<div class="brands-slider-section py-5 bg-white">
    <div class="container text-center">
        <h2 class="mb-5" style="font-family: 'Sora', sans-serif; font-weight: 700; text-transform: uppercase;">
            <span style="color: #ef4444;">LEADING BRANDS</span> <span style="color: #0f172a;">PARTNERED WITH US</span>
        </h2>
        
        <div class="swiper brandsSwiper px-4">
            <div class="swiper-wrapper align-items-center">
                @php
                    $files = \Illuminate\Support\Facades\Storage::disk('public')->files('partner-brands');
                    $brands = array_map(function ($file) {
                        return \Illuminate\Support\Facades\Storage::disk('public')->url($file);
                    }, $files);
                @endphp
                
                @forelse ($brands as $index => $brandUrl)
                <div class="swiper-slide">
                    <div class="brand-card">
                        <img src="{{ $brandUrl }}" alt="Partner Brand {{ $index + 1 }}" class="img-fluid">
                    </div>
                </div>
                @empty
                <!-- Fallback if empty -->
                @endforelse
            </div>
            <div class="swiper-pagination mt-4 position-static"></div>
            <div class="swiper-button-prev" style="color: #000; left: 0;"></div>
            <div class="swiper-button-next" style="color: #000; right: 0;"></div>
        </div>
    </div>
</div>

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
<style>
    .brands-slider-section {
        overflow: hidden;
    }
    .brand-card {
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 1.5rem;
        height: 120px;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        background: #fff;
    }
    .brand-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1);
        border-color: #cbd5e1;
    }
    .brand-card img {
        max-height: 80px;
        max-width: 100%;
        object-fit: contain;
        filter: grayscale(100%) opacity(0.8);
        transition: filter 0.3s ease;
    }
    .brand-card:hover img {
        filter: grayscale(0%) opacity(1);
    }
    
    .brandsSwiper {
        padding-bottom: 40px; /* Space for pagination */
    }
    
    /* Swiper custom styling */
    .swiper-button-next:after, .swiper-button-prev:after {
        font-size: 20px;
        font-weight: bold;
    }
    .swiper-button-next, .swiper-button-prev {
        width: 40px;
        height: 40px;
        background: #fff;
        border-radius: 50%;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        top: 40%;
    }
    .swiper-pagination-bullet {
        background: #cbd5e1;
        opacity: 1;
    }
    .swiper-pagination-bullet-active {
        background: #0f172a;
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        var swiper = new Swiper(".brandsSwiper", {
            slidesPerView: 2,
            spaceBetween: 20,
            loop: true,
            autoplay: {
                delay: 2500,
                disableOnInteraction: false,
            },
            pagination: {
                el: ".swiper-pagination",
                clickable: true,
            },
            navigation: {
                nextEl: ".swiper-button-next",
                prevEl: ".swiper-button-prev",
            },
            breakpoints: {
                640: {
                    slidesPerView: 3,
                    spaceBetween: 20,
                },
                768: {
                    slidesPerView: 4,
                    spaceBetween: 30,
                },
                1024: {
                    slidesPerView: 5,
                    spaceBetween: 30,
                },
            },
        });
    });
</script>
@endpush
