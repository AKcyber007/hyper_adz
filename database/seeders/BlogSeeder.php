<?php

namespace Database\Seeders;

use App\Models\Blog;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class BlogSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Assign manage-blogs permission to Admin role
        $permission = Permission::firstOrCreate(['name' => 'manage-blogs', 'guard_name' => 'web']);
        $adminRole = Role::where('name', 'Admin')->where('guard_name', 'web')->first();
        if ($adminRole && !$adminRole->hasPermissionTo('manage-blogs')) {
            $adminRole->givePermissionTo($permission);
        }

        // 2. Seed 3 default blog posts
        $blogs = [
            [
                'title' => 'The Rise of Social Indoor Advertising: Connecting Offline to Online',
                'slug' => 'rise-of-social-indoor-advertising',
                'featured_image' => 'images/slide-mall.png',
                'short_description' => 'Explore how indoor digital screens in high-traffic retail venues can bridge the gap between offline interactions and digital online conversions.',
                'content' => '<h2>Understanding Social Indoor Advertising</h2><p>In the digital age, businesses struggle to capture user attention amidst online clutter. Indoor digital advertising represents a unique opportunity to engage high-intent local audiences in physical spaces like malls, cafes, fitness centers, and medical lobbies where they naturally spend dwell time.</p><h3>Why Dwell Time Matters</h3><p>Unlike traditional outdoor billboards that drivers pass by in seconds, indoor digital signage benefits from extended dwell times. When gym-goers exercise or cafe patrons wait for their order, their attention shifts toward modern high-definition displays. This allows advertisers to present longer-form messages, storytelling creatives, or QR codes that drive immediate online interaction.</p><h3>Connecting Offline with Online Campaigns</h3><p>By using smart QR codes, interactive maps, or localized landing pages, indoor advertising serves as a gateway to digital channels. Advertisers can capture customer interest at the point of engagement, leading directly to higher online checkouts, app downloads, and social media follows.</p>',
                'author_name' => 'Senthil Kumar',
                'status' => 'published',
                'publish_date' => now()->subDays(2),
                'is_featured' => true,
                'seo_title' => 'Rise of Social Indoor Digital Signage & Advertising | Hyper Adz',
                'seo_description' => 'Discover how indoor digital screen networks bridge offline visibility and digital conversions using smart QR codes and high dwell-time placements.',
            ],
            [
                'title' => '5 Essential Tips for Creating High-Impact Digital Billboard Creatives',
                'slug' => '5-tips-for-high-impact-digital-billboard-creatives',
                'featured_image' => 'images/slide-cafe.png',
                'short_description' => 'Designing for digital out-of-home screens requires a unique approach compared to print or web. Learn the key design principles for success.',
                'content' => '<h2>Designing for DOOH Displays</h2><p>Digital Out-of-Home (DOOH) screens are dynamic, bright, and highly noticeable. However, a poorly designed creative can get ignored. Follow these design tips to ensure your campaign stands out:</p><ol><li><strong>Keep it Concise:</strong> Passersby only have a few seconds to absorb your message. Stick to a single clear headline and simple Call to Action.</li><li><strong>High Color Contrast:</strong> Use dark backgrounds with bright text, or vice versa, to ensure legibility from a distance. Avoid muddy tones.</li><li><strong>Use Clean Typography:</strong> Sans-serif fonts like Sora, Inter, or Arial work best. Avoid overly decorative fonts that are hard to read quickly.</li><li><strong>Include Visual Hierarchy:</strong> Make your brand logo and main value proposition prominent.</li><li><strong>Optimize Resolution:</strong> Ensure your video or image assets match the display aspect ratios (e.g. 1080p full HD) so they appear crystal clear.</li></ol>',
                'author_name' => 'Adithya Vignesh',
                'status' => 'published',
                'publish_date' => now()->subDay(),
                'is_featured' => false,
                'seo_title' => 'High-Impact Digital Billboard Creative Design Tips | Hyper Adz',
                'seo_description' => 'Master DOOH design with our top 5 tips on typography, color contrast, and central hierarchy to make digital advertising campaigns stand out.',
            ],
            [
                'title' => 'How Local Businesses Can Leverage DOOH Advertising in Coimbatore',
                'slug' => 'local-dooh-advertising-coimbatore',
                'featured_image' => 'images/slide-outdoor.png',
                'short_description' => 'Local businesses in Coimbatore can now advertise like big brands. Discover why geo-targeted digital out-of-home networks are changing the game.',
                'content' => '<h2>Coimbatore\'s Digital Shift</h2><p>Coimbatore has emerged as a major hub for retail, technology, and entrepreneurship. With business competition increasing, local brands need cost-effective ways to establish authority. Digital Out-of-Home (DOOH) networks offer local businesses the same premium advertising options as global corporations.</p><h3>Cost-Effective Geo-Targeting</h3><p>Traditional billboard rentals can cost lakhs per month and target general traffic. DOOH networks allow local gyms, showrooms, or clinics to display their ads in specific retail neighborhoods, cafes, and community hotspots for a fraction of the cost. This hyper-local targeting prevents marketing budget waste.</p><h3>Flexible Scheduling</h3><p>Unlike traditional static signs, digital displays let you adjust campaigns instantly. Want to run breakfast deals in local cafes? Scheduled DOOH screens can display your morning campaign and automatically switch to business services in the afternoon.</p>',
                'author_name' => 'Rajesh Sharma',
                'status' => 'published',
                'publish_date' => now(),
                'is_featured' => false,
                'seo_title' => 'Local DOOH Screen Advertising in Coimbatore | Hyper Adz',
                'seo_description' => 'Learn how local showrooms, restaurants, and SMBs can leverage geo-targeted indoor digital signage networks across Coimbatore to boost sales.',
            ],
        ];

        foreach ($blogs as $blogData) {
            Blog::updateOrCreate(
                ['slug' => $blogData['slug']],
                $blogData
            );
        }
    }
}
