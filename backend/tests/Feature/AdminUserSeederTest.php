<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\AdminUserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminUserSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_creates_the_admin_once_and_never_overwrites_it(): void
    {
        config(['app.initial_admin' => ['email' => 'design@indocyber.id', 'password' => 'first-password']]);
        $this->seed(AdminUserSeeder::class);

        $user = User::where('email', 'design@indocyber.id')->firstOrFail();
        $this->assertTrue(Hash::check('first-password', $user->password));

        config(['app.initial_admin.password' => 'other-password']);
        $this->seed(AdminUserSeeder::class);

        $this->assertSame(1, User::count());
        $this->assertTrue(Hash::check('first-password', $user->fresh()->password));
    }

    public function test_does_nothing_without_env_values(): void
    {
        config(['app.initial_admin' => ['email' => null, 'password' => null]]);
        $this->seed(AdminUserSeeder::class);

        $this->assertSame(0, User::count());
    }
}
