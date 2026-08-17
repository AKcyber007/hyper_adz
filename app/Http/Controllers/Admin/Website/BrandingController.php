<?php

namespace App\Http\Controllers\Admin\Website;

use App\Http\Controllers\Controller;
use App\Models\WebsiteBranding;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BrandingController extends Controller
{
    public function index()
    {
        $branding = WebsiteBranding::first();
        return view('admin.website.branding', compact('branding'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'logo' => 'nullable|image|max:5120',
            'footer_logo' => 'nullable|image|max:5120',
            'favicon' => 'nullable|image|max:2048',
            'dark_logo' => 'nullable|image|max:5120',
        ]);

        $branding = WebsiteBranding::first() ?? new WebsiteBranding();

        if ($request->hasFile('logo')) {
            if ($branding->logo_path) Storage::disk('public')->delete($branding->logo_path);
            $branding->logo_path = $request->file('logo')->store('branding', 'public');
        }

        if ($request->hasFile('footer_logo')) {
            if ($branding->footer_logo_path) Storage::disk('public')->delete($branding->footer_logo_path);
            $branding->footer_logo_path = $request->file('footer_logo')->store('branding', 'public');
        }

        if ($request->hasFile('favicon')) {
            if ($branding->favicon_path) Storage::disk('public')->delete($branding->favicon_path);
            $branding->favicon_path = $request->file('favicon')->store('branding', 'public');
        }
        
        if ($request->hasFile('dark_logo')) {
            if ($branding->dark_logo_path) Storage::disk('public')->delete($branding->dark_logo_path);
            $branding->dark_logo_path = $request->file('dark_logo')->store('branding', 'public');
        }

        $branding->save();

        \Illuminate\Support\Facades\Cache::forget('website_branding');

        return redirect()->route('admin.website.branding.index')->with('success', 'Branding updated successfully.');
    }
}
