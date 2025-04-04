<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Item_category extends Model
{
    public function items(): hasMany
    {
        return $this->hasMany(Item::class);
    }
    
}
