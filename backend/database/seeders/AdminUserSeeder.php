<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public const APPLIED_PASSWORD_KEY = 'cms:admin-password-applied';

    /**
     * Admin account from ADMIN_EMAIL / ADMIN_PASSWORD, for hosts where
     * `php artisan make:filament-user` can't be run (no shell on Vercel).
     * Runs on every boot:
     *  - no account with that email yet → it is created;
     *  - ADMIN_PASSWORD was CHANGED in the host's env (then redeployed) → the
     *    account's password is updated to it. While the value stays the same
     *    the account's password is left alone.
     * The last applied value is remembered only as a bcrypt hash (never the
     * plain text) in the database cache. Owner-approved auth change, 2026-11-02.
     */
    public function run(): void
    {
        $email = config('app.initial_admin.email');
        $password = config('app.initial_admin.password');

        if (blank($email) || blank($password)) {
            return;
        }

        $user = User::query()->where('email', $email)->first();

        if (! $user) {
            User::create([
                'name' => 'Admin',
                'email' => $email,
                'password' => $password,
            ]);
            $this->rememberApplied($password);
            $this->command?->info("Admin user {$email} created");

            return;
        }

        $applied = Cache::get(self::APPLIED_PASSWORD_KEY);

        if (is_string($applied) && Hash::check($password, $applied)) {
            return; // ADMIN_PASSWORD unchanged since it was last applied.
        }

        if (! Hash::check($password, $user->password)) {
            $user->update(['password' => $password]);
            $this->command?->info("Admin user {$email}: password updated from ADMIN_PASSWORD");
        }

        $this->rememberApplied($password);
    }

    private function rememberApplied(string $password): void
    {
        Cache::forever(self::APPLIED_PASSWORD_KEY, Hash::make($password));
    }
}
