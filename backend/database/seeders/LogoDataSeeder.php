<?php

namespace Database\Seeders;

use App\Models\ClientCategory;
use App\Models\Milestone;
use App\Models\Partner;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class LogoDataSeeder extends Seeder
{
    /**
     * Migrates the real partner/client/milestone logos already used by the
     * iglo frontend's static About page (lib/content.js) into the CMS, so
     * editors take over from real data instead of a blank slate. Reads
     * database/seed-assets/manifest.json (names + filenames) and copies the
     * matching image from database/seed-assets/ into the public disk. Safe
     * to re-run: upserts by name, so it just re-copies the same files.
     */
    public function run(): void
    {
        $manifest = json_decode(File::get(database_path('seed-assets/manifest.json')), true);

        $this->seedPartners($manifest['partners']);
        $this->seedMilestoneLogos($manifest['milestones']);
        $this->seedClients($manifest['clients']);
    }

    private function copyAsset(string $relativeDir, string $file): string
    {
        $source = database_path("seed-assets/{$relativeDir}/{$file}");
        $destination = "{$relativeDir}/{$file}";
        Storage::disk('public')->put($destination, File::get($source));

        return $destination;
    }

    private function seedPartners(array $partners): void
    {
        foreach ($partners as $i => $p) {
            $logoPath = $this->copyAsset('partners', $p['file']);
            Partner::updateOrCreate(
                ['name' => $p['name']],
                ['logo_path' => $logoPath, 'order' => $i],
            );
        }
    }

    private function seedMilestoneLogos(array $milestones): void
    {
        foreach ($milestones as $group) {
            $milestone = Milestone::firstOrCreate(['period' => $group['period']]);

            foreach ($group['logos'] as $i => $l) {
                $logoPath = $this->copyAsset("milestones/{$group['periodSlug']}", $l['file']);
                $milestone->logos()->updateOrCreate(
                    ['name' => $l['name']],
                    ['logo_path' => $logoPath, 'order' => $i],
                );
            }
        }
    }

    private function seedClients(array $clients): void
    {
        foreach ($clients as $group) {
            $category = ClientCategory::where('name_id', $group['category'])->first();

            if (! $category) {
                continue;
            }

            foreach ($group['clients'] as $i => $c) {
                $logoPath = $this->copyAsset("clients/{$group['categorySlug']}", $c['file']);
                $category->clients()->updateOrCreate(
                    ['name' => $c['name']],
                    ['logo_path' => $logoPath, 'order' => $i],
                );
            }
        }
    }
}
