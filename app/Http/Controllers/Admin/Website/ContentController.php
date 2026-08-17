<?php

namespace App\Http\Controllers\Admin\Website;

use App\Http\Controllers\Controller;
use App\Models\WebsiteSetting;
use Illuminate\Http\Request;

class ContentController extends Controller
{
    public function index()
    {
        $settings = WebsiteSetting::first();
        return view('admin.website.content', compact('settings'));
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'company_name' => 'nullable|string|max:255',
            'company_description' => 'nullable|string',
            'address' => 'nullable|string',
            'primary_email' => 'nullable|email|max:255',
            'secondary_email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:50',
            'whatsapp' => 'nullable|string|max:50',
            'gst_number' => 'nullable|string|max:50',
            'business_hours' => 'nullable|string|max:255',
        ]);

        $settings = WebsiteSetting::first();
        if ($settings) {
            $settings->update($data);
        } else {
            WebsiteSetting::create($data);
        }

        \Illuminate\Support\Facades\Cache::forget('website_settings');

        return redirect()->route('admin.website.content.index')->with('success', 'Company Information updated successfully.');
    }
}
