<?php

namespace App\Http\Controllers;

use App\Models\WebsitePolicy;
use Illuminate\Http\Request;

class PublicPolicyController extends Controller
{
    public function show($type)
    {
        $policy = WebsitePolicy::where('type', $type)->where('status', 'published')->first();

        if ($policy) {
            return view('policies.show', compact('policy'));
        }

        if (view()->exists("policies.{$type}")) {
            return view("policies.{$type}");
        }

        abort(404);

    }
}
