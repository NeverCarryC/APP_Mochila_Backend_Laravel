<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class TemplateBackpack extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'category'];

    public function items()
    {
        return $this->hasMany(TemplateItem::class);
    }
}
