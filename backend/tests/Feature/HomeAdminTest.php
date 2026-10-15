<?php

namespace Tests\Feature;

use App\Filament\Pages\Home\ManageHomeIntro;
use App\Filament\Pages\Home\ManageHomeNews;
use App\Filament\Pages\Home\ManageHomeProducts;
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

    public function test_each_home_section_page_saves_only_its_own_fields(): void
    {
        Livewire::test(ManageHomeIntro::class)
            ->assertSet('data.intro_title_en', 'We Provide A Cutting Edge Solution For What You Need')
            ->set('data.intro_title_en', 'New intro')
            ->call('save')
            ->assertHasNoErrors();

        Livewire::test(ManageHomeProducts::class)
            ->set('data.products_title_en', 'New products title')
            ->call('save')
            ->assertHasNoErrors();

        Livewire::test(ManageHomeNews::class)
            ->set('data.news_kicker_en', 'NEWS')
            ->call('save')
            ->assertHasNoErrors();

        $home = HomePage::singleton();
        $this->assertSame('New intro', $home->intro_title_en);
        $this->assertSame('New products title', $home->products_title_en);
        $this->assertSame('NEWS', $home->news_kicker_en);
        $this->assertSame('Latest News & Insights', $home->news_title_en);
    }

    public function test_intro_rejects_unsafe_links(): void
    {
        Livewire::test(ManageHomeIntro::class)
            ->set('data.intro_cta_url', 'javascript:alert(1)')
            ->call('save')
            ->assertHasErrors(['data.intro_cta_url']);

        $this->assertSame('/about', HomePage::singleton()->intro_cta_url);
    }

    public function test_hero_slides_can_be_listed_and_created(): void
    {
        Livewire::test(ManageHeroSlides::class)
            ->assertCanSeeTableRecords(HeroSlide::all())
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

    public function test_seeder_does_not_restore_deleted_slides(): void
    {
        HeroSlide::query()->delete();

        $this->seed(HomeSeeder::class);

        $this->assertSame(0, HeroSlide::count());
    }
}
