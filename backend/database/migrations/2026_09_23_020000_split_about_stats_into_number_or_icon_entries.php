<?php

use App\Models\AboutStat;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Each stat entry used to always produce two cards on the frontend (a
     * number+label counter, paired by index with an icon+text callout).
     * That pairing was arbitrary and made "Number label" a required field
     * even for icon-only cards. Flatten to one entry per card instead: an
     * entry has either a number or an icon (never required to have both),
     * and a single "text" caption (the existing note_id/note_en columns)
     * shown either way.
     *
     * Splits each existing entry into a number-only row (using its old
     * label as the text) and an icon-only row (keeping its old note),
     * so the frontend shows the exact same four cards afterwards.
     */
    public function up(): void
    {
        Schema::table('about_stats', function (Blueprint $table) {
            $table->string('value')->nullable()->change();
        });

        foreach (AboutStat::orderBy('order')->get() as $i => $stat) {
            $originalLabelId = $stat->label_id;
            $originalLabelEn = $stat->label_en;
            $originalNoteId = $stat->note_id;
            $originalNoteEn = $stat->note_en;
            $originalIcon = $stat->icon;

            $stat->update([
                'note_id' => $originalLabelId,
                'note_en' => $originalLabelEn,
                'icon' => null,
                'order' => $i,
            ]);

            AboutStat::create([
                'about_us_id' => $stat->about_us_id,
                'value' => null,
                'icon' => $originalIcon ?? ['speed', 'layers'][$i % 2],
                'note_id' => $originalNoteId,
                'note_en' => $originalNoteEn,
                'order' => 100 + $i,
            ]);
        }

        Schema::table('about_stats', function (Blueprint $table) {
            $table->dropColumn(['label_id', 'label_en']);
        });
    }

    public function down(): void
    {
        Schema::table('about_stats', function (Blueprint $table) {
            $table->string('label_id')->nullable()->after('value');
            $table->string('label_en')->nullable()->after('label_id');
        });
    }
};
