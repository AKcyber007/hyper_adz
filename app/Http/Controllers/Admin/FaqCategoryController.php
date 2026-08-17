<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FaqCategory;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class FaqCategoryController extends Controller
{
    public function index(): View
    {
        $categories = FaqCategory::withCount('faqs')->ordered()->get();
        return view('admin.faqs.categories.index', compact('categories'));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name'          => 'required|string|max:100|unique:faq_categories,name',
            'description'   => 'nullable|string|max:500',
            'display_order' => 'nullable|integer|min:0',
        ]);

        FaqCategory::create([
            'name'          => $request->name,
            'description'   => $request->description,
            'display_order' => $request->display_order ?? 0,
            'status'        => 'active',
        ]);

        return redirect()->route('admin.faq-categories.index')
            ->with('success', 'FAQ category created successfully.');
    }

    public function update(Request $request, int $id): RedirectResponse
    {
        $category = FaqCategory::findOrFail($id);

        $request->validate([
            'name'          => 'required|string|max:100|unique:faq_categories,name,' . $id,
            'description'   => 'nullable|string|max:500',
            'display_order' => 'nullable|integer|min:0',
            'status'        => 'required|in:active,inactive',
        ]);

        $category->update([
            'name'          => $request->name,
            'description'   => $request->description,
            'display_order' => $request->display_order ?? 0,
            'status'        => $request->status,
        ]);

        return redirect()->route('admin.faq-categories.index')
            ->with('success', 'FAQ category updated successfully.');
    }

    public function destroy(int $id): RedirectResponse
    {
        $category = FaqCategory::withCount('faqs')->findOrFail($id);

        if ($category->faqs_count > 0) {
            return redirect()->route('admin.faq-categories.index')
                ->with('error', 'Cannot delete category — it contains ' . $category->faqs_count . ' FAQ(s). Remove the FAQs first.');
        }

        $category->delete();

        return redirect()->route('admin.faq-categories.index')
            ->with('success', 'FAQ category deleted successfully.');
    }

    public function reorder(Request $request): JsonResponse
    {
        $request->validate(['order' => 'required|array', 'order.*' => 'integer']);

        foreach ($request->order as $position => $id) {
            FaqCategory::where('id', $id)->update(['display_order' => $position + 1]);
        }

        return response()->json(['success' => true]);
    }
}
