<?php

use App\Models\Milestone;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * One-time fix: the Milestones list should always show the newest
     * period first (2021 - Present at the top). The seeder already
     * assigns order this way for a fresh install, but a production row
     * created before that, or reordered since, may not match — force the
     * five known periods back to their intended order.
     */
    public function up(): void
    {
        $periodOrder = [
            '2021 - Present' => 5,
            '2016 - 2020' => 4,
            '2011 - 2015' => 3,
            '2006 - 2010' => 2,
            '2001 - 2005' => 1,
        ];

        foreach ($periodOrder as $period => $order) {
            Milestone::where('period', $period)->update(['order' => $order]);
        }
    }

    public function down(): void
    {
        // Order-only content fix; not worth reverting.
    }
};
