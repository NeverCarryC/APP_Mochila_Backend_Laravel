<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Backpack extends Model
{
    public function items(): HasMany
    {
        return $this->hasMany(Item::class);
    }
    public function color(): HasOne
    {
        return $this->hasOne(Color::class);
    }
    public function trip(): BelongsTo
    {
        return $this->belongsTo(Trip::class);
    }

}
