<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\LawyerAccountSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use RuntimeException;
use Tests\TestCase;

class LawyerAccountSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_seeder_creates_and_updates_one_hashed_lawyer_account(): void
    {
        Config::set('lawyer.admin', [
            'name' => 'Jordan Lee',
            'email' => 'jordan@example.com',
            'password' => 'private-password',
        ]);

        $this->seed(LawyerAccountSeeder::class);
        $this->seed(LawyerAccountSeeder::class);

        $this->assertSame(1, User::query()->count());
        $this->assertTrue(User::query()->sole()->is_lawyer);
        $this->assertTrue(password_verify('private-password', User::query()->sole()->password));
    }

    public function test_seeder_refuses_to_create_another_user_account(): void
    {
        User::factory()->create(['is_lawyer' => false]);
        Config::set('lawyer.admin', [
            'name' => 'Jordan Lee',
            'email' => 'jordan@example.com',
            'password' => 'private-password',
        ]);

        $this->expectException(RuntimeException::class);
        $this->seed(LawyerAccountSeeder::class);
    }
}
