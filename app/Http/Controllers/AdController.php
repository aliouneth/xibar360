<?php

namespace App\Http\Controllers;

use App\Models\Ad;
use App\Http\Requests\AdRequest;
use Illuminate\Http\Request;

class AdController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:admin']);
    }

    public function index()
    {
        $this->authorize('viewAny', Ad::class);

        $ads = Ad::latest()->paginate(20);

        return view('admin.ads.index', compact('ads'));
    }

    public function store(AdRequest $request)
    {
        $this->authorize('create', Ad::class);

        Ad::create($request->validated());

        return redirect()->route('admin.ads.index')->with('success', __('Ad created successfully.'));
    }

    public function update(AdRequest $request, Ad $ad)
    {
        $this->authorize('update', $ad);

        $ad->update($request->validated());

        return redirect()->route('admin.ads.index')->with('success', __('Ad updated successfully.'));
    }

    public function destroy(Ad $ad)
    {
        $this->authorize('delete', $ad);

        $ad->delete();

        return redirect()->route('admin.ads.index')->with('success', __('Ad deleted successfully.'));
    }

    public function toggle(Ad $ad)
    {
        $this->authorize('update', $ad);

        $ad->update(['is_active' => !$ad->is_active]);

        return back()->with('success', __('Ad status toggled.'));
    }
}
