<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Service;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ServiceController extends Controller
{
    public function index(): View
    {
        return view('admin.services.index', [
            'services' => Service::query()->orderBy('sort_order')->orderBy('title')->get(),
            'icons' => Service::ICONS,
        ]);
    }

    public function create(): View
    {
        return view('admin.services.create', ['icons' => Service::ICONS]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validatedData($request);
        $validated['slug'] = $this->uniqueSlug($validated['title']);
        $validated['icon'] = $validated['icon'] ?? 'scale';
        $validated['is_published'] = $request->boolean('is_published');
        $validated['image_path'] = $request->file('image')?->store('service-images', 'public');
        unset($validated['image']);

        Service::query()->create($validated);

        return redirect()->route('admin.services.index')->with('status', 'Service created.');
    }

    public function edit(Service $service): View
    {
        return view('admin.services.edit', ['service' => $service, 'icons' => Service::ICONS]);
    }

    public function update(Request $request, Service $service): RedirectResponse
    {
        $validated = $this->validatedData($request);
        $validated['slug'] = $this->uniqueSlug($validated['title'], $service);
        $validated['icon'] = $validated['icon'] ?? 'scale';
        $validated['is_published'] = $request->boolean('is_published');
        if ($request->hasFile('image')) {
            if ($service->image_path) {
                Storage::disk('public')->delete($service->image_path);
            }
            $validated['image_path'] = $request->file('image')->store('service-images', 'public');
        }
        unset($validated['image']);

        $service->update($validated);

        return redirect()->route('admin.services.index')->with('status', 'Service updated.');
    }

    public function destroy(Service $service): RedirectResponse
    {
        if ($service->image_path) {
            Storage::disk('public')->delete($service->image_path);
        }
        $service->delete();

        return redirect()->route('admin.services.index')->with('status', 'Service deleted.');
    }

    private function validatedData(Request $request): array
    {
        return $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'summary' => ['nullable', 'string', 'max:500'],
            'description' => ['nullable', 'string'],
            'icon' => ['nullable', 'in:'.implode(',', array_keys(Service::ICONS))],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
            'sort_order' => ['required', 'integer', 'min:0', 'max:100000'],
        ]);
    }

    private function uniqueSlug(string $title, ?Service $ignore = null): string
    {
        $base = Str::slug($title) ?: 'service';
        $slug = $base;
        $suffix = 2;

        while (Service::query()->where('slug', $slug)->when($ignore, fn ($query) => $query->where('id', '!=', $ignore->getKey()))->exists()) {
            $slug = $base.'-'.$suffix++;
        }

        return $slug;
    }
}
