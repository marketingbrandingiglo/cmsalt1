<?php

namespace Tests\Feature;

use App\Models\HeroSlide;
use Database\Seeders\HomeSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class DefaultHeroSlidesTest extends TestCase
{
    use RefreshDatabase;

    private function migration(): object
    {
        return require database_path('migrations/2026_11_02_000000_replace_legacy_default_hero_slides.php');
    }

    private function createLegacySlides(): void
    {
        foreach ([
            'AI Skills Shortage Becomes a New Challenge for the Financial Industry',
            'How to Accelerate Your Business System',
            'Why AI Performs Better with SAP GROW Than Disconnected Systems',
        ] as $i => $title) {
            HeroSlide::create(['title_id' => $title, 'title_en' => $title, 'order' => $i]);
        }
    }

    public function test_seed_matches_the_live_frontend_hero_with_photos(): void
    {
        Storage::fake('public');
        $this->seed(HomeSeeder::class);

        $slides = HeroSlide::orderBy('order')->get();
        $this->assertCount(4, $slides);
        $this->assertSame('hero-slides/banner_1.jpg', $slides[0]->image_path);
        Storage::disk('public')->assertExists('hero-slides/banner_4.jpg');

        $this->getJson('/api/home/hero-slides?locale=id')
            ->assertJsonCount(4, 'data')
            ->assertJsonPath('data.1.title', 'Dari Otomasi Workflow Menuju Digital Workforce: Mengenal Creatio 10x')
            ->assertJsonPath('data.0.imageUrl', Storage::disk('public')->url('hero-slides/banner_1.jpg'))
            ->assertJsonPath('data.0.ctaUrl', '/news');
    }

    public function test_migration_replaces_untouched_legacy_placeholder_slides(): void
    {
        Storage::fake('public');
        $this->createLegacySlides();

        $this->migration()->up();

        $this->assertSame(4, HeroSlide::count());
        $this->assertNotNull(HeroSlide::orderBy('order')->first()->image_path);
    }

    public function test_migration_leaves_edited_slides_alone(): void
    {
        Storage::fake('public');
        $this->createLegacySlides();
        HeroSlide::orderBy('order')->first()->update(['title_en' => 'Edited by an editor']);

        $this->migration()->up();

        $this->assertSame(3, HeroSlide::count());
        $this->assertTrue(HeroSlide::where('title_en', 'Edited by an editor')->exists());
    }
}
