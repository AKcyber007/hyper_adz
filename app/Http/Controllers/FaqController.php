<?php

namespace App\Http\Controllers;

use App\Models\FaqCategory;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FaqController extends Controller
{
    public function index(Request $request): View
    {
        $categories = FaqCategory::active()
            ->ordered()
            ->with(['activeFaqs' => fn($q) => $q->ordered()])
            ->get();

        return view('faqs', compact('categories'));
    }
}
