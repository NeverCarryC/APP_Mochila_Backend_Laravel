<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TemplateCategoriesItems extends Model
{
    protected $fillable = ['backpack_id', 'name', 'description', 'user_id'];

    public function templateBackpack()
    {
        return $this->belongsTo(TemplateBackpack::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(TemplateItem::class);
    }
}
