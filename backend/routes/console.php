<?php

use App\Models\AboutUs;
use Database\Seeders\AdminUserSeeder;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

/*
 * Container start-up for stateless hosts (Vercel): migrate, seed the full
 * initial content only on an empty database, and make sure the first admin
 * exists. Cheap on every boot after the first one, and never re-creates
 * content an editor deleted.
 */
Artisan::command('cms:bootstrap', function () {
    $this->call('migrate', ['--force' => true]);

    if (! AboutUs::query()->exists()) {
        $this->call('db:seed', ['--force' => true]);
    } else {
        $this->call('db:seed', ['--class' => AdminUserSeeder::class, '--force' => true]);
    }
})->purpose('Migrate, seed a fresh database, and ensure the initial admin exists');
