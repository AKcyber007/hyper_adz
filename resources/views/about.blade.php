@extends('layouts.app', [
    'title' => 'About Hyper Adz | Hyperlocal Ad Network Coimbatore | Multitude Solutions',
    'description' => 'Learn about Hyper Adz — an indoor digital advertising network by Multitude Solutions connecting brands with high-intent local audiences through premium partner venues in Coimbatore.'
])

@section('content')
<!-- Wejha-style Banner/Hero Section -->
<section class="solution-section">
    <div class="content-wrapper">
        <div class="image-left" data-aos="fade-right">
            <img src="{{ asset('images/about_hero_left.png') }}" alt="Sleek digital screen mockup left">
        </div>
        <div class="text-center" data-aos="fade-up">
            <p class="breadcrumb-sub">Smart Visibility Starts Here</p>
            <h1 class="title">Driven by Innovation<br>Focused on You</h1>
            <p class="description">At Hyper Adz, we combine smart screens with real impact to help you attract more customers, build lasting loyalty, and boost your sales.</p>
            <div class="logo-circle">HA</div>
        </div>
        <div class="image-right" data-aos="fade-left">
            <img src="{{ asset('images/about_hero_right.png') }}" alt="Sleek digital screen mockup right">
        </div>
    </div>
</section>

<!-- Vision Section -->
<section class="vision-section">
    <div class="container">
        <div class="vision-content">
            <div class="vision-text" data-aos="fade-right">
                <p class="subtitle">About Hyper Adz</p>
                <h2 class="title">Your Destination for Excellence</h2>
                <p class="description">At Hyper Adz, we transform every digital sign into a smart display, providing valuable slots that help businesses expand their brand visibility. As a leader in digital signage innovation in Coimbatore, we offer top-tier software, advanced hardware, and exceptional customer service. Our platform enables businesses to deliver impactful messages across various locations, including gyms, cafes, malls, salons, and healthcare facilities.</p>
            </div>
            <div class="vision-image" data-aos="fade-left">
                <img src="{{ asset('images/about_us_showcase.png') }}" alt="Hyper Adz display showcase">
            </div>
        </div>
    </div>
</section>

<!-- Core Mission Section -->
<section class="core-mission">
    <div class="container">
        <div class="mission-header" data-aos="fade-up">
            <p class="subtitle">Core Mission</p>
            <h2 class="title">Building Authentic Connections</h2>
            <p class="description">We collaborate with businesses to deliver cutting-edge digital signage solutions. Together, we create impactful experiences and grow faster.</p>
        </div>
        <div class="mission-content">
            <div class="mission-cards" data-aos="fade-right">
                <!-- Card 1 -->
                <div class="card">
                    <div class="badge">1</div>
                    <div class="icon">
                        <i class="bi bi-info-circle"></i>
                    </div>
                    <h3>About Hyper Adz</h3>
                    <p>We transform everyday partner locations — restaurants, cafes, gyms, salons, clinics, and retail stores — into high-value media spaces that work for both advertisers and venue owners.</p>
                </div>
                <!-- Card 2 -->
                <div class="card">
                    <div class="badge">2</div>
                    <div class="icon">
                        <i class="bi bi-bullseye"></i>
                    </div>
                    <h3>Our Mission</h3>
                    <p>Help businesses grow by delivering location-based indoor advertising solutions while creating value for advertisers, venue partners, and consumers through measurable reach.</p>
                </div>
                <!-- Card 3 -->
                <div class="card">
                    <div class="badge">3</div>
                    <div class="icon">
                        <i class="bi bi-eye"></i>
                    </div>
                    <h3>Our Vision</h3>
                    <p>Build the most trusted and impactful Hyper Local advertising ecosystem in Tamil Nadu, connecting brands with high-intent audiences through intelligent digital visibility.</p>
                </div>
            </div>
            <div class="image-wrapper" data-aos="fade-left">
                <img src="{{ asset('images/about_mission_showcase.png') }}" alt="Core mission digital standee mockup">
            </div>
        </div>
    </div>
</section>

<!-- Brands Slider Section -->
<x-brands-slider />

<!-- Hyper Adz CMS Section -->
<section class="cms-section">
    <div class="container">
        <div class="content-wrapper">
            <div class="text-content" data-aos="fade-right">
                <p class="subtitle">Centralized Management</p>
                <h2>Hyper Adz CMS</h2>
                <p class="description">Hyper Adz CMS is a centralized cloud platform that simplifies content distribution and scheduling across digital screens. With smart features like multi-role access and real-time reports, it helps businesses manage content easily and enhance the customer experience.</p>
                
                <div class="checklist">
                    <div class="checklist-item">
                        <span class="check-icon"><i class="bi bi-check-lg"></i></span>
                        <span class="item-text"><strong>All-in-One Solution</strong>: Manage content and devices from one simple platform</span>
                    </div>
                    <div class="checklist-item">
                        <span class="check-icon"><i class="bi bi-check-lg"></i></span>
                        <span class="item-text"><strong>Highly Scalable</strong>: Built to grow with your business, from small to enterprise</span>
                    </div>
                    <div class="checklist-item">
                        <span class="check-icon"><i class="bi bi-check-lg"></i></span>
                        <span class="item-text"><strong>Robust Security</strong>: Advanced protocols keep your data and content protected</span>
                    </div>
                    <div class="checklist-item">
                        <span class="check-icon"><i class="bi bi-check-lg"></i></span>
                        <span class="item-text"><strong>Unparalleled Support</strong>: Get expert help and constant updates for a smooth experience</span>
                    </div>
                </div>
            </div>
            <div class="cms-image" data-aos="fade-left">
                <img src="{{ asset('images/about_cms_showcase.png') }}" alt="Hyper Adz CMS dashboard view">
            </div>
        </div>
    </div>
</section>

<x-cta title="Build visibility in places people already visit." text="Talk to Hyper Adz about your market, audience, and campaign timing." />
@endsection

@push('styles')
<style>
/* Wejha-style Redesign */
.solution-section {
    background: linear-gradient(135deg, #f8faff 0%, #edf2f9 100%);
    padding: 100px 20px;
    min-height: 70vh;
    display: flex;
    align-items: center;
    justify-content: center;
    position: relative;
    overflow: hidden;
}
.solution-section .content-wrapper {
    display: flex;
    align-items: center;
    justify-content: space-between;
    width: 100%;
    max-width: 1400px;
    margin: 0 auto;
    gap: 40px;
}
@media (max-width: 991px) {
    .solution-section {
        padding: 60px 10px;
    }
    .solution-section .content-wrapper {
        flex-direction: column;
        text-align: center;
    }
    .solution-section .image-left, .solution-section .image-right {
        display: none;
    }
}
.solution-section .image-left, .solution-section .image-right {
    flex: 1;
    display: flex;
    justify-content: center;
}
.solution-section .image-left img, .solution-section .image-right img {
    max-width: 240px;
    border-radius: 20px;
    box-shadow: 0 15px 35px rgba(0, 102, 204, 0.08);
    border: 1px solid rgba(0, 102, 204, 0.06);
    transition: transform 0.3s ease;
}
.solution-section .image-left img:hover, .solution-section .image-right img:hover {
    transform: scale(1.03);
}
.solution-section .text-center {
    flex: 2;
    padding: 0 20px;
}
.solution-section .breadcrumb-sub {
    font-size: 0.95rem;
    color: #0066cc;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    margin-bottom: 15px;
}
.solution-section .title {
    font-size: clamp(2rem, 4vw, 3rem);
    font-weight: 800;
    color: #1a1a1a;
    line-height: 1.25;
    margin-bottom: 20px;
}
.solution-section .description {
    font-size: 1.1rem;
    color: #555555;
    max-width: 600px;
    margin: 0 auto 30px;
    line-height: 1.6;
}
.solution-section .logo-circle {
    width: 50px;
    height: 50px;
    background-color: #0066cc;
    color: #ffffff;
    font-weight: 700;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
    margin: 0 auto;
    box-shadow: 0 8px 20px rgba(0, 102, 204, 0.25);
}

/* Vision Section */
.vision-section {
    padding: 100px 0;
    background-color: #ffffff;
}
.vision-section .vision-content {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 60px;
}
@media (max-width: 991px) {
    .vision-section {
        padding: 60px 0;
    }
    .vision-section .vision-content {
        flex-direction: column-reverse;
        text-align: center;
    }
}
.vision-section .vision-text {
    flex: 1.2;
}
.vision-section .vision-text .subtitle {
    color: #0066cc;
    font-size: 0.95rem;
    font-weight: 700;
    text-transform: uppercase;
    margin-bottom: 10px;
}
.vision-section .vision-text .title {
    font-size: 2.25rem;
    font-weight: 800;
    color: #1a1a1a;
    margin-bottom: 20px;
    line-height: 1.2;
}
.vision-section .vision-text .description {
    font-size: 1.05rem;
    line-height: 1.7;
    color: #555555;
}
.vision-section .vision-image {
    flex: 0.8;
    display: flex;
    justify-content: center;
}
.vision-section .vision-image img {
    max-width: 100%;
    height: auto;
    border-radius: 20px;
    box-shadow: 0 15px 40px rgba(0, 0, 0, 0.06);
}

/* Core Mission Section */
.core-mission {
    padding: 100px 0;
    background-color: #f8faff;
}
.core-mission .mission-header {
    text-align: center;
    margin-bottom: 60px;
}
.core-mission .mission-header .subtitle {
    color: #0066cc;
    font-weight: 700;
    text-transform: uppercase;
    font-size: 0.95rem;
    margin-bottom: 10px;
}
.core-mission .mission-header .title {
    font-size: 2.5rem;
    font-weight: 800;
    color: #1a1a1a;
}
.core-mission .mission-header .description {
    margin-top: 15px;
    font-size: 1.1rem;
    color: #666666;
    max-width: 700px;
    margin-left: auto;
    margin-right: auto;
}
.core-mission .mission-content {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 60px;
}
@media (max-width: 991px) {
    .core-mission {
        padding: 60px 0;
    }
    .core-mission .mission-content {
        flex-direction: column;
    }
}
.core-mission .mission-cards {
    flex: 1.2;
    display: flex;
    flex-direction: column;
    gap: 20px;
    width: 100%;
}
.core-mission .mission-cards .card {
    background: #ffffff;
    padding: 1.75rem 1.5rem;
    border-radius: 1.25rem;
    position: relative;
    box-shadow: 0 10px 30px rgba(0, 102, 204, 0.02);
    border: 1px solid rgba(0, 102, 204, 0.06);
    transition: all 0.3s ease;
    display: flex;
    flex-direction: column;
}
.core-mission .mission-cards .card:hover {
    transform: translateY(-3px);
    box-shadow: 0 15px 35px rgba(0, 102, 204, 0.05);
    border-color: rgba(0, 102, 204, 0.18);
}
.core-mission .mission-cards .card .icon {
    font-size: 1.5rem;
    margin-bottom: 1rem;
    color: #0066cc;
    background: #f0f6ff;
    width: 44px;
    height: 44px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
}
.core-mission .mission-cards .card h3 {
    font-size: 1.15rem;
    font-weight: 700;
    margin-bottom: 0.5rem;
    color: #1a1a1a;
}
.core-mission .mission-cards .card p {
    font-size: 0.9rem;
    color: #555555;
    margin-bottom: 0;
    line-height: 1.6;
}
.core-mission .mission-cards .card .badge {
    position: absolute;
    top: 1.5rem;
    right: 1.5rem;
    background: #0066cc;
    color: #ffffff;
    width: 24px;
    height: 24px;
    border-radius: 50%;
    display: flex;
    justify-content: center;
    align-items: center;
    font-weight: 700;
    font-size: 0.75rem;
}
.core-mission .image-wrapper {
    flex: 0.8;
    display: flex;
    justify-content: center;
}
.core-mission .image-wrapper img {
    width: 100%;
    max-width: 440px;
    border-radius: 20px;
    border: 2px solid #0066cc;
    box-shadow: 0 10px 30px rgba(0, 102, 204, 0.15);
}

/* CMS Section */
.cms-section {
    padding: 100px 0;
    background-color: #ffffff;
}
.cms-section .content-wrapper {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 60px;
}
@media (max-width: 991px) {
    .cms-section {
        padding: 60px 0;
    }
    .cms-section .content-wrapper {
        flex-direction: column;
        text-align: center;
    }
    .cms-section .checklist {
        text-align: left;
        display: inline-block;
    }
}
.cms-section .text-content {
    flex: 1.2;
}
.cms-section .text-content .subtitle {
    color: #0066cc;
    font-size: 0.95rem;
    font-weight: 700;
    text-transform: uppercase;
    margin-bottom: 10px;
}
.cms-section .text-content h2 {
    font-size: 2.5rem;
    font-weight: 800;
    color: #1a1a1a;
    margin-bottom: 20px;
}
.cms-section .text-content .description {
    font-size: 1.05rem;
    color: #555555;
    line-height: 1.7;
    margin-bottom: 30px;
}
.cms-section .checklist {
    display: flex;
    flex-direction: column;
    gap: 15px;
}
.cms-section .checklist-item {
    display: flex;
    align-items: flex-start;
    gap: 12px;
}
.cms-section .checklist-item .check-icon {
    color: #0066cc;
    background: #f0f6ff;
    width: 24px;
    height: 24px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 700;
    font-size: 0.85rem;
    flex-shrink: 0;
}
.cms-section .checklist-item .item-text {
    font-size: 1rem;
    color: #444444;
    text-align: left;
}
.cms-section .cms-image {
    flex: 0.8;
    display: flex;
    justify-content: center;
}
.cms-section .cms-image img {
    max-width: 100%;
    height: auto;
    border-radius: 20px;
    box-shadow: 0 15px 40px rgba(0, 0, 0, 0.04);
}
</style>
@endpush
