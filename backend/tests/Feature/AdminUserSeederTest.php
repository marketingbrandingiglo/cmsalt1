<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\AdminUserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminUserSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_creates_the_admin_once(): void
    {
        config(['app.initial_admin' => ['email' => 'design@indocyber.id', 'password' => 'first-password']]);
        $this->seed(AdminUserSeeder::class);
        $this->seed(AdminUserSeeder::class);

        $this->assertSame(1, User::count());
        $this->assertTrue(Hash::check('first-password', User::sole()->password));
        $this->assertStringNotContainsString('first-password', (string) Cache::get(AdminUserSeeder::APPLIED_PASSWORD_KEY));
    }

    public function test_changing_admin_password_in_the_env_updates_the_account(): void
    {
        config(['app.initial_admin' => ['email' => 'design@indocyber.id', 'password' => 'first-password']]);
        $this->seed(AdminUserSeeder::class);

        config(['app.initial_admin.password' => 'new-password']);
        $this->seed(AdminUserSeeder::class);

        $this->assertSame(1, User::count());
        $this->assertTrue(Hash::check('new-password', User::sole()->password));
    }

    public function test_an_unchanged_env_password_never_overwrites_the_account(): void
    {
        config(['app.initial_admin' => ['email' => 'design@indocyber.id', 'password' => 'first-password']]);
        $this->seed(AdminUserSeeder::class);
        User::sole()->update(['password' => 'changed-elsewhere']);

        $this->seed(AdminUserSeeder::class);

        $this->assertTrue(Hash::check('changed-elsewhere', User::sole()->password));
    }

    public function test_an_existing_admin_from_an_older_deploy_gets_the_env_password(): void
    {
        User::factory()->create(['email' => 'design@indocyber.id', 'password' => 'old-password']);
        config(['app.initial_admin' => ['email' => 'design@indocyber.id', 'password' => 'new-password']]);

        $this->seed(AdminUserSeeder::class);

        $this->assertTrue(Hash::check('new-password', User::sole()->password));
    }

    public function test_does_nothing_without_env_values(): void
    {
        config(['app.initial_admin' => ['email' => null, 'password' => null]]);
        $this->seed(AdminUserSeeder::class);

        $this->assertSame(0, User::count());
    }
}
