<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Auth;

class TripCategory extends Model
{
    protected $fillable = ['name', 'description', 'user_id'];

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


    public function trips()
    {
        return $this->belongsToMany(Trip::class, 'trip_trip_category');
    }

    public function user()
    {
        return $this->belongTo(User::class);
    }
}
