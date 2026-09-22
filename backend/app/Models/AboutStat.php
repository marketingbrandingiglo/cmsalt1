<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AboutStat extends Model
{
    protected $fillable = ['about_us_id', 'value', 'label_id', 'label_en', 'note_id', 'note_en', 'icon_path', 'order'];
}
