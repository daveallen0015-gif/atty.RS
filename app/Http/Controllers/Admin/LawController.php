<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Law;
use App\Models\LawCategory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class LawController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('q', ''));
        $laws = Law::query()->with('category')
            ->when($search !== '', fn ($query) => $query->where(function ($query) use ($search) {
                $query->where('official_title', 'like', "%{$search}%")
                    ->orWhere('law_number', 'like', "%{$search}%")
                    ->orWhere('law_type', 'like', "%{$search}%");
            }))
            ->when($request->filled('category'), fn ($query) => $query->where('law_category_id', $request->integer('category')))
            ->when($request->query('status') === 'published', fn ($query) => $query->where('is_published', true))
            ->when($request->query('status') === 'draft', fn ($query) => $query->where('is_published', false))
            ->orderBy('official_title')->paginate(15)->withQueryString();

        return view('admin.laws.index', [
            'laws' => $laws,
            'categories' => LawCategory::query()->orderBy('category_name')->get(),
        ]);
    }

    public function create(): View
    {
        return view('admin.laws.create', [
            'law' => null,
            'categories' => LawCategory::query()->orderBy('category_name')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validatedData($request);
        $validated['slug'] = $this->uniqueSlug($validated['official_title']);
        $validated['is_published'] = $request->boolean('is_published');
        $validated['document_path'] = $request->file('document')?->store('law-documents', 'public');
        unset($validated['document']);
        Law::query()->create($validated);

        return redirect()->route('admin.laws.index')->with('status', 'Law reference created.');
    }

    public function edit(Law $law): View
    {
        return view('admin.laws.edit', [
            'law' => $law,
            'categories' => LawCategory::query()->orderBy('category_name')->get(),
        ]);
    }

    public function update(Request $request, Law $law): RedirectResponse
    {
        $validated = $this->validatedData($request);
        $validated['slug'] = $this->uniqueSlug($validated['official_title'], $law);
        $validated['is_published'] = $request->boolean('is_published');
        if ($request->hasFile('document')) {
            if ($law->document_path) {
                Storage::disk('public')->delete($law->document_path);
            }
            $validated['document_path'] = $request->file('document')->store('law-documents', 'public');
        }
        unset($validated['document']);
        $law->update($validated);

        return redirect()->route('admin.laws.index')->with('status', 'Law reference updated.');
    }

    public function destroy(Law $law): RedirectResponse
    {
        if ($law->document_path) {
            Storage::disk('public')->delete($law->document_path);
        }
        $law->delete();

        return redirect()->route('admin.laws.index')->with('status', 'Law reference deleted.');
    }

    private function validatedData(Request $request): array
    {
        return $request->validate([
            'law_category_id' => ['required', 'integer', 'exists:law_categories,id'],
            'law_type' => ['required', 'string', 'max:80'],
            'law_number' => ['required', 'string', 'max:100'],
            'official_title' => ['required', 'string', 'max:255'],
            'short_description' => ['nullable', 'string', 'max:2000'],
            'full_text' => ['nullable', 'string'],
            'date_approved' => ['nullable', 'date'],
            'official_source_url' => ['nullable', 'url:https', 'max:2048'],
            'legal_status' => ['nullable', 'string', 'max:120'],
            'document' => ['nullable', 'file', 'mimes:pdf', 'max:15360'],
        ]);
    }

    private function uniqueSlug(string $title, ?Law $ignore = null): string
    {
        $base = Str::slug($title) ?: 'law-reference';
        $slug = $base;
        $suffix = 2;

        while (Law::query()->where('slug', $slug)->when($ignore, fn ($query) => $query->where('id', '!=', $ignore->id))->exists()) {
            $slug = $base.'-'.$suffix++;
        }

        return $slug;
    }
}
