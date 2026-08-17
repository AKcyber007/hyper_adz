<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Blog;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class BlogController extends Controller
{
    public function index(Request $request): View
    {
        $query = Blog::latestFirst();

        if ($request->filled('status') && in_array($request->status, ['published', 'draft', 'archived'])) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = '%' . $request->search . '%';
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', $search)
                  ->orWhere('author_name', 'like', $search);
            });
        }

        $blogs = $query->paginate(15)->withQueryString();

        return view('admin.blogs.index', compact('blogs'));
    }

    public function create(): View
    {
        return view('admin.blogs.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'title'             => 'required|string|max:255',
            'slug'              => 'nullable|string|max:255|unique:blogs,slug',
            'featured_image'    => 'nullable|image|max:10240', // max 2MB
            'short_description' => 'required|string|max:500',
            'content'           => 'required|string',
            'author_name'       => 'required|string|max:100',
            'status'            => 'required|in:draft,published,archived',
            'publish_date'      => 'nullable|date',
            'is_featured'       => 'nullable|boolean',
            'seo_title'         => 'nullable|string|max:255',
            'seo_description'   => 'nullable|string|max:500',
        ]);

        $featuredImagePath = null;
        if ($request->hasFile('featured_image')) {
            $featuredImagePath = $request->file('featured_image')->store('blogs', 'public');
        }

        Blog::create([
            'title'             => $request->title,
            'slug'              => $request->filled('slug') ? Str::slug($request->slug) : Str::slug($request->title),
            'featured_image'    => $featuredImagePath,
            'short_description' => $request->short_description,
            'content'           => $request->content,
            'author_name'       => $request->author_name,
            'status'            => $request->status,
            'publish_date'      => $request->publish_date,
            'is_featured'       => $request->boolean('is_featured'),
            'seo_title'         => $request->seo_title,
            'seo_description'   => $request->seo_description,
        ]);

        return redirect()->route('admin.blogs.index')
            ->with('success', 'Blog article successfully created.');
    }

    public function edit(int $id): View
    {
        $blog = Blog::findOrFail($id);
        return view('admin.blogs.edit', compact('blog'));
    }

    public function update(Request $request, int $id): RedirectResponse
    {
        $blog = Blog::findOrFail($id);

        $request->validate([
            'title'             => 'required|string|max:255',
            'slug'              => 'nullable|string|max:255|unique:blogs,slug,' . $id,
            'featured_image'    => 'nullable|image|max:10240',
            'short_description' => 'required|string|max:500',
            'content'           => 'required|string',
            'author_name'       => 'required|string|max:100',
            'status'            => 'required|in:draft,published,archived',
            'publish_date'      => 'nullable|date',
            'is_featured'       => 'nullable|boolean',
            'seo_title'         => 'nullable|string|max:255',
            'seo_description'   => 'nullable|string|max:500',
        ]);

        $featuredImagePath = $blog->featured_image;
        if ($request->hasFile('featured_image')) {
            // Delete old featured image if exists
            if ($blog->featured_image && Storage::disk('public')->exists($blog->featured_image)) {
                Storage::disk('public')->delete($blog->featured_image);
            }
            $featuredImagePath = $request->file('featured_image')->store('blogs', 'public');
        }

        $blog->update([
            'title'             => $request->title,
            'slug'              => $request->filled('slug') ? Str::slug($request->slug) : Str::slug($request->title),
            'featured_image'    => $featuredImagePath,
            'short_description' => $request->short_description,
            'content'           => $request->content,
            'author_name'       => $request->author_name,
            'status'            => $request->status,
            'publish_date'      => $request->publish_date,
            'is_featured'       => $request->boolean('is_featured'),
            'seo_title'         => $request->seo_title,
            'seo_description'   => $request->seo_description,
        ]);

        return redirect()->route('admin.blogs.index')
            ->with('success', 'Blog article successfully updated.');
    }

    public function destroy(int $id): RedirectResponse
    {
        $blog = Blog::findOrFail($id);

        if ($blog->featured_image && Storage::disk('public')->exists($blog->featured_image)) {
            Storage::disk('public')->delete($blog->featured_image);
        }

        $blog->delete();

        return redirect()->route('admin.blogs.index')
            ->with('success', 'Blog article successfully deleted.');
    }

    public function toggleStatus(int $id): JsonResponse
    {
        $blog = Blog::findOrFail($id);
        $blog->update(['status' => $blog->status === 'published' ? 'draft' : 'published']);

        return response()->json(['status' => $blog->status]);
    }

    public function toggleFeature(int $id): JsonResponse
    {
        $blog = Blog::findOrFail($id);
        $blog->update(['is_featured' => !$blog->is_featured]);

        return response()->json(['is_featured' => $blog->is_featured]);
    }

    public function uploadImage(Request $request): JsonResponse
    {
        $request->validate([
            'image' => 'required|image|max:10240'
        ]);

        $path = $request->file('image')->store('blogs/content', 'public');
        $url = asset('storage/' . $path);

        return response()->json(['url' => $url]);
    }
}
