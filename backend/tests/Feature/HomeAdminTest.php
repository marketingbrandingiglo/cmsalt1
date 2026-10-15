<?php

namespace Tests\Feature;

use App\Filament\Pages\ManageHome;
use App\Filament\Resources\HeroSlides\Pages\ManageHeroSlides;
use App\Models\HeroSlide;
use App\Models\HomePage;
use App\Models\User;
use Database\Seeders\HomeSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class HomeAdminTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->actingAs(User::factory()->create());
        $this->seed(HomeSeeder::class);
    }

    public function test_admin_pages_render(): void
    {
        Livewire::test(ManageHome::class)
            ->assertOk()
            ->assertSee('Intro / Tagline')
            ->assertSet('data.news_title_en', 'Latest News & Insights');
        Livewire::test(ManageHeroSlides::class)
            ->assertOk()
            ->assertCanSeeTableRecords(HeroSlide::all());
    }

    public function test_manage_home_saves(): void
    {
        Livewire::test(ManageHome::class)
            ->set('data.intro_title_en', 'New title')
            ->call('save')
            ->assertHasNoErrors();

        $this->assertSame('New title', HomePage::singleton()->intro_title_en);
    }

    public function test_manage_home_rejects_unsafe_link(): void
    {
        Livewire::test(ManageHome::class)
            ->set('data.intro_cta_url', 'javascript:alert(1)')
            ->call('save')
            ->assertHasErrors(['data.intro_cta_url']);

        $this->assertSame('/about', HomePage::singleton()->intro_cta_url);
    }

    public function test_hero_slide_can_be_created(): void
    {
        Livewire::test(ManageHeroSlides::class)
            ->callAction('create', [
                'title_id' => 'Judul baru',
                'title_en' => 'New slide',
                'cta_url' => '/products',
                'is_active' => true,
                'order' => 5,
            ])
            ->assertHasNoActionErrors();

        $this->assertDatabaseHas(HeroSlide::class, ['title_en' => 'New slide', 'cta_url' => '/products']);
    }
}
