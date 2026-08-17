<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Industry;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class IndustryController extends Controller
{
    /**
     * Display listing of industries.
     */
    public function index(): View
    {
        $industries = Industry::orderBy('name', 'asc')->paginate(20);
        return view('admin.industries.index', compact('industries'));
    }

    /**
     * Store new industry.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string|max:100|unique:industries,name',
        ]);

        Industry::create([
            'name'   => $request->name,
            'status' => 'active',
        ]);

        return redirect()->route('admin.industries.index')->with('success', 'Industry lookup successfully created.');
    }

    /**
     * Update industry details.
     */
    public function update(Request $request, int $id): RedirectResponse
    {
        $industry = Industry::findOrFail($id);

        $request->validate([
            'name'   => 'required|string|max:100|unique:industries,name,' . $id,
            'status' => 'required|in:active,inactive',
        ]);

        $industry->update($request->only(['name', 'status']));

        return redirect()->route('admin.industries.index')->with('success', 'Industry lookup successfully updated.');
    }

    /**
     * Delete industry lookup.
     */
    public function destroy(int $id): RedirectResponse
    {
        $industry = Industry::findOrFail($id);
        
        // Check if industry is bound to any advertisers
        if ($industry->advertisers()->exists()) {
            return redirect()->route('admin.industries.index')->with('error', 'Cannot delete industry. It is associated with active advertiser profiles.');
        }

        $industry->delete();

        return redirect()->route('admin.industries.index')->with('success', 'Industry lookup successfully deleted.');
    }
}
