<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
class Dream extends Model
{
    protected $fillable = [
        'name',
        'category_id',
        'user_id',
        'description',
        'frequency',
        'start_date',
        'end_date',
        'from',
        'to',
        'per_week',
        'per_month',
        'status',
        'icon',
        'per_year',
        'per_quarter',
        'goal',
    ];
    public function user()
    {
        return $this->belongsTo(User::class);
    }
     public function category()
    {
        return $this->belongsTo(Category::class);
    }
    // In App\Models\Dream.php
    public function activities()
    {
        return $this->hasMany(DreamActivity::class, 'dream_id', 'id');
    }
     public function dreams(): HasMany
    {
        return $this->hasMany(Dream::class, 'user_id'); // make sure foreign key is correct
    }
}
