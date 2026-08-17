<?php

namespace App\Http\Controllers\Admin\Website;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;

class PartnerBrandController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Get all files from the storage directory
        $files = Storage::disk('public')->files('partner-brands');
        
        $brands = array_map(function ($file) {
            return [
                'name' => basename($file),
                'url' => Storage::disk('public')->url($file),
                'path' => $file
            ];
        }, $files);

        return view('admin.website.partner-brands.index', compact('brands'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'image' => 'required|image|mimes:png,jpg,jpeg,svg,webp|max:2048',
        ]);

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $filename = time() . '_' . $file->getClientOriginalName();
            
            // Store the file
            $path = $file->storeAs('partner-brands', $filename, 'public');
            
            if ($path) {
                return redirect()->route('admin.website.partner-brands.index')
                    ->with('success', 'Brand logo uploaded successfully.');
            }
        }

        return back()->with('error', 'Failed to upload brand logo.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($filename)
    {
        $path = 'partner-brands/' . $filename;
        
        if (Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
            return redirect()->route('admin.website.partner-brands.index')
                ->with('success', 'Brand logo deleted successfully.');
        }

        return back()->with('error', 'Brand logo not found.');
    }
}
