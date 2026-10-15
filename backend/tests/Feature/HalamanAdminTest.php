<?php

namespace Tests\Feature;

use App\Filament\Pages\Halaman;
use App\Filament\Pages\Halaman\ManageHome;
use App\Models\HeroSlide;
use App\Models\HomePage;
use App\Models\User;
use Database\Seeders\HomeSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class HalamanAdminTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->actingAs(User::factory()->create(['email' => 'editor@indocyber.id']));
        $this->seed(HomeSeeder::class);
    }

    public function test_halaman_lists_every_website_page(): void
    {
        $this->get('/admin/halaman')
            ->assertOk()
            ->assertSee('Beranda')
            ->assertSee(ManageHome::getUrl());
    }

    public function test_beranda_is_reached_through_halaman_not_its_own_menu(): void
    {
        $this->get('/admin/halaman/beranda')->assertOk()->assertSee('Hero Slides');

        $this->assertFalse(ManageHome::shouldRegisterNavigation());
        $this->assertTrue(Halaman::shouldRegisterNavigation());
    }

    public function test_seeder_does_not_restore_deleted_slides(): void
    {
        HeroSlide::query()->delete();

        $this->seed(HomeSeeder::class);

        $this->assertSame(0, HeroSlide::count());
    }

    public function test_home_editor_saves_sections_and_slides(): void
    {
        $component = Livewire::test(ManageHome::class)->assertOk();
        $slides = $component->get('data.hero_slides');
        $firstKey = array_key_first($slides);
        $lastKey = array_key_last($slides);
        $removedId = $slides[$lastKey]['id'];

        $slides[$firstKey]['title_en'] = 'Edited slide';
        unset($slides[$lastKey]);
        $slides['new'] = ['title_id' => 'Baru', 'title_en' => 'New', 'cta_url' => '/products', 'is_active' => false];

        $component
            ->set('data.intro_title_en', 'New intro')
            ->set('data.hero_slides', $slides)
            ->call('save')
            ->assertHasNoErrors();

        $this->assertSame('New intro', HomePage::singleton()->intro_title_en);
        $this->assertSame(3, HeroSlide::count());
        $this->assertNull(HeroSlide::find($removedId));
        $this->assertSame('Edited slide', HeroSlide::orderBy('order')->first()->title_en);
        $this->assertSame(['Edited slide', 'How to Accelerate Your Business System', 'New'], HeroSlide::orderBy('order')->pluck('title_en')->all());
        $this->assertFalse(HeroSlide::where('title_en', 'New')->first()->is_active);
    }

    public function test_home_editor_rejects_unsafe_links(): void
    {
        Livewire::test(ManageHome::class)
            ->set('data.intro_cta_url', 'javascript:alert(1)')
            ->call('save')
            ->assertHasErrors(['data.intro_cta_url']);

        $this->assertSame('/about', HomePage::singleton()->intro_cta_url);
    }
}
