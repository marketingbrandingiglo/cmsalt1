<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /** Optional longer description for a milestone event, in addition to its title. */
    public function up(): void
    {
        Schema::table('milestone_events', function (Blueprint $table) {
            $table->text('description_id')->nullable()->after('text_en');
            $table->text('description_en')->nullable()->after('description_id');
        });
    }

    public function down(): void
    {
        Schema::table('milestone_events', function (Blueprint $table) {
            $table->dropColumn(['description_id', 'description_en']);
        });
    }
};
