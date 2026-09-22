<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Vision & Mission summary stats (e.g. "More Than 50 Top Clients...")
     * shown on the About Us page.
     */
    public function up(): void
    {
        Schema::create('about_stats', function (Blueprint $table) {
            $table->id();
            $table->foreignId('about_us_id')->constrained('about_us')->cascadeOnDelete();
            $table->string('value'); // same across locales, e.g. "50", "1100"
            $table->string('label_id');
            $table->string('label_en');
            $table->string('note_id')->nullable();
            $table->string('note_en')->nullable();
            $table->string('icon_path')->nullable();
            $table->unsignedInteger('order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('about_stats');
    }
};
