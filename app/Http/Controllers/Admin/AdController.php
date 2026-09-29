<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ad;
use Illuminate\Http\Request;
use App\Http\Requests\AdRequest;

class AdController extends Controller
{
    public function index()
    {
        $ads = Ad::paginate(15);
        return view('admin.ads.index', compact('ads'));
    }

    public function create()
    {
        return view('admin.ads.create');
    }

    public function store(AdRequest $request)
    {
        $data = $request->validated();

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('ads', 'public');
        }

        Ad::create($data);
        return back()->with('success', 'Ad created.');
    }

    public function edit(Ad $ad)
    {
        return view('admin.ads.edit', compact('ad'));
    }

    public function update(AdRequest $request, Ad $ad)
    {
        $data = $request->validated();

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('ads', 'public');
        }

        $ad->update($data);
        return back()->with('success', 'Ad updated.');
    }

    public function destroy(Ad $ad)
    {
        $ad->delete();
        return back()->with('success', 'Ad deleted.');
    }

    public function toggle(Ad $ad)
    {
        $ad->update(['is_active' => !$ad->is_active]);
        return back()->with('success', 'Ad status updated.');
    }
}
