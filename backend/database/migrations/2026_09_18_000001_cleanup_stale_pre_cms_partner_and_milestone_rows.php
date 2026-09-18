<?php

use App\Models\Milestone;
use App\Models\Partner;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * One-time cleanup of rows seeded before the real logo data was
     * migrated in: 15 old single-year Milestone rows (survived the
     * year->period rename with no logos attached) and Partner rows from
     * the very first PartnerSeeder run (sourced from milestone-timeline
     * names, not the actual "Our Partner" list) that never got a logo.
     * Only touches rows with no logo, so anything an editor has since
     * filled in via the admin panel is left alone.
     */
    public function up(): void
    {
        Milestone::doesntHave('logos')->delete();
        Partner::whereNull('logo_path')->delete();
    }

    public function down(): void
    {
        // Deleted rows were pre-CMS placeholder data with no logos and no
        // real content; not worth resurrecting.
    }
};
