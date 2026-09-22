<?php

namespace Tests\Feature;

use App\Models\ClientCategory;
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
        yield 'Banner' => ['/admin/manage-banner'];
        yield 'Visi Misi' => ['/admin/manage-about-us'];
        yield 'Client Categories' => ['/admin/client-categories'];
        yield 'Milestones' => ['/admin/milestones'];
        yield 'Partners' => ['/admin/partners'];
        yield 'Values' => ['/admin/values'];
    }

    #[DataProvider('adminPages')]
    public function test_page_loads_for_an_authenticated_editor(string $path): void
    {
        $this->actingAs(User::factory()->create())
            ->get($path)
            ->assertOk();
    }

    public function test_client_category_edit_page_loads_with_nested_clients(): void
    {
        $category = ClientCategory::create(['name_id' => 'Bank', 'name_en' => 'Bank', 'order' => 0]);
        $category->clients()->create(['name' => 'BCA', 'order' => 0]);

        $this->actingAs(User::factory()->create())
            ->get("/admin/client-categories/{$category->id}/edit")
            ->assertOk();
    }
}
