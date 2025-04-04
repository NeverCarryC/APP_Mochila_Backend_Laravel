<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
class Trip_Category extends Model
{
    public function trip(): HasMany
    {
        return $this->hasMany(Trip::class);
    }
    
}
