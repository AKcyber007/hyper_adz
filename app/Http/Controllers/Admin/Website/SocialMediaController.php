<?php

namespace App\Http\Controllers\Admin\Website;

use App\Http\Controllers\Controller;
use App\Models\WebsiteSocialLink;
use Illuminate\Http\Request;

class SocialMediaController extends Controller
{
    public function index()
    {
        $links = WebsiteSocialLink::orderBy('platform')->get();
        return view('admin.website.social.index', compact('links'));
    }

    public function create()
    {
        return view('admin.website.social.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'platform' => 'required|string|max:255',
            'url' => 'nullable|url|max:255',
            'status' => 'boolean',
        ]);

        WebsiteSocialLink::create($data);
        \Illuminate\Support\Facades\Cache::forget('website_social_links');

        return redirect()->route('admin.website.social-links.index')->with('success', 'Social Media link added successfully.');
    }

    public function edit($id)
    {
        $link = WebsiteSocialLink::findOrFail($id);
        return view('admin.website.social.edit', compact('link'));
    }

    public function update(Request $request, $id)
    {
        $link = WebsiteSocialLink::findOrFail($id);

        $data = $request->validate([
            'platform' => 'required|string|max:255',
            'url' => 'nullable|url|max:255',
            'status' => 'boolean',
        ]);

        $link->update($data);
        \Illuminate\Support\Facades\Cache::forget('website_social_links');

        return redirect()->route('admin.website.social-links.index')->with('success', 'Social Media link updated successfully.');
    }

    public function destroy($id)
    {
        $link = WebsiteSocialLink::findOrFail($id);
        $link->delete();
        \Illuminate\Support\Facades\Cache::forget('website_social_links');

        return redirect()->route('admin.website.social-links.index')->with('success', 'Social Media link deleted successfully.');
    }
}
