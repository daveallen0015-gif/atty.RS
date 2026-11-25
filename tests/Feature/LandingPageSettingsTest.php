<?php

namespace Tests\Feature;

use App\Models\PageContent;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class LandingPageSettingsTest extends TestCase
{
    use RefreshDatabase;

    public function test_landing_page_fields_are_grouped_into_quick_access_categories(): void
    {
        $lawyer = User::factory()->create(['is_lawyer' => true]);

        $this->actingAs($lawyer)->get(route('admin.settings.edit'))
            ->assertOk()
            ->assertSee('Home hero')
            ->assertSee('Highlights')
            ->assertSee('About')
            ->assertSee('Services')
            ->assertSee('Contact')
            ->assertSee('Footer')
            ->assertSee('settings-panel-services')
            ->assertSee('Save Changes');
    }

    public function test_lawyer_can_update_database_backed_landing_page_content(): void
    {
        $lawyer = User::factory()->create(['is_lawyer' => true]);

        $this->actingAs($lawyer)->put(route('admin.settings.update'), [
            'content' => [
                'hero_title' => 'Counsel for considered decisions',
                'hero_body' => 'Personal, clear legal advice.',
                'about_body' => 'A considered biography.',
            ],
        ])->assertRedirect(route('admin.settings.edit'));

        $this->assertDatabaseHas('page_contents', [
            'key' => 'hero_title',
            'content' => 'Counsel for considered decisions',
            'updated_by' => $lawyer->id,
        ]);
        $this->assertSame('Counsel for considered decisions', PageContent::query()->where('key', 'hero_title')->value('content'));
        $this->assertDatabaseHas('page_contents', ['key' => 'contact_heading', 'content' => '']);
    }

    public function test_unknown_content_keys_are_not_saved(): void
    {
        $lawyer = User::factory()->create(['is_lawyer' => true]);

        $this->actingAs($lawyer)->put(route('admin.settings.update'), [
            'content' => ['unexpected' => 'Not an approved setting'],
        ])->assertSessionHasNoErrors();

        $this->assertDatabaseMissing('page_contents', ['key' => 'unexpected']);
    }

    public function test_lawyer_can_update_general_website_settings(): void
    {
        $lawyer = User::factory()->create(['is_lawyer' => true]);

        $this->actingAs($lawyer)->get(route('admin.website-settings.edit'))
            ->assertOk()->assertSee('Social media links')->assertSee('Contact details');

        $this->put(route('admin.website-settings.update'), [
            'content' => [
                'site_description' => 'Personal legal support and information.',
                'social_facebook' => 'https://facebook.com/example',
                'social_instagram' => '',
                'social_linkedin' => 'https://linkedin.com/in/example',
            ],
        ])->assertRedirect(route('admin.website-settings.edit'));

        $this->assertDatabaseHas('page_contents', ['key' => 'social_facebook', 'content' => 'https://facebook.com/example']);
        $this->assertDatabaseHas('page_contents', ['key' => 'site_description', 'content' => 'Personal legal support and information.']);
    }

    public function test_general_website_settings_reject_invalid_social_urls(): void
    {
        $lawyer = User::factory()->create(['is_lawyer' => true]);

        $this->actingAs($lawyer)->from(route('admin.website-settings.edit'))
            ->put(route('admin.website-settings.update'), [
                'content' => ['social_facebook' => 'not-a-secure-url'],
            ])->assertSessionHasErrors('content.social_facebook');
    }

    public function test_lawyer_can_upload_a_home_background_image(): void
    {
        Storage::fake('public');
        $lawyer = User::factory()->create(['is_lawyer' => true]);

        $this->actingAs($lawyer)->put(route('admin.settings.update'), [
            'hero_background' => UploadedFile::fake()->create('courthouse.jpg', 400, 'image/jpeg'),
        ])->assertRedirect(route('admin.settings.edit'));

        $path = PageContent::query()->where('key', 'hero_background')->value('content');
        $this->assertNotEmpty($path);
        Storage::disk('public')->assertExists($path);
    }
}
