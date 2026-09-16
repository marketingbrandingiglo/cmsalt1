<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ClientCategory extends Model
{
    protected $fillable = ['name_id', 'name_en', 'order'];

    public function clients(): HasMany
    {
        return $this->hasMany(Client::class)->orderBy('order');
    }
}
