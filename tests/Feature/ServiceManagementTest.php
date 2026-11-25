<?php

namespace Tests\Feature;

use App\Models\Service;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ServiceManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_lawyer_can_create_services_and_duplicate_titles_get_unique_slugs(): void
    {
        $lawyer = User::factory()->create(['is_lawyer' => true]);
        $payload = [
            'title' => 'Estate Planning',
            'summary' => 'Thoughtful planning for the future.',
            'description' => 'A detailed service description.',
            'icon' => 'document',
            'sort_order' => 2,
            'is_published' => '1',
        ];

        $this->actingAs($lawyer)->post(route('admin.services.store'), $payload)
            ->assertRedirect(route('admin.services.index'));
        $this->actingAs($lawyer)->post(route('admin.services.store'), $payload)
            ->assertRedirect(route('admin.services.index'));

        $this->assertDatabaseHas('services', ['slug' => 'estate-planning', 'is_published' => true]);
        $this->assertDatabaseHas('services', ['slug' => 'estate-planning', 'icon' => 'document']);
        $this->assertDatabaseHas('services', ['slug' => 'estate-planning-2']);
    }

    public function test_lawyer_can_edit_and_delete_a_service(): void
    {
        $lawyer = User::factory()->create(['is_lawyer' => true]);
        $service = Service::query()->create(['title' => 'Old title', 'slug' => 'old-title', 'sort_order' => 0]);

        $this->actingAs($lawyer)->put(route('admin.services.update', $service), [
            'title' => 'New title',
            'summary' => '',
            'description' => '',
            'sort_order' => 5,
        ])->assertRedirect(route('admin.services.index'));

        $this->assertDatabaseHas('services', ['id' => $service->id, 'slug' => 'new-title', 'is_published' => false]);

        $this->delete(route('admin.services.destroy', $service))->assertRedirect(route('admin.services.index'));
        $this->assertDatabaseMissing('services', ['id' => $service->id]);
    }
}
