<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class AdminPanelNavigationTest extends TestCase
{
    use RefreshDatabase;

    /** @return iterable<string, array{string}> */
    public static function adminPages(): iterable
    {
        yield 'Visi Misi' => ['/admin/manage-about-us'];
        yield 'Client Categories' => ['/admin/client-categories'];
        yield 'Milestones' => ['/admin/milestones'];
        yield 'Partners' => ['/admin/partners'];
    }

    #[DataProvider('adminPages')]
    public function test_page_loads_for_an_authenticated_editor(string $path): void
    {
        $this->actingAs(User::factory()->create())
            ->get($path)
            ->assertOk();
    }
}
