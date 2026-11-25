<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\LawyerProfile;
use App\Models\PageContent;
use App\Models\Service;
use App\Models\User;
use Illuminate\Http\Response;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function index(): View
    {
        return view('public.home', $this->pageData());
    }

    public function about(): View
    {
        return view('public.about', $this->pageData());
    }

    public function services(): View
    {
        return view('public.services', $this->pageData());
    }

    public function service(Service $service): View
    {
        abort_unless($service->is_published, Response::HTTP_NOT_FOUND);

        return view('public.service', [...$this->pageData(), 'service' => $service]);
    }

    private function pageData(): array
    {
        return [
            'lawyer' => User::query()->where('is_lawyer', true)->first(),
            'profile' => LawyerProfile::query()->with('user')->first(),
            'services' => Service::query()->where('is_published', true)->orderBy('sort_order')->orderBy('title')->get(),
            'content' => PageContent::query()->pluck('content', 'key'),
        ];
    }
}
