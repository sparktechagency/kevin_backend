<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
       protected $fillable = [
        'user_id',
        'subscription_id',
        'plan_id',
        'stripe_payment_intent_id',
        'stripe_invoice_id',
        'stripe_charge_id',
        'type',
        'status',
        'amount',
        'tax_amount',
        'discount_amount',
        'total_amount',
        'currency',
        'payment_method',
        'card_brand',
        'card_last_four',
        'billing_address',
        'paid_at',
        'refunded_at',
        'failed_at',
        'failure_reason'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'billing_address' => 'array',
        'paid_at' => 'datetime',
        'refunded_at' => 'datetime',
        'failed_at' => 'datetime'
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function subscription()
    {
        return $this->belongsTo(Subscription::class);
    }

    public function plan()
    {
        return $this->belongsTo(Plan::class);
    }

    // Scopes
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }

    public function scopeRefunded($query)
    {
        return $query->where('status', 'refunded');
    }

    public function scopeSubscription($query)
    {
        return $query->where('type', 'subscription');
    }

    public function scopeOneTime($query)
    {
        return $query->where('type', 'one_time');
    }

    public function scopeThisMonth($query)
    {
        return $query->whereYear('created_at', now()->year)
                    ->whereMonth('created_at', now()->month);
    }

    // Status check methods
    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isFailed(): bool
    {
        return $this->status === 'failed';
    }

    public function isRefunded(): bool
    {
        return $this->status === 'refunded';
    }

    // Helper methods
    public function markAsCompleted(): void
    {
        $this->update([
            'status' => 'completed',
            'paid_at' => now()
        ]);
    }

    public function markAsFailed(string $reason = null): void
    {
        $this->update([
            'status' => 'failed',
            'failed_at' => now(),
            'failure_reason' => $reason
        ]);
    }

    public function markAsRefunded(): void
    {
        $this->update([
            'status' => 'refunded',
            'refunded_at' => now()
        ]);
    }

    // Formatting methods
    public function getFormattedAmountAttribute(): string
    {
        return '$' . number_format($this->total_amount, 2);
    }

    public function getFormattedDateAttribute(): string
    {
        return $this->paid_at?->format('M j, Y') ?? $this->created_at->format('M j, Y');
    }

    public function getDescriptionAttribute(): string
    {
        if ($this->plan) {
            return $this->plan->name . ' Subscription';
        }

        return match($this->type) {
            'subscription' => 'Subscription Payment',
            'refund' => 'Payment Refund',
            'credit' => 'Account Credit',
            default => 'Payment'
        };
    }

}
