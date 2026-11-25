<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use RuntimeException;

class LawyerAccountSeeder extends Seeder
{
    public function run(): void
    {
        $name = config('lawyer.admin.name');
        $email = config('lawyer.admin.email');
        $password = config('lawyer.admin.password');

        if (! $name || ! $email || ! $password) {
            throw new RuntimeException('Set LAWYER_ADMIN_NAME, LAWYER_ADMIN_EMAIL, and LAWYER_ADMIN_PASSWORD before seeding the lawyer account.');
        }

        $lawyer = User::query()->where('is_lawyer', true)->first();

        if (! $lawyer && User::query()->exists()) {
            throw new RuntimeException('A user account already exists. Refusing to create a second account.');
        }

        $lawyer ??= new User;
        $lawyer->fill([
            'name' => $name,
            'email' => $email,
            'password' => $password,
            'is_lawyer' => true,
        ])->save();
    }
}
