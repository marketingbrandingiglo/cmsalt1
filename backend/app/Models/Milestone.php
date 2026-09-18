<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Milestone extends Model
{
    protected $fillable = ['period', 'order'];

    public function logos(): HasMany
    {
        return $this->hasMany(MilestoneLogo::class)->orderBy('order');
    }
}
