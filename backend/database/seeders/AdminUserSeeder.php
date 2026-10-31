<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class AdminUserSeeder extends Seeder
{
    /**
     * Creates the first admin account from ADMIN_EMAIL / ADMIN_PASSWORD,
     * for hosts where `php artisan make:filament-user` can't be run (no
     * shell on Render's free tier). Runs on every boot but never touches an
     * existing account, so a password changed later is not overwritten.
     */
    public function run(): void
    {
        $email = config('app.initial_admin.email');
        $password = config('app.initial_admin.password');

        if (blank($email) || blank($password)) {
            return;
        }

        if (User::query()->where('email', $email)->exists()) {
            return;
        }

        User::create([
            'name' => 'Admin',
            'email' => $email,
            'password' => $password,
        ]);

        $this->command?->info("Admin user {$email} created");
    }
}
