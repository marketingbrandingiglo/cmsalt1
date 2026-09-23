<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * The frontend's "feature callout" cards next to the stat counters use
     * one of two fixed built-in icons (a lightning bolt for "speed", a
     * stack for "layers") — never an arbitrary uploaded image. Swap the
     * unused icon upload for a select of those two icons instead, seeded
     * to match the fixed order the frontend already falls back to.
     */
    public function up(): void
    {
        Schema::table('about_stats', function (Blueprint $table) {
            $table->string('icon')->nullable()->after('note_en');
        });

        DB::table('about_stats')->orderBy('order')->get()->each(function ($stat, $i) {
            DB::table('about_stats')->where('id', $stat->id)->update([
                'icon' => ['speed', 'layers'][$i % 2],
            ]);
        });

        Schema::table('about_stats', function (Blueprint $table) {
            $table->dropColumn('icon_path');
        });
    }

    public function down(): void
    {
        Schema::table('about_stats', function (Blueprint $table) {
            $table->string('icon_path')->nullable()->after('note_en');
            $table->dropColumn('icon');
        });
    }
};
