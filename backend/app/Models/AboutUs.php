<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AboutUs extends Model
{
    protected $table = 'about_us';

    protected $fillable = [
        'company_name',
        'banner_image_path',
        'banner_title_id',
        'banner_title_en',
        'banner_description_id',
        'banner_description_en',
        'description_id',
        'description_en',
        'vision_id',
        'vision_en',
        'mission_id',
        'mission_en',
        'milestone_title_id',
        'milestone_title_en',
        'milestone_description_id',
        'milestone_description_en',
        'video_title_id',
        'video_title_en',
        'video_description_id',
        'video_description_en',
        'video_youtube_url',
    ];

    /** There is always exactly one row (id = 1). */
    public static function singleton(): self
    {
        return static::firstOrCreate(['id' => 1]);
    }

    public function values(): HasMany
    {
        return $this->hasMany(AboutValue::class)->orderBy('order');
    }

    public function stats(): HasMany
    {
        return $this->hasMany(AboutStat::class)->orderBy('order');
    }
}
