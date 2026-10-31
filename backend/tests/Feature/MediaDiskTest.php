<?php

namespace Tests\Feature;

use App\Filament\Resources\HeroSlides\Pages\ManageHeroSlides;
use App\Models\HeroSlide;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class MediaDiskTest extends TestCase
{
    use RefreshDatabase;

    private const PNG = 'iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNk+M9QDwADhgGAWjR9awAAAABJRU5ErkJggg==';

    public function test_uploads_and_api_urls_use_the_configured_media_disk(): void
    {
        config(['filesystems.media_disk' => 'r2']);
        Storage::fake('r2');
        Storage::fake('public');

        $this->actingAs(User::factory()->create());

        Livewire::test(ManageHeroSlides::class)
            ->callAction('create', [
                'title_id' => 'Judul',
                'title_en' => 'Title',
                'image_path' => [UploadedFile::fake()->createWithContent('slide.png', base64_decode(self::PNG))],
                'is_active' => true,
                'order' => 0,
            ])
            ->assertHasNoActionErrors();

        $path = HeroSlide::firstOrFail()->image_path;
        Storage::disk('r2')->assertExists($path);
        Storage::disk('public')->assertMissing($path);

        $this->getJson('/api/home/hero-slides')
            ->assertJsonPath('data.0.imageUrl', Storage::disk('r2')->url($path));
    }

    public function test_media_disk_defaults_to_public(): void
    {
        $this->assertSame('public', config('filesystems.media_disk'));
    }
}
