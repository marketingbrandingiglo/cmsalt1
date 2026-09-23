<?php

namespace Tests\Feature;

use App\Filament\Pages\ManageAboutUs;
use App\Filament\Pages\ManageBanner;
use App\Filament\Resources\Milestones\Pages\ManageMilestones;
use App\Filament\Resources\Values\Pages\ManageValues;
use App\Models\AboutUs;
use App\Models\AboutValue;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class AboutUsAdminPanelTest extends TestCase
{
    use RefreshDatabase;

    public function test_manage_about_us_page_saves_description_image_stats_and_video(): void
    {
        $this->actingAs(User::factory()->create());

        Livewire::test(ManageAboutUs::class)
            ->fillForm([
                'company_name' => 'Indocyber',
                'description_id' => 'Desc ID',
                'description_en' => 'Desc EN',
                'vision_id' => 'V ID',
                'vision_en' => 'V EN',
                'mission_id' => 'M ID',
                'mission_en' => 'M EN',
                'video_title_id' => 'VT ID',
                'video_title_en' => 'VT EN',
                'video_description_id' => 'VD ID',
                'video_description_en' => 'VD EN',
                'video_youtube_url' => 'https://www.youtube.com/embed/xyz',
                'stats' => [
                    ['value' => '50', 'note_id' => 'N ID', 'note_en' => 'N EN'],
                    ['icon' => 'layers', 'note_id' => 'N2 ID', 'note_en' => 'N2 EN'],
                ],
            ])
            ->call('save')
            ->assertHasNoFormErrors();

        $aboutUs = AboutUs::singleton();
        $this->assertSame('Indocyber', $aboutUs->company_name);
        $this->assertSame('VT EN', $aboutUs->video_title_en);
        $this->assertSame('https://www.youtube.com/embed/xyz', $aboutUs->video_youtube_url);
        $this->assertSame(2, $aboutUs->stats()->count());
        $numberStat = $aboutUs->stats()->whereNotNull('value')->first();
        $iconStat = $aboutUs->stats()->whereNotNull('icon')->first();
        $this->assertSame('50', $numberStat->value);
        $this->assertNull($numberStat->icon);
        $this->assertSame('layers', $iconStat->icon);
        $this->assertNull($iconStat->value);
    }

    public function test_manage_about_us_page_no_longer_manages_banner_or_milestone_section(): void
    {
        $this->actingAs(User::factory()->create());

        Livewire::test(ManageAboutUs::class)
            ->assertFormFieldDoesNotExist('banner_title_id')
            ->assertFormFieldDoesNotExist('milestone_title_id');
    }

    public function test_banner_page_saves_image_title_and_description(): void
    {
        Storage::fake('public');
        $this->actingAs(User::factory()->create());

        Livewire::test(ManageBanner::class)
            ->fillForm([
                'banner_image_path' => UploadedFile::fake()->image('banner.png'),
                'banner_title_id' => 'Judul ID',
                'banner_title_en' => 'Title EN',
                'banner_description_id' => 'Deskripsi ID',
                'banner_description_en' => 'Desc EN',
            ])
            ->call('save')
            ->assertHasNoFormErrors();

        $aboutUs = AboutUs::singleton();
        $this->assertSame('Title EN', $aboutUs->banner_title_en);
        Storage::disk('public')->assertExists($aboutUs->banner_image_path);

        $response = $this->getJson('/api/about-us?locale=en');
        $response->assertJsonPath('data.banner.title', 'Title EN');
        $response->assertJsonPath('data.banner.imageUrl', fn ($url) => str_contains($url, $aboutUs->banner_image_path));
    }

    public function test_milestones_page_saves_section_heading_above_the_list(): void
    {
        $this->actingAs(User::factory()->create());

        Livewire::test(ManageMilestones::class)
            ->fillForm([
                'milestone_title_id' => 'MT ID',
                'milestone_title_en' => 'MT EN',
                'milestone_description_id' => 'MD ID',
                'milestone_description_en' => 'MD EN',
            ], 'headerForm')
            ->call('saveHeader')
            ->assertHasNoFormErrors([], 'headerForm');

        $aboutUs = AboutUs::singleton();
        $this->assertSame('MT EN', $aboutUs->milestone_title_en);
        $this->assertSame('MD EN', $aboutUs->milestone_description_en);
    }

    public function test_values_resource_supports_create_edit_and_delete(): void
    {
        $this->actingAs(User::factory()->create());

        Livewire::test(ManageValues::class)
            ->mountAction('create')
            ->setActionData([
                'title' => 'Integrity',
                'description_id' => 'Deskripsi ID',
                'description_en' => 'Description EN',
                'order' => 0,
            ])
            ->callMountedAction()
            ->assertHasNoActionErrors();

        $value = AboutValue::firstWhere('title', 'Integrity');
        $this->assertNotNull($value);
        $this->assertSame(AboutUs::singleton()->id, $value->about_us_id);

        Livewire::test(ManageValues::class)
            ->mountTableAction('edit', record: $value)
            ->setTableActionData([
                'title' => 'Integrity Updated',
                'description_id' => 'Deskripsi ID',
                'description_en' => 'Description EN',
                'order' => 0,
            ])
            ->callMountedTableAction()
            ->assertHasNoTableActionErrors();

        $this->assertSame('Integrity Updated', $value->fresh()->title);

        Livewire::test(ManageValues::class)
            ->callTableAction('delete', record: $value);

        $this->assertNull(AboutValue::find($value->id));
    }

    public function test_value_image_description_image_and_stat_icon_uploads_are_stored_and_served(): void
    {
        Storage::fake('public');
        $this->actingAs(User::factory()->create());

        Livewire::test(ManageValues::class)
            ->mountAction('create')
            ->setActionData([
                'title' => 'Innovative',
                'image_path' => UploadedFile::fake()->image('innovative.png'),
                'order' => 0,
            ])
            ->callMountedAction()
            ->assertHasNoActionErrors();

        $value = AboutValue::firstWhere('title', 'Innovative');
        Storage::disk('public')->assertExists($value->image_path);

        Livewire::test(ManageAboutUs::class)
            ->fillForm([
                'company_name' => 'Indocyber',
                'description_id' => 'Desc ID',
                'description_en' => 'Desc EN',
                'description_image_path' => UploadedFile::fake()->image('i5.png'),
                'vision_id' => 'V ID',
                'vision_en' => 'V EN',
                'mission_id' => 'M ID',
                'mission_en' => 'M EN',
            ])
            ->call('save')
            ->assertHasNoFormErrors();

        $aboutUs = AboutUs::singleton()->fresh();
        Storage::disk('public')->assertExists($aboutUs->description_image_path);

        $stat = $aboutUs->stats()->create([
            'icon' => 'speed',
            'note_id' => 'N ID',
            'note_en' => 'N EN',
            'order' => 0,
        ]);

        $response = $this->getJson('/api/about-us?locale=en');
        $response->assertJsonPath('data.values.0.imageUrl', fn ($url) => str_contains($url, $value->image_path));
        $response->assertJsonPath('data.descriptionImageUrl', fn ($url) => str_contains($url, $aboutUs->description_image_path));
        $response->assertJsonPath('data.stats.0.icon', 'speed');
        $response->assertJsonPath('data.stats.0.text', 'N EN');
    }
}
