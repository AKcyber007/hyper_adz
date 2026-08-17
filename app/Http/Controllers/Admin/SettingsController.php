<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SettingsController extends Controller
{
    /**
     * Display the Admin Settings page.
     */
    public function index(): View
    {
        return view('admin.settings.index');
    }
}
