<?php

namespace Tests\Feature;

use App\Models\Inquiry;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InquiryManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_lawyer_can_review_and_update_an_inquiry(): void
    {
        $lawyer = User::factory()->create(['is_lawyer' => true]);
        $inquiry = Inquiry::query()->create([
            'name' => 'Alex Chen',
            'email' => 'alex@example.com',
            'subject' => 'Property question',
            'message' => 'A private visitor message.',
        ]);

        $this->actingAs($lawyer)->get(route('admin.inquiries.index', ['status' => 'new']))
            ->assertOk()->assertSee('Alex Chen')->assertSee('Property question');
        $this->get(route('admin.inquiries.show', $inquiry))->assertOk()->assertSee('A private visitor message.');
        $this->patch(route('admin.inquiries.update', $inquiry), ['status' => 'archived'])
            ->assertRedirect(route('admin.inquiries.show', $inquiry));

        $this->assertDatabaseHas('inquiries', ['id' => $inquiry->id, 'status' => 'archived']);
    }

    public function test_invalid_inquiry_status_is_rejected(): void
    {
        $lawyer = User::factory()->create(['is_lawyer' => true]);
        $inquiry = Inquiry::query()->create([
            'name' => 'Alex Chen',
            'email' => 'alex@example.com',
            'subject' => 'Property question',
            'message' => 'A private visitor message.',
        ]);

        $this->actingAs($lawyer)->patch(route('admin.inquiries.update', $inquiry), ['status' => 'deleted'])
            ->assertSessionHasErrors('status');
        $this->assertDatabaseHas('inquiries', ['id' => $inquiry->id, 'status' => 'new']);
    }

    public function test_lawyer_can_delete_an_inquiry(): void
    {
        $lawyer = User::factory()->create(['is_lawyer' => true]);
        $inquiry = Inquiry::query()->create([
            'name' => 'Alex Chen',
            'email' => 'alex@example.com',
            'subject' => 'Property question',
            'message' => 'Please remove this inquiry.',
        ]);

        $this->actingAs($lawyer)->delete(route('admin.inquiries.destroy', $inquiry))
            ->assertRedirect(route('admin.inquiries.index'));
        $this->assertDatabaseMissing('inquiries', ['id' => $inquiry->id]);
    }
}
