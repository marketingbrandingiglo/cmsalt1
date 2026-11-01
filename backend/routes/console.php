<?php

use Database\Seeders\AdminUserSeeder;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

/*
 * Container start-up for stateless hosts (Vercel), run in the background
 * while the web server is already listening: migrate, then seed the initial
 * content until it has completed once (marker in the database cache, so a
 * run interrupted by an instance being stopped resumes next time; every
 * seeder is idempotent), and make sure the first admin exists. Never
 * re-creates content an editor deleted after the initial seed.
 */
Artisan::command('cms:bootstrap', function () {
    $this->call('migrate', ['--force' => true]);

    if (! Cache::get('cms:initial-seed-done')) {
        $this->call('db:seed', ['--force' => true]);
        Cache::forever('cms:initial-seed-done', true);
    } else {
        $this->call('db:seed', ['--class' => AdminUserSeeder::class, '--force' => true]);
    }
})->purpose('Migrate, finish the initial seed, and ensure the initial admin exists');
