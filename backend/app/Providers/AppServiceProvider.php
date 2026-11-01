<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // When media lives in object storage (R2), Livewire's temporary
        // uploads go there too, straight from the browser via a signed URL.
        // On stateless hosts (Vercel) the upload and the form save can hit
        // different instances, and request bodies are size-capped.
        $mediaDisk = config('filesystems.media_disk');

        if (config("filesystems.disks.{$mediaDisk}.driver") === 's3') {
            config(['livewire.temporary_file_upload.disk' => $mediaDisk]);
        }
    }
}
