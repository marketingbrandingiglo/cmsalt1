<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /** Per-value illustration image (Value I5 cards). */
    public function up(): void
    {
        Schema::table('about_values', function (Blueprint $table) {
            $table->string('image_path')->nullable()->after('title');
        });
    }

    public function down(): void
    {
        Schema::table('about_values', function (Blueprint $table) {
            $table->dropColumn('image_path');
        });
    }
};
