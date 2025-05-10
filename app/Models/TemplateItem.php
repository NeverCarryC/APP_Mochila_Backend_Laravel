<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Auth ;

class TemplateItem extends Model
{
    use HasFactory;

    protected $fillable = ['template_backpack_id', 'name', 'quantity', 'category_id'];

    // public static function boot()
    // {
    //     parent::boot();

    //     static::creating(function ($model) {
    //         if (Auth::check()) {
    //             $model->user_id = Auth::user()->id;
    //         }
    //     });
    // }

    public function templateBackpack()
    {
        return $this->belongsTo(TemplateBackpack::class);
    }

    public function item_category(): BelongsTo
    {
        return $this->belongsTo(ItemCategory::class, 'category_id');
    }
}
