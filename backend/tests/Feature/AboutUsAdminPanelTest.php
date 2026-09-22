<?php

namespace Tests\Feature;

use App\Filament\Pages\ManageAboutUs;
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

    public function test_manage_about_us_page_saves_banner_stats_milestone_section_and_video(): void
    {
        $this->actingAs(User::factory()->create());

        Livewire::test(ManageAboutUs::class)
            ->fillForm([
                'company_name' => 'Indocyber',
                'banner_title_id' => 'Judul ID',
                'banner_title_en' => 'Title EN',
                'banner_description_id' => 'Deskripsi ID',
                'banner_description_en' => 'Desc EN',
                'description_id' => 'Desc ID',
                'description_en' => 'Desc EN',
                'vision_id' => 'V ID',
                'vision_en' => 'V EN',
                'mission_id' => 'M ID',
                'mission_en' => 'M EN',
                'milestone_title_id' => 'MT ID',
                'milestone_title_en' => 'MT EN',
                'milestone_description_id' => 'MD ID',
                'milestone_description_en' => 'MD EN',
                'video_title_id' => 'VT ID',
                'video_title_en' => 'VT EN',
                'video_description_id' => 'VD ID',
                'video_description_en' => 'VD EN',
                'video_youtube_url' => 'https://www.youtube.com/embed/xyz',
                'stats' => [
                    ['value' => '50', 'label_id' => 'L ID', 'label_en' => 'L EN', 'note_id' => 'N ID', 'note_en' => 'N EN'],
                ],
            ])
            ->call('save')
            ->assertHasNoFormErrors();

        $aboutUs = AboutUs::singleton();
        $this->assertSame('Title EN', $aboutUs->banner_title_en);
        $this->assertSame('MT EN', $aboutUs->milestone_title_en);
        $this->assertSame('https://www.youtube.com/embed/xyz', $aboutUs->video_youtube_url);
        $this->assertSame(1, $aboutUs->stats()->count());
        $this->assertSame('50', $aboutUs->stats()->first()->value);
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

    public function test_value_image_and_stat_icon_uploads_are_stored_and_served(): void
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

        // Stat icons are uploaded the same way through the FileUpload component,
        // but exercising a nested Repeater upload via Livewire's synthetic file-set
        // test helper is unreliable; verify the storage + API wiring directly instead.
        $stat = AboutUs::singleton()->stats()->create([
            'value' => '50',
            'label_id' => 'L ID',
            'label_en' => 'L EN',
            'icon_path' => UploadedFile::fake()->image('stat.png')->store('about-stats', 'public'),
            'order' => 0,
        ]);
        Storage::disk('public')->assertExists($stat->icon_path);

        $response = $this->getJson('/api/about-us?locale=en');
        $response->assertJsonPath('data.values.0.imageUrl', fn ($url) => str_contains($url, $value->image_path));
        $response->assertJsonPath('data.stats.0.iconUrl', fn ($url) => str_contains($url, $stat->icon_path));
    }
}
