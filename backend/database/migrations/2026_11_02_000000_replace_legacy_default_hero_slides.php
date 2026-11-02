<?php

use App\Models\HeroSlide;
use Database\Seeders\HomeSeeder;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /** Titles of the 3 placeholder slides the first Home seed created. */
    private const LEGACY_TITLES_EN = [
        'AI Skills Shortage Becomes a New Challenge for the Financial Industry',
        'How to Accelerate Your Business System',
        'Why AI Performs Better with SAP GROW Than Disconnected Systems',
    ];

    /**
     * The live frontend's hero moved on to 4 photo slides before the CMS
     * went live. Swap the old seeded placeholders for those — but only if
     * the slides are still exactly the untouched placeholders, so nothing
     * an editor changed is ever overwritten. Fresh databases are seeded
     * with the new slides directly (HomeSeeder).
     */
    public function up(): void
    {
        $slides = HeroSlide::query()->orderBy('order')->get();

        $untouchedLegacy = $slides->count() === count(self::LEGACY_TITLES_EN)
            && $slides->pluck('title_en')->all() === self::LEGACY_TITLES_EN
            && $slides->every(fn (HeroSlide $s) => blank($s->image_path));

        if (! $untouchedLegacy) {
            return;
        }

        HeroSlide::query()->delete();
        HomeSeeder::createDefaultHeroSlides();
    }

    public function down(): void
    {
        //
    }
};
