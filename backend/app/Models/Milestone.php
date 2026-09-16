<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Milestone extends Model
{
    protected $fillable = ['year', 'order'];

    public function events(): HasMany
    {
        return $this->hasMany(MilestoneEvent::class)->orderBy('order');
    }
}
