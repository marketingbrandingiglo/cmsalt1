<?php

namespace Tests\Feature;

use App\Models\AboutUs;
use App\Models\HeroSlide;
use App\Models\User;
use App\Providers\AppServiceProvider;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class CmsBootstrapCommandTest extends TestCase
{
    use RefreshDatabase;

    public function test_seeds_a_fresh_database_once_and_keeps_editor_deletions(): void
    {
        Storage::fake('public');
        config(['app.initial_admin' => ['email' => 'design@indocyber.id', 'password' => 'secret-password']]);

        $this->artisan('cms:bootstrap')->assertSuccessful();

        $this->assertTrue(AboutUs::query()->exists());
        $this->assertSame(3, HeroSlide::count());
        $this->assertTrue(User::where('email', 'design@indocyber.id')->exists());

        HeroSlide::query()->delete();
        $this->artisan('cms:bootstrap')->assertSuccessful();

        $this->assertSame(0, HeroSlide::count());
        $this->assertSame(1, User::count());
    }

    public function test_livewire_temporary_uploads_follow_an_object_storage_media_disk(): void
    {
        config(['filesystems.media_disk' => 'r2', 'livewire.temporary_file_upload.disk' => null]);
        (new AppServiceProvider($this->app))->boot();
        $this->assertSame('r2', config('livewire.temporary_file_upload.disk'));

        config(['filesystems.media_disk' => 'public', 'livewire.temporary_file_upload.disk' => null]);
        (new AppServiceProvider($this->app))->boot();
        $this->assertNull(config('livewire.temporary_file_upload.disk'));
    }
}
