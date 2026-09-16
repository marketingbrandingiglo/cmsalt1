<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Single-row settings table for the About Us page (Deskripsi, Visi,
     * Misi). Always exactly one row (id = 1) — managed via a Filament
     * settings page, not a normal CRUD resource.
     */
    public function up(): void
    {
        Schema::create('about_us', function (Blueprint $table) {
            $table->id();
            $table->string('company_name')->nullable();
            $table->longText('description_id')->nullable();
            $table->longText('description_en')->nullable();
            $table->text('vision_id')->nullable();
            $table->text('vision_en')->nullable();
            $table->text('mission_id')->nullable();
            $table->text('mission_en')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('about_us');
    }
};
