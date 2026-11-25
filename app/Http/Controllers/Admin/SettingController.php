<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PageContent;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Services\GitHubImageService;
use Illuminate\View\View;

class SettingController extends Controller
{
    private const GROUPS = [
        'home' => [
            'label' => 'Home hero',
            'description' => 'Edit the opening section and hero background.',
            'fields' => [
                'hero_eyebrow' => 'Opening label',
                'hero_title' => 'Opening headline',
                'hero_accent_title' => 'Highlighted headline',
                'hero_body' => 'Opening introduction',
                'hero_button_label' => 'Opening link label',
                'hero_contact_label' => 'Secondary opening link label',
            ],
            'background' => true,
        ],
        'navigation' => [
            'label' => 'Navigation',
            'description' => 'Edit the public navigation branding, link labels, and inquiry button.',
            'fields' => [
                'nav_brand_prefix' => 'Name prefix',
                'nav_subtitle' => 'Brand subtitle (blank uses the profile title)',
                'nav_home_label' => 'Home link',
                'nav_about_label' => 'About link',
                'nav_services_label' => 'Services link',
                'nav_laws_label' => 'Philippine Laws link',
                'nav_contact_label' => 'Contact link',
                'nav_portal_label' => 'Lawyer Portal link',
                'nav_dashboard_label' => 'Dashboard link (when signed in)',
                'nav_inquiry_label' => 'Inquiry button',
            ],
            'navigation_logo' => true,
        ],
        'highlights' => [
            'label' => 'Highlights',
            'description' => 'Edit the four value statements shown below the home hero.',
            'fields' => [
                'feature_1' => 'Highlight one',
                'feature_2' => 'Highlight two',
                'feature_3' => 'Highlight three',
                'feature_4' => 'Highlight four',
            ],
        ],
        'about' => [
            'label' => 'About',
            'description' => 'Edit the About preview displayed on the home page.',
            'fields' => [
                'about_heading' => 'About section heading',
                'about_body' => 'About section text',
                'about_link_label' => 'About section link label',
            ],
        ],
        'services' => [
            'label' => 'Services',
            'description' => 'Edit the Services preview and the introduction on the Services page.',
            'fields' => [
                'services_heading' => 'Services section heading',
                'services_intro' => 'Services section introduction',
                'services_link_label' => 'Services section link label',
                'services_page_label' => 'Services page label',
                'services_page_heading' => 'Services page heading',
                'services_page_intro' => 'Services page introduction',
                'services_cta_heading' => 'Services inquiry heading',
                'services_cta_body' => 'Services inquiry introduction',
                'services_cta_button' => 'Services inquiry button label',
            ],
        ],
        'contact' => [
            'label' => 'Contact',
            'description' => 'Edit contact page content, website search details, and social media links.',
            'fields' => [
                'contact_heading' => 'Contact section heading',
                'contact_intro' => 'Contact section introduction',
                'contact_link_label' => 'Contact section link label',
                'site_description' => 'Website description',
                'social_facebook' => 'Facebook URL',
                'social_instagram' => 'Instagram URL',
                'social_linkedin' => 'LinkedIn URL',
            ],
        ],
        'footer' => [
            'label' => 'Footer',
            'description' => 'Edit the small note displayed at the bottom of the public website.',
            'fields' => [
                'footer_note' => 'Footer note',
            ],
        ],
    ];

    public function edit(): View
    {
        $contents = PageContent::query()->whereIn('key', [...array_keys($this->fields()), 'hero_background', 'navigation_logo'])->pluck('content', 'key');

        return view('admin.settings.edit', [
            'groups' => self::GROUPS,
            'contents' => $contents,
        ]);
    }

    public function update(Request $request, GitHubImageService $images): RedirectResponse
    {
        $rules = [];
        foreach (array_keys($this->fields()) as $key) {
            $rules["content.{$key}"] = ['nullable', 'string', 'max:12000'];
        }
        foreach (array_keys(self::GROUPS['navigation']['fields']) as $key) {
            $rules["content.{$key}"] = ['nullable', 'string', 'max:255'];
        }
        $rules['content.site_description'] = ['nullable', 'string', 'max:500'];
        foreach (['social_facebook', 'social_instagram', 'social_linkedin'] as $key) {
            $rules["content.{$key}"] = ['nullable', 'url:https', 'max:2048'];
        }
        $rules['hero_background'] = ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:8192'];
        $rules['navigation_logo'] = ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:8192'];
        $validated = $request->validate($rules);

        foreach ($this->fields() as $key => $label) {
            PageContent::query()->updateOrCreate(
                ['key' => $key],
                [
                    'title' => $label,
                    'content' => $validated['content'][$key] ?? '',
                    'updated_by' => $request->user()->id,
                ],
            );
        }

        if ($request->hasFile('hero_background')) {
            $background = PageContent::query()->firstOrNew(['key' => 'hero_background']);
            $previousPath = $background->content;
            $imageUrl = $images->upload($request->file('hero_background'), 'site-backgrounds');
            $background->fill([
                'title' => 'Home hero background',
                'content' => $imageUrl,
                'updated_by' => $request->user()->id,
            ])->save();

            if ($previousPath) {
                $images->delete($previousPath);
            }
        }

        if ($request->hasFile('navigation_logo')) {
            $logo = PageContent::query()->firstOrNew(['key' => 'navigation_logo']);
            $previousPath = $logo->content;
            $imageUrl = $images->upload($request->file('navigation_logo'), 'site-branding');
            $logo->fill([
                'title' => 'Navigation bar logo',
                'content' => $imageUrl,
                'updated_by' => $request->user()->id,
            ])->save();

            if ($previousPath) {
                $images->delete($previousPath);
            }
        }

        return redirect()->route('admin.settings.edit')->with('status', 'Landing page content updated.');
    }

    private function fields(): array
    {
        $fields = [];
        foreach (self::GROUPS as $group) {
            $fields = [...$fields, ...$group['fields']];
        }

        return $fields;
    }
}
