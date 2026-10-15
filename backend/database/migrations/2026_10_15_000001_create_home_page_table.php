<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Single-row settings table for the Home page section copy (Intro,
     * Products section heading, News section heading). Always exactly one
     * row (id = 1) — managed via a Filament settings page, not a CRUD
     * resource. Hero slides live in their own table.
     */
    public function up(): void
    {
        Schema::create('home_page', function (Blueprint $table) {
            $table->id();
            $table->string('intro_kicker_id')->nullable();
            $table->string('intro_kicker_en')->nullable();
            $table->text('intro_title_id')->nullable();
            $table->text('intro_title_en')->nullable();
            $table->text('intro_description_id')->nullable();
            $table->text('intro_description_en')->nullable();
            $table->string('intro_cta_label_id')->nullable();
            $table->string('intro_cta_label_en')->nullable();
            $table->string('intro_cta_url')->nullable();
            $table->string('products_kicker_id')->nullable();
            $table->string('products_kicker_en')->nullable();
            $table->text('products_title_id')->nullable();
            $table->text('products_title_en')->nullable();
            $table->text('products_description_id')->nullable();
            $table->text('products_description_en')->nullable();
            $table->string('news_kicker_id')->nullable();
            $table->string('news_kicker_en')->nullable();
            $table->text('news_title_id')->nullable();
            $table->text('news_title_en')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('home_page');
    }
};
