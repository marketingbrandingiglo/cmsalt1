<?php

namespace Tests\Feature;

use App\Filament\Pages\Halaman;
use App\Filament\Pages\Halaman\ManageAboutUs;
use App\Filament\Pages\Halaman\ManageHome;
use App\Models\AboutUs;
use App\Models\ClientCategory;
use App\Models\HeroSlide;
use App\Models\HomePage;
use App\Models\Milestone;
use App\Models\User;
use Database\Seeders\AboutUsSeeder;
use Database\Seeders\HomeSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class HalamanAdminTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->actingAs(User::factory()->create(['email' => 'editor@indocyber.id']));
        $this->seed([HomeSeeder::class, AboutUsSeeder::class]);
    }

    public function test_halaman_lists_every_website_page(): void
    {
        $this->get('/admin/halaman')
            ->assertOk()
            ->assertSee('Beranda')
            ->assertSee('Tentang Kami')
            ->assertSee(ManageHome::getUrl())
            ->assertSee(ManageAboutUs::getUrl());
    }

    public function test_sidebar_has_a_single_halaman_entry(): void
    {
        $this->get('/admin/halaman/beranda')
            ->assertOk()
            ->assertSee('Halaman')
            ->assertDontSee('Hero Slides</span>', false)
            ->assertDontSee('/admin/milestones')
            ->assertDontSee('/admin/partners');

        $this->assertFalse(ManageHome::shouldRegisterNavigation());
        $this->assertFalse(ManageAboutUs::shouldRegisterNavigation());
        $this->assertTrue(Halaman::shouldRegisterNavigation());
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

    public function test_about_us_editor_keeps_existing_rows_and_order(): void
    {
        $milestoneIds = Milestone::orderByDesc('order')->pluck('id')->all();
        $categoryIds = ClientCategory::orderBy('order')->pluck('id')->all();

        Livewire::test(ManageAboutUs::class)
            ->assertOk()
            ->set('data.vision_en', 'New vision')
            ->call('save')
            ->assertHasNoErrors();

        $this->assertSame('New vision', AboutUs::singleton()->vision_en);
        $this->assertSame($milestoneIds, Milestone::orderByDesc('order')->pluck('id')->all());
        $this->assertSame($categoryIds, ClientCategory::orderBy('order')->pluck('id')->all());

        $this->getJson('/api/about-us?locale=en')->assertJsonPath('data.vision', 'New vision');
    }

    public function test_about_us_editor_adds_nested_rows(): void
    {
        Storage::fake('public');

        $component = Livewire::test(ManageAboutUs::class);
        $categories = $component->get('data.client_categories');
        $firstKey = array_key_first($categories);
        $categories[$firstKey]['clients'] = ['c1' => ['name' => 'Bank ABC', 'website_url' => 'https://abc.example', 'logo_path' => []]];
        $logo = UploadedFile::fake()->createWithContent('abc.png', base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNk+M9QDwADhgGAWjR9awAAAABJRU5ErkJggg=='));

        $milestones = $component->get('data.milestones');
        $milestones = ['m-new' => ['year' => '2030', 'events' => ['e1' => ['text_id' => 'Acara', 'text_en' => 'Event']]]] + $milestones;

        $component
            ->set('data.client_categories', $categories)
            ->set("data.client_categories.{$firstKey}.clients.c1.logo_path", [$logo])
            ->set('data.milestones', $milestones)
            ->call('save')
            ->assertHasNoErrors();

        $this->getJson('/api/about-us/milestones?locale=en')
            ->assertJsonPath('data.0.year', '2030')
            ->assertJsonPath('data.0.events.0.text', 'Event');
        $this->getJson('/api/about-us/client-categories')
            ->assertJsonPath('data.0.clients.0.name', 'Bank ABC');

        $logoPath = ClientCategory::orderBy('order')->first()->clients()->first()->logo_path;
        $this->assertStringStartsWith('clients/', $logoPath);
        Storage::disk('public')->assertExists($logoPath);
    }
}
