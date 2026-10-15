<?php

namespace Tests\Feature;

use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminPanelAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_indocyber_staff_can_access_admin_panel(): void
    {
        $user = User::factory()->create(['email' => 'Editor@Indocyber.id']);

        $this->assertTrue($user->canAccessPanel(Filament::getPanel('admin')));
        $this->actingAs($user)->get('/admin')->assertOk();
    }

    public function test_other_users_are_denied(): void
    {
        foreach (['someone@gmail.com', 'x@indocyber.id.evil.com', 'x@notindocyber.id'] as $email) {
            $user = User::factory()->create(['email' => $email]);

            $this->assertFalse($user->canAccessPanel(Filament::getPanel('admin')), $email);
            $this->actingAs($user)->get('/admin')->assertForbidden();
        }
    }
}
