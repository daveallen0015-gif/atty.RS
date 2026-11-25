<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Law;
use App\Models\LawCategory;
use App\Models\LawyerProfile;
use App\Models\PageContent;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LawController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('q', ''));
        $categorySlug = (string) $request->query('category', '');

        $laws = Law::query()->with('category')->where('is_published', true)
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($query) use ($search) {
                    $query->where('official_title', 'like', "%{$search}%")
                        ->orWhere('law_number', 'like', "%{$search}%")
                        ->orWhere('law_type', 'like', "%{$search}%")
                        ->orWhere('short_description', 'like', "%{$search}%")
                        ->orWhere('full_text', 'like', "%{$search}%")
                        ->orWhereHas('category', fn ($category) => $category->where('category_name', 'like', "%{$search}%"));
                });
            })
            ->when($categorySlug !== '', fn ($query) => $query->whereHas('category', fn ($category) => $category->where('slug', $categorySlug)))
            ->orderBy('official_title')
            ->paginate(9)
            ->withQueryString();

        $viewData = [
            ...$this->sharedData(),
            'laws' => $laws,
            'categories' => LawCategory::query()->orderBy('category_name')->get(),
            'search' => $search,
            'selectedCategory' => $categorySlug,
        ];

        if ($request->ajax()) {
            return view('public.laws._results', ['laws' => $laws]);
        }

        return view('public.laws.index', $viewData);
    }

    public function show(Law $law): View
    {
        abort_unless($law->is_published, 404);

        return view('public.laws.show', [
            ...$this->sharedData(),
            'law' => $law->load('category'),
            'relatedLaws' => Law::query()->where('is_published', true)
                ->where('law_category_id', $law->law_category_id)->whereKeyNot($law->id)
                ->orderBy('official_title')->limit(4)->get(),
        ]);
    }

    private function sharedData(): array
    {
        return [
            'lawyer' => User::query()->where('is_lawyer', true)->first(),
            'profile' => LawyerProfile::query()->with('user')->first(),
            'content' => PageContent::query()->pluck('content', 'key'),
        ];
    }
}
