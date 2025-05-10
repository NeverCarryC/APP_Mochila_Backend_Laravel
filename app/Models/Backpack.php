<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\Auth;

class Backpack extends Model
{

    protected $fillable = [
        'id',
        'trip_id',
        'color_id',
        'name',
        'description',
    ];

    public static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            // Comprobar si hay un usuario autenticado, 
            // y si lo hay, establecer el user_id; 
            // de lo contrario, no hacer nada.
            // if (Auth::check()) {
            $model->user_id = Auth::user()->id;
            // }
        });
    }


    public function categories(): HasMany
    {
        return $this->hasMany(ItemCategory::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(Item::class);
    }

    public function color()
    {
        return $this->belongsTo(Color::class, 'color_id');
    }


    public function trip(): BelongsTo
    {
        return $this->belongsTo(Trip::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
