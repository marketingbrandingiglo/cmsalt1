<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HeroSlide extends Model
{
    protected $fillable = [
        'tag_id',
        'tag_en',
        'title_id',
        'title_en',
        'description_id',
        'description_en',
        'image_path',
        'cta_label_id',
        'cta_label_en',
        'cta_url',
        'is_active',
        'order',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }
}
