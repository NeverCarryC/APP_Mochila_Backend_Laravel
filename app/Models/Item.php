<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Item extends Model
{
    public function item_category(): BelongsTo
    {
        return $this->belongsTo(Item_category::class);
    }
    public function backpack(): BelongsTo
    {
        return $this->belongsTo(Backpack::class);
    }
}
