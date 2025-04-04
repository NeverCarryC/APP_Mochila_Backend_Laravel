<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\Auth;


class Trip extends Model
{
    protected $fillable = [
        'url_photo',
        'start_date',
        'destination',
        'description',
        'name',
        'end_date',
    ];

    public static function boot()
    {
        parent::boot();

        // old code by Eric
        //    static::creating(function ($model)
        //    {
        //         $model->user_id = Auth::user()->id;

        //    });     

        static::creating(function ($model) {
            // Comprobar si hay un usuario autenticado, 
            // y si lo hay, establecer el user_id; 
            // de lo contrario, no hacer nada.
            if (Auth::check()) {
                $model->user_id = Auth::user()->id;
            }
        });
    }



    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    public function backpacks(): HasMany
    {
        return $this->hasMany(Backpack::class);
    }
    public function trip_categories(): BelongsToMany
    {
        return $this->belongsToMany(Trip_categories::class);
    }
}
