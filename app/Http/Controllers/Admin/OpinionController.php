<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Opinion;
use Illuminate\Http\Request;

class OpinionController extends Controller
{
    public function index()
    {
        $opinions = Opinion::latest()->paginate(20);

        return view('admin.opinions.index', compact('opinions'));
    }

    public function destroy(Opinion $opinion)
    {
        $opinion->delete();

        return redirect()->route('admin.opinions.index')->with('success', __('Opinion deleted.'));
    }
}
