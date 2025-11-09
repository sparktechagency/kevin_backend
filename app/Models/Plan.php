<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{
    protected $fillable = [
        'name',
        'stripe_price_id',
        'stripe_product_id',
        'price',
        'interval',
        'features',
        'is_active'
    ];

    protected $casts = [
        'features' => 'array',
        'is_active' => 'boolean',
        'price' => 'decimal:2'
    ];

    public function subscriptions()
    {
        return $this->hasMany(Subscription::class);
    }

    // Helper methods for your plans
    public function isStarter(): bool
    {
        return $this->name === 'Starter';
    }

    public function isBuilder(): bool
    {
        return $this->name === 'Builder';
    }

    public function isMaster(): bool
    {
        return $this->name === 'Master';
    }
}
