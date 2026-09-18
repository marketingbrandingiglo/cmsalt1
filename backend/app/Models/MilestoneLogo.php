<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MilestoneLogo extends Model
{
    protected $fillable = ['milestone_id', 'name', 'logo_path', 'order'];

    public function milestone(): BelongsTo
    {
        return $this->belongsTo(Milestone::class);
    }
}
