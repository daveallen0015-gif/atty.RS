<?php

namespace Tests\Feature;

use App\Models\Service;
use Database\Seeders\ServicePlaceholderSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ServicePlaceholderSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_sample_service_categories_are_created_as_unpublished_drafts(): void
    {
        $this->seed(ServicePlaceholderSeeder::class);

        $this->assertDatabaseCount('services', 8);
        $this->assertDatabaseHas('services', [
            'slug' => 'legal-consultation',
            'icon' => 'scale',
            'is_published' => false,
        ]);
        $this->assertSame(0, Service::query()->where('is_published', true)->count());
    }
}
