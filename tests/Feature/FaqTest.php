<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\FaqCategory;
use App\Models\Faq;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class FaqTest extends TestCase
{
    use RefreshDatabase;

    protected User $adminUser;

    protected function setUp(): void
    {
        parent::setUp();

        // Setup role and admin user
        Role::firstOrCreate(['name' => 'Admin', 'guard_name' => 'web']);
        \Spatie\Permission\Models\Permission::firstOrCreate(['name' => 'manage-faqs', 'guard_name' => 'web']);

        $this->adminUser = User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'phone' => '9999999999',
            'status' => 'active',
        ]);
        $this->adminUser->assignRole('Admin');
        $this->adminUser->givePermissionTo('manage-faqs');

        // Run seeders to test seed data presence
        $this->seed(\Database\Seeders\FaqSeeder::class);
    }

    /**
     * Test public FAQ page loads correctly.
     */
    public function test_public_faq_page_loads(): void
    {
        $response = $this->get(route('faqs'));
        $response->assertStatus(200);
        $response->assertSee('Frequently Asked Questions');
        $response->assertSee('What is Hyper Adz?');
        $response->assertSee('How does Hyper Adz work?');
    }

    /**
     * Test footer FAQ link is present.
     */
    public function test_footer_faq_link_present(): void
    {
        $response = $this->get(route('home'));
        $response->assertStatus(200);
        $response->assertSee(route('faqs'));
    }

    /**
     * Test active categories and FAQs display while inactive ones are hidden.
     */
    public function test_faq_visibility_rules(): void
    {
        // 1. Inactive FAQ
        $category = FaqCategory::where('slug', 'general')->first();
        $inactiveFaq = Faq::create([
            'faq_category_id' => $category->id,
            'question' => 'Hidden Question Test',
            'answer' => 'Hidden Answer Test',
            'status' => 'inactive',
            'display_order' => 10,
        ]);

        $response = $this->get(route('faqs'));
        $response->assertStatus(200);
        $response->assertDontSee('Hidden Question Test');

        // 2. Inactive Category
        $inactiveCategory = FaqCategory::create([
            'name' => 'Hidden Category',
            'slug' => 'hidden-category',
            'description' => 'Inactive category',
            'status' => 'inactive',
            'display_order' => 20,
        ]);
        $activeFaqInInactiveCategory = Faq::create([
            'faq_category_id' => $inactiveCategory->id,
            'question' => 'Active Question in Inactive Category',
            'answer' => 'Answer text',
            'status' => 'active',
            'display_order' => 1,
        ]);

        $response = $this->get(route('faqs'));
        $response->assertStatus(200);
        $response->assertDontSee('Hidden Category');
        $response->assertDontSee('Active Question in Inactive Category');
    }

    /**
     * Test Admin Category CRUD operations.
     */
    public function test_admin_category_crud(): void
    {
        // Store
        $response = $this->actingAs($this->adminUser)->post(route('admin.faq-categories.store'), [
            'name' => 'New Test Category',
            'description' => 'Test description',
            'display_order' => 5,
        ]);
        $response->assertRedirect(route('admin.faq-categories.index'));
        $this->assertDatabaseHas('faq_categories', [
            'name' => 'New Test Category',
            'slug' => 'new-test-category',
            'display_order' => 5,
        ]);

        $category = FaqCategory::where('slug', 'new-test-category')->first();

        // Update
        $response = $this->actingAs($this->adminUser)->put(route('admin.faq-categories.update', $category->id), [
            'name' => 'Updated Test Category',
            'description' => 'Updated description',
            'display_order' => 6,
            'status' => 'inactive',
        ]);
        $response->assertRedirect(route('admin.faq-categories.index'));
        $this->assertDatabaseHas('faq_categories', [
            'id' => $category->id,
            'name' => 'Updated Test Category',
            'status' => 'inactive',
        ]);

        // Destroy
        $response = $this->actingAs($this->adminUser)->delete(route('admin.faq-categories.update', $category->id));
        $response->assertRedirect(route('admin.faq-categories.index'));
        $this->assertDatabaseMissing('faq_categories', ['id' => $category->id]);
    }

    /**
     * Test Admin FAQ CRUD operations.
     */
    public function test_admin_faq_crud(): void
    {
        $category = FaqCategory::where('slug', 'general')->first();

        // Store
        $response = $this->actingAs($this->adminUser)->post(route('admin.faqs.store'), [
            'faq_category_id' => $category->id,
            'question' => 'What is the speed of light?',
            'answer' => '<p>299,792 km/s</p>',
            'display_order' => 1,
            'status' => 'active',
        ]);
        $response->assertRedirect(route('admin.faqs.index'));
        $this->assertDatabaseHas('faqs', [
            'question' => 'What is the speed of light?',
            'answer' => '<p>299,792 km/s</p>',
        ]);

        $faq = Faq::where('question', 'What is the speed of light?')->first();

        // Update
        $response = $this->actingAs($this->adminUser)->put(route('admin.faqs.update', $faq->id), [
            'faq_category_id' => $category->id,
            'question' => 'What is the speed of sound?',
            'answer' => '<p>343 m/s</p>',
            'display_order' => 2,
            'status' => 'inactive',
        ]);
        $response->assertRedirect(route('admin.faqs.index'));
        $this->assertDatabaseHas('faqs', [
            'id' => $faq->id,
            'question' => 'What is the speed of sound?',
            'status' => 'inactive',
        ]);

        // Toggle Status
        $response = $this->actingAs($this->adminUser)->patch(route('admin.faqs.toggle-status', $faq->id));
        $response->assertStatus(200);
        $this->assertEquals('active', $faq->fresh()->status);

        // Destroy
        $response = $this->actingAs($this->adminUser)->delete(route('admin.faqs.destroy', $faq->id));
        $response->assertRedirect(route('admin.faqs.index'));
        $this->assertDatabaseMissing('faqs', ['id' => $faq->id]);
    }

    /**
     * Test reordering.
     */
    public function test_reordering(): void
    {
        $categories = FaqCategory::ordered()->get();
        $order = $categories->pluck('id')->reverse()->toArray();

        $response = $this->actingAs($this->adminUser)->post(route('admin.faq-categories.reorder'), [
            'order' => $order
        ]);
        $response->assertStatus(200);

        // Verify display order has updated
        foreach ($order as $position => $id) {
            $this->assertEquals($position + 1, FaqCategory::find($id)->display_order);
        }
    }
}
