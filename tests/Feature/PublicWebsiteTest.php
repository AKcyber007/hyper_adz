<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Phase 1 — Public Website Testing
 * Tests all public pages load (200 OK) and CTA redirection flows work correctly.
 */
class PublicWebsiteTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    // ── 1.1 Navigation — All pages must return 200 ─────────────────────────

    public function test_home_page_loads(): void
    {
        $this->get('/')->assertStatus(200);
    }

    public function test_about_page_loads(): void
    {
        $this->get('/about')->assertStatus(200);
    }

    public function test_services_page_loads(): void
    {
        $this->get('/services')->assertStatus(200);
    }

    public function test_why_hyper_adz_page_loads(): void
    {
        $this->get('/why-hyper-adz')->assertStatus(200);
    }

    public function test_network_page_loads(): void
    {
        $this->get('/network')->assertStatus(200);
    }

    public function test_contact_page_loads(): void
    {
        $this->get('/contact')->assertStatus(200);
    }

    public function test_privacy_policy_page_loads(): void
    {
        $this->get('/privacy-policy')->assertStatus(200);
    }

    public function test_terms_conditions_page_loads(): void
    {
        $this->get('/terms-conditions')->assertStatus(200);
    }

    public function test_refund_policy_page_loads(): void
    {
        $this->get('/refund-policy')->assertStatus(200);
    }

    // ── 1.2 Contact page CTA cards must always be visible ──────────────────

    public function test_contact_page_shows_advertise_with_us_cta(): void
    {
        $this->get('/contact')->assertStatus(200)->assertSee('Advertise With Us');
    }

    public function test_contact_page_shows_become_partner_cta(): void
    {
        $this->get('/contact')->assertStatus(200)->assertSee('Become a Location Partner');
    }

    public function test_contact_advertiser_form_param_shows_enquiry_form(): void
    {
        $this->get('/contact?form=advertiser')->assertStatus(200)->assertSee('contact-us-form');
    }

    public function test_contact_partner_form_param_shows_enquiry_form(): void
    {
        $this->get('/contact?form=partner')->assertStatus(200)->assertSee('contact-us-form');
    }

    // ── 1.3 Redirection Flows ───────────────────────────────────────────────

    public function test_old_become_a_partner_url_redirects_to_contact(): void
    {
        $this->get('/become-a-partner')->assertRedirect('/contact');
    }

    public function test_unauthenticated_admin_redirects_to_login(): void
    {
        $this->get('/admin/dashboard')->assertRedirect('/login');
    }

    public function test_unauthenticated_advertiser_dashboard_redirects(): void
    {
        $this->get('/advertiser/dashboard')->assertRedirect('/advertiser/login');
    }

    public function test_unauthenticated_partner_dashboard_redirects(): void
    {
        $this->get('/partner/dashboard')->assertRedirect('/partner/login');
    }

    public function test_unauthenticated_advertiser_create_campaign_redirects(): void
    {
        $this->get('/advertiser/requests/create')->assertRedirect();
    }

    public function test_unauthenticated_partner_locations_redirects(): void
    {
        $this->get('/partner/locations')->assertRedirect();
    }
}
