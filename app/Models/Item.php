<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Auth;

class Item extends Model
{

    protected $fillable = [
        'name',
        'item_category_id',
        'description',
        'user_id',
        'quantity',
        'is_checked',
    ];

    public static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->user_id = Auth::user()->id;
        });
    }



    public function itemCategory(): BelongsTo
    {
        return $this->belongsTo(ItemCategory::class, 'item_category_id', 'id');
    }

    public function backpack(): BelongsTo
    {
        return $this->belongsTo(Backpack::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
