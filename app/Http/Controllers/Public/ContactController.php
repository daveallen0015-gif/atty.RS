<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Inquiry;
use App\Models\LawyerProfile;
use App\Models\PageContent;
use App\Models\Service;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ContactController extends Controller
{
    public function show(Request $request): View
    {
        $serviceTitle = Service::query()
            ->where('is_published', true)
            ->where('slug', $request->query('service'))
            ->value('title');

        return view('public.contact', [
            'lawyer' => User::query()->where('is_lawyer', true)->first(),
            'profile' => LawyerProfile::query()->with('user')->first(),
            'content' => PageContent::query()->pluck('content', 'key'),
            'serviceTitle' => $serviceTitle,
            'subjectOptions' => $this->subjectOptions(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'subject' => ['required', 'string', Rule::in($this->subjectOptions())],
            'other_subject' => ['required_if:subject,Other', 'nullable', 'string', 'max:255'],
            'message' => ['required', 'string', 'max:12000'],
        ]);

        if ($validated['subject'] === 'Other') {
            $validated['subject'] = trim($validated['other_subject']);
        }
        unset($validated['other_subject']);

        Inquiry::query()->create($validated);

        return redirect()->route('contact')->with('status', 'Your message has been received. Thank you for getting in touch.');
    }

    private function subjectOptions(): array
    {
        return array_values(array_unique([
            'Legal Consultation',
            'Family and Marriage Law',
            'Criminal Law',
            'Civil Law',
            'Labor and Employment Law',
            'Business and Commercial Law',
            'Property and Real Estate Law',
            'Taxation Law',
            'Human Rights and Social Legislation',
            'Estate and Inheritance',
            ...Service::query()->where('is_published', true)->orderBy('sort_order')->orderBy('title')->pluck('title')->all(),
            'Other',
        ]));
    }
}
