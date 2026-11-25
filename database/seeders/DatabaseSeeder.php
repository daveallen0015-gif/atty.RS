<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(LawyerAccountSeeder::class);
        $this->call(LandingPageContentSeeder::class);
        $this->call(ServicePlaceholderSeeder::class);
        $this->call(LawLibrarySeeder::class);
    }
}
