<?php

namespace Tests\Feature;

use App\Models\Inquiry;
use App\Models\PageContent;
use App\Models\Service;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicWebsiteTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_pages_render_database_content_and_published_services(): void
    {
        $lawyer = User::factory()->create(['name' => 'Jordan Lee', 'is_lawyer' => true]);
        $lawyer->lawyerProfile()->create([
            'professional_title' => 'Barrister',
            'photo_path' => 'lawyer-profile/portrait.jpg',
        ]);
        PageContent::query()->create(['key' => 'hero_title', 'content' => 'Counsel for considered decisions']);
        PageContent::query()->create(['key' => 'hero_accent_title', 'content' => 'Commitment to Justice']);
        PageContent::query()->create(['key' => 'hero_background', 'content' => 'site-backgrounds/courthouse.jpg']);
        PageContent::query()->create(['key' => 'services_heading', 'content' => 'Areas of practice']);
        PageContent::query()->create(['key' => 'services_page_heading', 'content' => 'Legal Services']);
        PageContent::query()->create(['key' => 'services_page_intro', 'content' => 'Explore guidance based on your circumstances.']);
        PageContent::query()->create(['key' => 'services_cta_heading', 'content' => 'Need Legal Assistance?']);
        Service::query()->create(['title' => 'Estate Planning', 'slug' => 'estate-planning', 'summary' => 'Planning with care.', 'description' => 'Detailed estate-planning assistance.', 'icon' => 'estate', 'is_published' => true]);
        Service::query()->create(['title' => 'Unpublished draft', 'slug' => 'draft', 'is_published' => false]);

        $this->get(route('home'))->assertOk()->assertSee('Counsel for considered decisions')->assertSee('Commitment to Justice')->assertSee('Jordan Lee')->assertSee(route('admin.login'))->assertSee('Chat with Us')->assertDontSee('>Chat</a>', false)->assertSee(asset('storage/site-backgrounds/courthouse.jpg'))->assertSee(asset('images/lawyer-emblem.svg'));
        $this->get(route('services'))->assertOk()->assertSee('Legal Services')->assertSee('Explore guidance based on your circumstances.')->assertSee('Estate Planning')->assertSee('Learn More')->assertSee('Need Legal Assistance?')->assertDontSee('Unpublished draft');
        $this->get(route('services.show', 'estate-planning'))->assertOk()->assertSee('Detailed estate-planning assistance.')->assertSee(route('contact', ['service' => 'estate-planning']));
        $this->get('/services/draft')->assertNotFound();
        $this->get(route('about'))->assertOk()->assertSee('Barrister')->assertSee(asset('storage/lawyer-profile/portrait.jpg'));
        $this->get(route('contact'))->assertOk()->assertSee('Send inquiry');
        $this->get(route('practice-areas'))->assertRedirect(route('services'));
    }

    public function test_contact_form_stores_a_valid_visitor_inquiry(): void
    {
        $this->post(route('contact.store'), [
            'name' => 'Alex Chen',
            'email' => 'alex@example.com',
            'phone' => '+886 912 345 678',
            'subject' => 'Property and Real Estate Law',
            'message' => 'I would like to discuss a property matter.',
        ])->assertRedirect(route('contact'));

        $this->assertDatabaseHas('inquiries', [
            'name' => 'Alex Chen',
            'email' => 'alex@example.com',
            'status' => 'new',
            'subject' => 'Property and Real Estate Law',
        ]);
        $this->assertSame(1, Inquiry::query()->count());
    }

    public function test_contact_form_validates_required_fields(): void
    {
        $this->from(route('contact'))->post(route('contact.store'), [
            'name' => 'Alex Chen',
            'email' => 'not-an-email',
            'subject' => '',
            'message' => '',
        ])->assertSessionHasErrors(['email', 'subject', 'message']);

        $this->assertDatabaseCount('inquiries', 0);
    }

    public function test_service_inquiry_link_prefills_the_contact_subject(): void
    {
        Service::query()->create(['title' => 'Legal Consultation', 'slug' => 'legal-consultation', 'is_published' => true]);

        $this->get(route('contact', ['service' => 'legal-consultation']))
            ->assertOk()
            ->assertSee('value="Legal Consultation" selected', false);
    }

    public function test_inquiry_form_allows_a_custom_other_subject(): void
    {
        $this->post(route('contact.store'), [
            'name' => 'Alex Chen',
            'email' => 'alex@example.com',
            'subject' => 'Other',
            'other_subject' => 'Question about an unusual contract',
            'message' => 'I would like to ask about a contract.',
        ])->assertRedirect(route('contact'));

        $this->assertDatabaseHas('inquiries', ['subject' => 'Question about an unusual contract']);
    }

    public function test_custom_subject_is_required_when_other_is_selected(): void
    {
        $this->from(route('contact'))->post(route('contact.store'), [
            'name' => 'Alex Chen',
            'email' => 'alex@example.com',
            'subject' => 'Other',
            'other_subject' => '',
            'message' => 'I would like to ask a question.',
        ])->assertSessionHasErrors('other_subject');
    }

    public function test_about_navigation_link_is_highlighted_on_the_about_page(): void
    {
        $this->get(route('about'))
            ->assertOk()
            ->assertSeeInOrder([
                'href="'.route('about').'" class="active"',
                'aria-current="page"',
                'About Lawyer',
            ], false);
    }
}
