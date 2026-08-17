<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Faq;
use App\Models\FaqCategory;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class FaqController extends Controller
{
    public function index(Request $request): View
    {
        $query = Faq::with('category')->orderBy('faq_category_id')->orderBy('display_order');

        if ($request->filled('category')) {
            $query->where('faq_category_id', $request->category);
        }

        $faqs       = $query->paginate(25)->withQueryString();
        $categories = FaqCategory::ordered()->get();

        return view('admin.faqs.index', compact('faqs', 'categories'));
    }

    public function create(): View
    {
        $categories = FaqCategory::active()->ordered()->get();
        return view('admin.faqs.create', compact('categories'));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'faq_category_id' => 'required|exists:faq_categories,id',
            'question'        => 'required|string|max:500',
            'answer'          => 'required|string',
            'display_order'   => 'nullable|integer|min:0',
            'status'          => 'required|in:active,inactive',
        ]);

        Faq::create([
            'faq_category_id' => $request->faq_category_id,
            'question'        => $request->question,
            'answer'          => $request->answer,
            'display_order'   => $request->display_order ?? 0,
            'status'          => $request->status,
        ]);

        return redirect()->route('admin.faqs.index')
            ->with('success', 'FAQ created successfully.');
    }

    public function edit(int $id): View
    {
        $faq        = Faq::findOrFail($id);
        $categories = FaqCategory::active()->ordered()->get();
        return view('admin.faqs.edit', compact('faq', 'categories'));
    }

    public function update(Request $request, int $id): RedirectResponse
    {
        $faq = Faq::findOrFail($id);

        $request->validate([
            'faq_category_id' => 'required|exists:faq_categories,id',
            'question'        => 'required|string|max:500',
            'answer'          => 'required|string',
            'display_order'   => 'nullable|integer|min:0',
            'status'          => 'required|in:active,inactive',
        ]);

        $faq->update([
            'faq_category_id' => $request->faq_category_id,
            'question'        => $request->question,
            'answer'          => $request->answer,
            'display_order'   => $request->display_order ?? 0,
            'status'          => $request->status,
        ]);

        return redirect()->route('admin.faqs.index')
            ->with('success', 'FAQ updated successfully.');
    }

    public function destroy(int $id): RedirectResponse
    {
        Faq::findOrFail($id)->delete();

        return redirect()->route('admin.faqs.index')
            ->with('success', 'FAQ deleted successfully.');
    }

    public function toggleStatus(int $id): JsonResponse
    {
        $faq = Faq::findOrFail($id);
        $faq->update(['status' => $faq->status === 'active' ? 'inactive' : 'active']);

        return response()->json(['status' => $faq->status]);
    }

    public function reorder(Request $request): JsonResponse
    {
        $request->validate(['order' => 'required|array', 'order.*' => 'integer']);

        foreach ($request->order as $position => $id) {
            Faq::where('id', $id)->update(['display_order' => $position + 1]);
        }

        return response()->json(['success' => true]);
    }
}
