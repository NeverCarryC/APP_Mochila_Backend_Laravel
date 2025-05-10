<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Auth;

class ItemCategory extends Model
{
    protected $fillable = [
        'name',
        'description',
        'backpack_id',
        'user_id'
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

    public function items(): hasMany
    {
        return $this->hasMany(Item::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
