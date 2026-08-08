document.addEventListener('DOMContentLoaded', () => {
    // 1. Initialize AOS (Scroll Animations)
    if (window.AOS) {
        AOS.init({ 
            duration: 800, 
            once: true, 
            offset: 50,
            easing: 'ease-out-quad'
        });
    }

    // 2. GSAP Load Animations
    if (window.gsap) {
        // Hero Content Animations
        if (document.querySelector('.hero-section')) {
            gsap.from('.hero-section h1, .hero-section .eyebrow, .hero-section .hero-lead, .hero-section .hero-actions, .hero-section .hero-trust-indicators', {
                y: 30,
                opacity: 0,
                duration: 0.8,
                stagger: 0.1,
                ease: 'power3.out'
            });
        }
        
        // Hero Showcase Image Animation
        if (document.querySelector('.hero-showcase-wrapper')) {
            gsap.from('.hero-showcase-wrapper', {
                scale: 0.95,
                opacity: 0,
                duration: 1,
                ease: 'power3.out',
                delay: 0.2
            });
        }
    }

    // 3. Navbar scroll shrink effect
    const navbar = document.querySelector('.site-nav');
    if (navbar) {
        const handleScroll = () => {
            if (window.scrollY > 40) {
                navbar.classList.add('scrolled');
            } else {
                navbar.classList.remove('scrolled');
            }
        };
        window.addEventListener('scroll', handleScroll);
        handleScroll(); // Check immediately on load
    }

    // 4. Statistics count-up animation
    const statsElements = document.querySelectorAll('.stats-num');
    if (statsElements.length > 0) {
        const animateStats = (el) => {
            const target = parseInt(el.getAttribute('data-val'), 10);
            const prefix = el.getAttribute('data-prefix') || '';
            const suffix = el.getAttribute('data-suffix') || '';
            const duration = 2000; // ms
            const startTime = performance.now();

            const updateCount = (currentTime) => {
                const elapsed = currentTime - startTime;
                const progress = Math.min(elapsed / duration, 1);
                
                // Ease out quad
                const easeProgress = progress * (2 - progress);
                
                const currentValue = Math.floor(easeProgress * target);
                
                el.textContent = prefix + currentValue + suffix;

                if (progress < 1) {
                    requestAnimationFrame(updateCount);
                } else {
                    el.textContent = prefix + target + suffix;
                }
            };
            requestAnimationFrame(updateCount);
        };

        // Trigger animation when scrolled into view
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    animateStats(entry.target);
                    observer.unobserve(entry.target);
                }
            });
        }, { threshold: 0.2 });

        statsElements.forEach(el => observer.observe(el));
    }

    // 5. Leaflet Map setup (focused, clean, no markers as requested, ready for future data-driven addition)
    const mapNode = document.getElementById('networkMap');
    if (mapNode && window.L) {
        // Center on Coimbatore
        const map = L.map('networkMap', { 
            scrollWheelZoom: false,
            zoomControl: false,
            dragging: false,
            doubleClickZoom: false,
            boxZoom: false
        }).setView([11.0168, 76.9558], 12);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 18,
            attribution: '&copy; OpenStreetMap contributors'
        }).addTo(map);
    }

    // 6. Stats Background Slider
    const statsSlider = document.querySelector('.stats-bg-slider');
    if (statsSlider) {
        const slides = statsSlider.querySelectorAll('.stats-bg-image');
        let currentSlide = 0;
        const totalSlides = slides.length;
        const intervalTime = 4000; // 4 seconds

        setInterval(() => {
            slides[currentSlide].classList.remove('active');
            currentSlide = (currentSlide + 1) % totalSlides;
            slides[currentSlide].classList.add('active');
        }, intervalTime);
    }
});
