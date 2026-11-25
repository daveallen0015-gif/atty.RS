<?php

namespace Database\Seeders;

use App\Models\PageContent;
use Illuminate\Database\Seeder;

class LandingPageContentSeeder extends Seeder
{
    public function run(): void
    {
        $defaults = [
            'hero_eyebrow' => ['Opening label', 'Your Trusted Legal Partner'],
            'hero_title' => ['Opening headline', 'Justice Served with Integrity'],
            'hero_accent_title' => ['Highlighted headline', 'Commitment to Justice.'],
            'hero_body' => ['Opening introduction', 'We strive to ensure justice with integrity and professionalism in every case we handle.'],
            'hero_button_label' => ['Opening link label', 'Get Started'],
            'hero_contact_label' => ['Secondary opening link label', 'Contact the Lawyer'],
            'feature_1' => ['Highlight one', 'Professional Legal Advice'],
            'feature_2' => ['Highlight two', 'Confidential Consultation'],
            'feature_3' => ['Highlight three', 'Client-Centered Approach'],
            'feature_4' => ['Highlight four', 'Integrity and Dedication'],
            'about_heading' => ['About section heading', 'A personal approach to legal counsel'],
            'about_body' => ['About section text', 'Clear guidance, careful preparation, and personal attention at every step.'],
            'about_link_label' => ['About section link label', 'Read About the Lawyer'],
            'services_heading' => ['Services section heading', 'Legal Services'],
            'services_intro' => ['Services section introduction', 'Explore legal services and available assistance.'],
            'services_link_label' => ['Services section link label', 'Explore Services'],
            'services_page_label' => ['Services page label', 'Legal Services'],
            'services_page_heading' => ['Services page heading', 'Legal Services'],
            'services_page_intro' => ['Services page introduction', 'Access professional legal assistance for various legal concerns. Explore the available services and find appropriate guidance based on your specific needs and circumstances.'],
            'services_cta_heading' => ['Services inquiry heading', 'Need Legal Assistance?'],
            'services_cta_body' => ['Services inquiry introduction', 'Every legal concern deserves proper attention. Send an inquiry to share your concerns and receive information about the available legal assistance.'],
            'services_cta_button' => ['Services inquiry button label', 'Send an Inquiry'],
            'contact_heading' => ['Contact section heading', 'Let’s Begin a Conversation'],
            'contact_intro' => ['Contact section introduction', 'Share a few details about your legal concern to get in touch.'],
            'contact_link_label' => ['Contact section link label', 'Contact the Office'],
            'footer_note' => ['Footer note', 'Dedicated legal counsel, with care.'],
        ];

        foreach ($defaults as $key => [$title, $content]) {
            PageContent::query()->firstOrCreate(
                ['key' => $key],
                ['title' => $title, 'content' => $content],
            );
        }

        $legacyLabels = [
            'services_heading' => ['Practice Areas' => 'Legal Services'],
            'services_page_label' => ['Practice' => 'Legal Services'],
            'services_page_heading' => ['Practice Areas' => 'Legal Services', 'Services Areas' => 'Legal Services', 'Legal Practice' => 'Legal Services', 'Practice' => 'Services'],
            'services_intro' => ['areas of practice' => 'legal assistance', 'Practice Areas' => 'legal services'],
        ];

        foreach ($legacyLabels as $key => $replacements) {
            $pageContent = PageContent::query()->where('key', $key)->first();

            if (! $pageContent) {
                continue;
            }

            $updatedContent = str_ireplace(array_keys($replacements), array_values($replacements), $pageContent->content);

            if ($updatedContent !== $pageContent->content) {
                $pageContent->update(['content' => $updatedContent]);
            }
        }
    }
}
