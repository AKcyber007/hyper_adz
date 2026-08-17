<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Blog;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class BlogTest extends TestCase
{
    use RefreshDatabase;

    protected User $adminUser;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('public');

        // Setup role and admin user
        Role::firstOrCreate(['name' => 'Admin', 'guard_name' => 'web']);
        \Spatie\Permission\Models\Permission::firstOrCreate(['name' => 'manage-blogs', 'guard_name' => 'web']);

        $this->adminUser = User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'phone' => '9999999999',
            'status' => 'active',
        ]);
        $this->adminUser->assignRole('Admin');
        $this->adminUser->givePermissionTo('manage-blogs');

        // Seed default blogs
        $this->seed(\Database\Seeders\BlogSeeder::class);
    }

    /**
     * Test public blog listing loads.
     */
    public function test_public_blog_page_loads(): void
    {
        $response = $this->get(route('blog.index'));
        $response->assertStatus(200);
        $response->assertSee('OUR POV — THE BLOG');
        $response->assertSee('The Rise of Social Indoor Advertising');
    }

    /**
     * Test public search filters.
     */
    public function test_public_blog_search(): void
    {
        $response = $this->get(route('blog.index', ['search' => 'Coimbatore']));
        $response->assertStatus(200);
        $response->assertSee('Coimbatore');
        $response->assertDontSee('5 Essential Tips');
    }

    /**
     * Test detail page loading and reading time display.
     */
    public function test_public_blog_detail_and_reading_time(): void
    {
        $blog = Blog::where('slug', 'rise-of-social-indoor-advertising')->first();
        
        $response = $this->get(route('blog.show', $blog->slug));
        $response->assertStatus(200);
        $response->assertSee('The Rise of Social Indoor Advertising');
        $response->assertSee($blog->reading_time); // e.g. 1 min read
    }

    /**
     * Test draft and future scheduled posts are hidden from public view.
     */
    public function test_blog_visibility_and_scheduling(): void
    {
        // 1. Draft post
        Blog::create([
            'title' => 'Draft Blog Post Test',
            'slug' => 'draft-blog-post-test',
            'short_description' => 'Short desc',
            'content' => '<p>Content</p>',
            'author_name' => 'Author',
            'status' => 'draft',
            'publish_date' => now()->subDay(),
        ]);

        // 2. Future scheduled post
        Blog::create([
            'title' => 'Scheduled Future Blog Post Test',
            'slug' => 'scheduled-future-blog-post-test',
            'short_description' => 'Short desc',
            'content' => '<p>Content</p>',
            'author_name' => 'Author',
            'status' => 'published',
            'publish_date' => now()->addWeek(),
        ]);

        $response = $this->get(route('blog.index'));
        $response->assertStatus(200);
        $response->assertDontSee('Draft Blog Post Test');
        $response->assertDontSee('Scheduled Future Blog Post Test');
    }

    /**
     * Test single featured blog restriction works correctly.
     */
    public function test_only_one_blog_featured_at_a_time(): void
    {
        $blog1 = Blog::create([
            'title' => 'Blog One',
            'slug' => 'blog-one',
            'short_description' => 'Short desc',
            'content' => '<p>Content</p>',
            'author_name' => 'Author',
            'status' => 'published',
            'is_featured' => true,
        ]);

        $this->assertTrue($blog1->fresh()->is_featured);

        $blog2 = Blog::create([
            'title' => 'Blog Two',
            'slug' => 'blog-two',
            'short_description' => 'Short desc',
            'content' => '<p>Content</p>',
            'author_name' => 'Author',
            'status' => 'published',
            'is_featured' => true, // Creating as featured
        ]);

        // Verify blog1 is automatically unfeatured
        $this->assertFalse($blog1->fresh()->is_featured);
        $this->assertTrue($blog2->fresh()->is_featured);
    }

    /**
     * Test Admin blog CRUD actions.
     */
    public function test_admin_blog_crud_actions(): void
    {
        $file = UploadedFile::fake()->create('featured.png', 100, 'image/png');

        // Store
        $response = $this->actingAs($this->adminUser)->post(route('admin.blogs.store'), [
            'title' => 'Brand New Strategy Article',
            'short_description' => 'Brief marketing synopsis of strategy',
            'content' => '<p>Strategy details inside</p>',
            'author_name' => 'Test Author',
            'status' => 'published',
            'featured_image' => $file,
            'is_featured' => 1,
            'seo_title' => 'SEO Brand New Strategy Article',
            'seo_description' => 'SEO details description meta text',
        ]);

        $response->assertRedirect(route('admin.blogs.index'));
        $this->assertDatabaseHas('blogs', [
            'title' => 'Brand New Strategy Article',
            'status' => 'published',
            'is_featured' => true,
            'seo_title' => 'SEO Brand New Strategy Article',
        ]);

        $blog = Blog::where('title', 'Brand New Strategy Article')->first();
        $this->assertNotNull($blog->featured_image);
        Storage::disk('public')->assertExists($blog->featured_image);

        // Edit/Update
        $response = $this->actingAs($this->adminUser)->put(route('admin.blogs.update', $blog->id), [
            'title' => 'Updated Brand New Strategy Article',
            'short_description' => 'Brief marketing synopsis of strategy updated',
            'content' => '<p>Strategy details inside updated</p>',
            'author_name' => 'Test Author Updated',
            'status' => 'draft',
            'is_featured' => 0,
        ]);

        $response->assertRedirect(route('admin.blogs.index'));
        $this->assertDatabaseHas('blogs', [
            'id' => $blog->id,
            'title' => 'Updated Brand New Strategy Article',
            'status' => 'draft',
            'is_featured' => false,
        ]);

        // Toggle Status
        $response = $this->actingAs($this->adminUser)->patch(route('admin.blogs.toggle-status', $blog->id));
        $response->assertStatus(200);
        $this->assertEquals('published', $blog->fresh()->status);

        // Delete
        $response = $this->actingAs($this->adminUser)->delete(route('admin.blogs.destroy', $blog->id));
        $response->assertRedirect(route('admin.blogs.index'));
        $this->assertDatabaseMissing('blogs', ['id' => $blog->id]);
    }

    /**
     * Test inline image upload returns correct URL path.
     */
    public function test_admin_inline_image_upload_endpoint(): void
    {
        $file = UploadedFile::fake()->create('content_img.jpg', 100, 'image/jpeg');

        $response = $this->actingAs($this->adminUser)->post(route('admin.blogs.upload-image'), [
            'image' => $file
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure(['url']);
    }

    /**
     * Test homepage Latest Insights displays.
     */
    public function test_homepage_latest_insights_section(): void
    {
        $response = $this->get(route('home'));
        $response->assertStatus(200);
        $response->assertSee('Latest Insights');
        $response->assertSee('The Rise of Social Indoor Advertising');
    }
}
