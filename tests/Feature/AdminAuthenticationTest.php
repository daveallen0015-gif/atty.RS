<?php

namespace Tests\Feature;

use App\Models\Inquiry;
use App\Models\Service;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminAuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_page_is_public(): void
    {
        $this->get(route('admin.login'))
            ->assertOk()
            ->assertSee('auth-brand', false)
            ->assertSee('LP')
            ->assertSee('aria-label="Back to landing page"', false)
            ->assertSee(route('home'))
            ->assertSee('This portal is exclusively for the lawyer.');
    }

    public function test_admin_dashboard_requires_a_lawyer_account(): void
    {
        $this->get(route('admin.dashboard'))->assertRedirect(route('admin.login'));

        $this->actingAs(User::factory()->create(['is_lawyer' => false]))
            ->get(route('admin.dashboard'))
            ->assertForbidden();
    }

    public function test_lawyer_can_sign_in_and_sign_out(): void
    {
        $lawyer = User::factory()->create(['is_lawyer' => true]);

        $this->post(route('admin.login.store'), [
            'email' => $lawyer->email,
            'password' => 'password',
        ])->assertRedirect(route('admin.dashboard'));

        $this->assertAuthenticatedAs($lawyer);
        $this->get(route('admin.dashboard'))->assertOk();

        $this->post(route('admin.logout'))->assertRedirect(route('home'));
        $this->assertGuest();
    }

    public function test_invalid_credentials_are_rejected(): void
    {
        User::factory()->create(['is_lawyer' => true]);

        $this->from(route('admin.login'))
            ->post(route('admin.login.store'), [
                'email' => 'lawyer@example.com',
                'password' => 'incorrect',
            ])
            ->assertSessionHasErrors('email');
    }

    public function test_dashboard_shows_recent_activity_summaries(): void
    {
        $lawyer = User::factory()->create(['is_lawyer' => true]);
        Service::query()->create(['title' => 'Published service', 'slug' => 'published-service', 'is_published' => true]);
        Service::query()->create(['title' => 'Draft service', 'slug' => 'draft-service', 'is_published' => false]);
        Inquiry::query()->create([
            'name' => 'Alex Chen',
            'email' => 'alex@example.com',
            'subject' => 'A recent question',
            'message' => 'Message',
        ]);

        $this->actingAs($lawyer)->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee('Legal services')
            ->assertSee('A recent question');
    }
}
