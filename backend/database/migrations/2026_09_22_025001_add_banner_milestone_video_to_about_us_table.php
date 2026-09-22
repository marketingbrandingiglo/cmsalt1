<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Adds the About page's Banner, Milestones-section (title/description),
     * and Company Video fields to the existing about_us singleton row.
     */
    public function up(): void
    {
        Schema::table('about_us', function (Blueprint $table) {
            $table->string('banner_image_path')->nullable()->after('company_name');
            $table->string('banner_title_id')->nullable()->after('banner_image_path');
            $table->string('banner_title_en')->nullable()->after('banner_title_id');
            $table->text('banner_description_id')->nullable()->after('banner_title_en');
            $table->text('banner_description_en')->nullable()->after('banner_description_id');

            $table->string('milestone_title_id')->nullable()->after('mission_en');
            $table->string('milestone_title_en')->nullable()->after('milestone_title_id');
            $table->text('milestone_description_id')->nullable()->after('milestone_title_en');
            $table->text('milestone_description_en')->nullable()->after('milestone_description_id');

            $table->string('video_title_id')->nullable()->after('milestone_description_en');
            $table->string('video_title_en')->nullable()->after('video_title_id');
            $table->text('video_description_id')->nullable()->after('video_title_en');
            $table->text('video_description_en')->nullable()->after('video_description_id');
            $table->string('video_youtube_url')->nullable()->after('video_description_en');
        });
    }

    public function down(): void
    {
        Schema::table('about_us', function (Blueprint $table) {
            $table->dropColumn([
                'banner_image_path',
                'banner_title_id',
                'banner_title_en',
                'banner_description_id',
                'banner_description_en',
                'milestone_title_id',
                'milestone_title_en',
                'milestone_description_id',
                'milestone_description_en',
                'video_title_id',
                'video_title_en',
                'video_description_id',
                'video_description_en',
                'video_youtube_url',
            ]);
        });
    }
};
