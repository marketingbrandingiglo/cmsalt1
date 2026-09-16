<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AboutUs extends Model
{
    protected $table = 'about_us';

    protected $fillable = [
        'company_name',
        'description_id',
        'description_en',
        'vision_id',
        'vision_en',
        'mission_id',
        'mission_en',
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
}
