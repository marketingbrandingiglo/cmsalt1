<?php

namespace Tests\Feature;

use App\Models\AboutUs;
use App\Models\Milestone;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AboutUsApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_about_us_endpoint_returns_banner_stats_values_and_video(): void
    {
        $aboutUs = AboutUs::create([
            'company_name' => 'Indocyber',
            'banner_image_path' => 'about-banner/banner.jpg',
            'banner_title_id' => 'Judul Banner',
            'banner_title_en' => 'Banner Title',
            'banner_description_id' => 'Deskripsi banner',
            'banner_description_en' => 'Banner description',
            'description_id' => 'Deskripsi ID',
            'description_en' => 'Description EN',
            'description_image_path' => 'about-description/i5.png',
            'vision_id' => 'Visi ID',
            'vision_en' => 'Vision EN',
            'mission_id' => 'Misi ID',
            'mission_en' => 'Mission EN',
            'milestone_title_id' => 'Judul Milestone',
            'milestone_title_en' => 'Milestone Title',
            'milestone_description_id' => 'Deskripsi milestone',
            'milestone_description_en' => 'Milestone description',
            'video_title_id' => 'Video Judul',
            'video_title_en' => 'Video Title',
            'video_description_id' => 'Deskripsi video',
            'video_description_en' => 'Video description',
            'video_youtube_url' => 'https://www.youtube.com/embed/abc123',
        ]);

        $aboutUs->stats()->create([
            'value' => '50',
            'label_id' => 'Label ID',
            'label_en' => 'Label EN',
            'note_id' => 'Catatan ID',
            'note_en' => 'Note EN',
            'order' => 0,
        ]);

        $aboutUs->values()->create([
            'title' => 'Integrity',
            'image_path' => 'about-values/integrity.png',
            'description_id' => 'Deskripsi nilai ID',
            'description_en' => 'Value description EN',
            'order' => 0,
        ]);

        $response = $this->getJson('/api/about-us?locale=en');

        $response->assertOk()->assertJson([
            'data' => [
                'companyName' => 'Indocyber',
                'banner' => [
                    'title' => 'Banner Title',
                    'description' => 'Banner description',
                ],
                'description' => 'Description EN',
                'vision' => 'Vision EN',
                'mission' => 'Mission EN',
                'milestoneSection' => [
                    'title' => 'Milestone Title',
                    'description' => 'Milestone description',
                ],
                'video' => [
                    'title' => 'Video Title',
                    'description' => 'Video description',
                    'youtubeUrl' => 'https://www.youtube.com/embed/abc123',
                ],
            ],
        ]);

        $response->assertJsonPath('data.banner.imageUrl', fn ($url) => str_contains($url, 'about-banner/banner.jpg'));
        $response->assertJsonPath('data.descriptionImageUrl', fn ($url) => str_contains($url, 'about-description/i5.png'));
        $response->assertJsonPath('data.stats.0.value', '50');
        $response->assertJsonPath('data.stats.0.label', 'Label EN');
        $response->assertJsonPath('data.stats.0.note', 'Note EN');
        $response->assertJsonPath('data.values.0.title', 'Integrity');
        $response->assertJsonPath('data.values.0.description', 'Value description EN');
        $response->assertJsonPath('data.values.0.imageUrl', fn ($url) => str_contains($url, 'about-values/integrity.png'));
    }

    public function test_milestones_endpoint_returns_period_and_logos(): void
    {
        $milestone = Milestone::create(['period' => '2021 - Present', 'order' => 0]);
        $milestone->logos()->create([
            'name' => 'Creatio',
            'logo_path' => 'milestones/creatio.png',
            'order' => 0,
        ]);

        $response = $this->getJson('/api/about-us/milestones');

        $response->assertOk();
        $response->assertJsonPath('data.0.period', '2021 - Present');
        $response->assertJsonPath('data.0.logos.0.name', 'Creatio');
        $response->assertJsonPath('data.0.logos.0.logoUrl', fn ($url) => str_contains($url, 'milestones/creatio.png'));
    }
}
