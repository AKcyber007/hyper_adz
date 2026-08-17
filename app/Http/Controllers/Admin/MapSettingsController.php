<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MapSettingsController extends Controller
{
    /**
     * Display the Admin Map Settings page.
     */
    public function index(): View
    {
        return view('admin.map-settings');
    }
}
