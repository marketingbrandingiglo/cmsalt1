<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * The About page redesign groups milestones into a handful of periods,
     * each showing the partner/client logos gained in that period — replacing
     * the old one-year-per-row list of text events.
     */
    public function up(): void
    {
        Schema::dropIfExists('milestone_events');

        Schema::table('milestones', function (Blueprint $table) {
            $table->renameColumn('year', 'period');
        });

        Schema::create('milestone_logos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('milestone_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('logo_path')->nullable();
            $table->unsignedInteger('order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('milestone_logos');

        Schema::table('milestones', function (Blueprint $table) {
            $table->renameColumn('period', 'year');
        });

        Schema::create('milestone_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('milestone_id')->constrained()->cascadeOnDelete();
            $table->string('text_id');
            $table->string('text_en');
            $table->unsignedInteger('order')->default(0);
            $table->timestamps();
        });
    }
};
