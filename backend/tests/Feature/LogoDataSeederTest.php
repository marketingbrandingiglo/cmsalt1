<?php

namespace Tests\Feature;

use App\Models\AboutUs;
use App\Models\Milestone;
use Database\Seeders\AboutUsSeeder;
use Database\Seeders\LogoDataSeeder;
use Database\Seeders\PartnerSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class LogoDataSeederTest extends TestCase
{
    use RefreshDatabase;

    private function seedAll(): void
    {
        $this->seed(AboutUsSeeder::class);
        $this->seed(PartnerSeeder::class);
        $this->seed(LogoDataSeeder::class);
    }

    public function test_reseeding_restores_images_wiped_from_an_ephemeral_disk(): void
    {
        Storage::fake('public');

        $this->seedAll();

        $aboutUs = AboutUs::singleton();
        $bannerPath = $aboutUs->banner_image_path;
        Storage::disk('public')->assertExists($bannerPath);

        // Simulate a redeploy onto a fresh container with no persistent
        // storage: the file is gone, but the database row (assumed
        // persistent) still points at its old path.
        Storage::disk('public')->delete($bannerPath);
        Storage::disk('public')->assertMissing($bannerPath);

        $this->seed(LogoDataSeeder::class);

        $aboutUs->refresh();
        Storage::disk('public')->assertExists($aboutUs->banner_image_path);
    }

    public function test_reseeding_does_not_overwrite_an_editors_own_upload(): void
    {
        Storage::fake('public');

        $this->seedAll();

        $aboutUs = AboutUs::singleton();
        $customPath = 'about-banner/editors-own-upload.png';
        Storage::disk('public')->put($customPath, 'not the seed asset');
        $aboutUs->update(['banner_image_path' => $customPath]);

        $this->seed(LogoDataSeeder::class);

        $this->assertSame($customPath, $aboutUs->fresh()->banner_image_path);
    }

    public function test_milestones_are_seeded_with_the_newest_period_first(): void
    {
        $this->seedAll();

        $periods = Milestone::orderByDesc('order')->pluck('period')->all();

        $this->assertSame([
            '2021 - Present',
            '2016 - 2020',
            '2011 - 2015',
            '2006 - 2010',
            '2001 - 2005',
        ], $periods);
    }
}
