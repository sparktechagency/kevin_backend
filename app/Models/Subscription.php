<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
{
   protected $fillable = [
        'user_id',
        'plan_id',
        'stripe_subscription_id',
        'status',
        'trial_ends_at',
        'ends_at'
    ];

    protected $casts = [
        'trial_ends_at' => 'datetime',
        'ends_at' => 'datetime'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function plan()
    {
        return $this->belongsTo(Plan::class);
    }

    public function active(): bool
    {
        return $this->status === 'active' || $this->status === 'trialing';
    }

    public function onTrial(): bool
    {
        return $this->trial_ends_at && $this->trial_ends_at->isFuture();
    }

    public function cancelled(): bool
    {
        return $this->ends_at && $this->ends_at->isPast();
    }

}
