<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
{
    protected $fillable = [
        'name',
        'tagline',
        'price',
        'duration',
        'has_trial',
        'trial_days',
        'features',
    ];
    protected $casts = [
        'features' => 'array',
        'has_trial' => 'boolean',
        'price' => 'float',
    ];

}
