<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\SourceRequest;
use App\Models\Source;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class SourceController extends Controller
{
    public function index(): View
    {
        $sources = Source::orderBy('name')->paginate(20);

        return view('admin.sources.index', compact('sources'));
    }

    public function create(): View
    {
        return view('admin.sources.create');
    }

    public function store(SourceRequest $request): RedirectResponse
    {
        Source::create($this->payload($request));

        return redirect()->route('admin.sources.index')->with('success', __('Source created.'));
    }

    public function edit(Source $source): View
    {
        return view('admin.sources.edit', compact('source'));
    }

    public function update(SourceRequest $request, Source $source): RedirectResponse
    {
        $source->update($this->payload($request));

        return redirect()->route('admin.sources.index')->with('success', __('Source updated.'));
    }

    public function destroy(Source $source): RedirectResponse
    {
        $source->delete();

        return redirect()->route('admin.sources.index')->with('success', __('Source deleted.'));
    }

    public function toggle(Source $source): RedirectResponse
    {
        $source->update(['enabled' => ! $source->enabled]);

        return back()->with('success', $source->enabled
            ? __('Source enabled.')
            : __('Source disabled.'));
    }

    /**
     * @return array<string, mixed>
     */
    private function payload(SourceRequest $request): array
    {
        $data = $request->validated();
        $data['enabled'] = $request->boolean('enabled');

        return $data;
    }
}
