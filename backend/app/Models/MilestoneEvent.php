<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MilestoneEvent extends Model
{
    protected $fillable = ['milestone_id', 'text_id', 'text_en', 'description_id', 'description_en', 'order'];

    public function milestone(): BelongsTo
    {
        return $this->belongsTo(Milestone::class);
    }
}
