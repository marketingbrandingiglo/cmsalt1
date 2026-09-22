<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AboutValue extends Model
{
    protected $fillable = ['about_us_id', 'title', 'image_path', 'description_id', 'description_en', 'order'];

    protected static function booted(): void
    {
        static::creating(function (self $value) {
            $value->about_us_id ??= AboutUs::singleton()->id;
        });
    }
}
