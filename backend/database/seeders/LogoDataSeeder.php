<?php

namespace Database\Seeders;

use App\Models\AboutUs;
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
     * to re-run: it only (re-)copies a file when the row doesn't already
     * point at one that actually exists on disk — so it self-heals if the
     * public disk was wiped (e.g. a redeploy with no persistent storage),
     * but never overwrites an image an editor has since uploaded.
     */
    public function run(): void
    {
        $manifest = json_decode(File::get(database_path('seed-assets/manifest.json')), true);

        $this->seedAboutMedia($manifest['about']);
        $this->seedPartners($manifest['partners']);
        $this->seedMilestoneLogos($manifest['milestones']);
        $this->seedClients($manifest['clients']);
    }

    /**
     * Banner image, the "i5" graphic beside Vision & Mission, and each
     * Value's icon — the exact images already used by the iglo frontend's
     * static About page.
     */
    private function seedAboutMedia(array $about): void
    {
        $aboutUs = AboutUs::singleton();
        $aboutUs->banner_image_path = $this->ensureAsset($aboutUs->banner_image_path, 'about-banner', $about['banner']);
        $aboutUs->description_image_path = $this->ensureAsset($aboutUs->description_image_path, 'about-description', $about['description']);
        $aboutUs->save();

        foreach ($about['values'] as $v) {
            $value = $aboutUs->values()->where('title', $v['title'])->first();

            if ($value) {
                $value->update(['image_path' => $this->ensureAsset($value->image_path, 'about-values', $v['file'])]);
            }
        }
    }

    /**
     * Returns $currentPath as-is if it already points at a file that
     * exists on the public disk; otherwise (re-)copies the seed asset and
     * returns its path.
     */
    private function ensureAsset(?string $currentPath, string $relativeDir, string $file): string
    {
        if (filled($currentPath) && Storage::disk('public')->exists($currentPath)) {
            return $currentPath;
        }

        $source = database_path("seed-assets/{$relativeDir}/{$file}");
        $destination = "{$relativeDir}/{$file}";
        Storage::disk('public')->put($destination, File::get($source));

        return $destination;
    }

    private function seedPartners(array $partners): void
    {
        foreach ($partners as $i => $p) {
            $partner = Partner::firstOrNew(['name' => $p['name']]);
            $partner->logo_path = $this->ensureAsset($partner->logo_path, 'partners', $p['file']);
            $partner->order = $i;
            $partner->save();
        }
    }

    private function seedMilestoneLogos(array $milestones): void
    {
        foreach ($milestones as $group) {
            $milestone = Milestone::firstOrCreate(['period' => $group['period']]);

            foreach ($group['logos'] as $i => $l) {
                $logo = $milestone->logos()->firstOrNew(['name' => $l['name']]);
                $logo->logo_path = $this->ensureAsset($logo->logo_path, "milestones/{$group['periodSlug']}", $l['file']);
                $logo->order = $i;
                $logo->save();
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
                $client = $category->clients()->firstOrNew(['name' => $c['name']]);
                $client->logo_path = $this->ensureAsset($client->logo_path, "clients/{$group['categorySlug']}", $c['file']);
                $client->order = $i;
                $client->save();
            }
        }
    }
}
