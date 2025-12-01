<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $fillable = [
        'name',
        'icon',
        'description'
    ];

    public function dreams()
    {
        return $this->hasMany(Dream::class, 'category_id', 'id');
    }
}
