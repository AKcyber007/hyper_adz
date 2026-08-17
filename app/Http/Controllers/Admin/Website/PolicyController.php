<?php

namespace App\Http\Controllers\Admin\Website;

use App\Http\Controllers\Controller;
use App\Models\WebsitePolicy;
use Illuminate\Http\Request;

class PolicyController extends Controller
{
    public function index()
    {
        // Ensure standard policies exist
        $standardTypes = [
            'privacy' => 'Privacy Policy',
            'terms' => 'Terms & Conditions',
            'refund' => 'Refund Policy',
            'cookie' => 'Cookie Policy'
        ];

        foreach ($standardTypes as $type => $title) {
            WebsitePolicy::firstOrCreate(
                ['type' => $type],
                ['title' => $title, 'status' => 'draft']
            );
        }

        $policies = WebsitePolicy::all();
        return view('admin.website.policies.index', compact('policies'));
    }

    public function edit($id)
    {
        $policy = WebsitePolicy::findOrFail($id);
        return view('admin.website.policies.edit', compact('policy'));
    }

    public function update(Request $request, $id)
    {
        $policy = WebsitePolicy::findOrFail($id);

        $data = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'nullable|string',
            'status' => 'required|in:draft,published',
        ]);

        $policy->update($data);

        return redirect()->route('admin.website.policies.index')->with('success', 'Policy updated successfully.');
    }
}
