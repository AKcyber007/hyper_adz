<?php

namespace App\Http\Controllers;

use App\Models\Blog;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PublicBlogController extends Controller
{
    public function index(Request $request): View
    {
        $query = Blog::published()->latestFirst();

        if ($request->filled('search')) {
            $search = '%' . $request->search . '%';
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', $search)
                  ->orWhere('short_description', 'like', $search)
                  ->orWhere('content', 'like', $search);
            });
        }

        // Fetch featured blog if not searching (featured hero is usually hidden when searching)
        $featured = null;
        if (!$request->filled('search')) {
            $featured = Blog::published()->featured()->first();
        }

        // Exclude the featured blog from the main listing grid if it exists
        if ($featured) {
            $query->where('id', '!=', $featured->id);
        }

        $blogs = $query->paginate(6)->withQueryString();

        return view('blog.index', compact('blogs', 'featured'));
    }

    public function show(string $slug): View
    {
        $blog = Blog::published()->where('slug', $slug)->firstOrFail();

        // Related articles: latest published blogs excluding the current article
        $related = Blog::published()
            ->where('id', '!=', $blog->id)
            ->latestFirst()
            ->limit(3)
            ->get();

        return view('blog.show', compact('blog', 'related'));
    }
}
