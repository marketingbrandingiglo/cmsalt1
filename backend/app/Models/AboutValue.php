<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AboutValue extends Model
{
    protected $fillable = ['about_us_id', 'title', 'description_id', 'description_en', 'order'];
}
