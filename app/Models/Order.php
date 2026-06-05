<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    protected $fillable = [
        'user_id',
        'status',
        'total',
        'address_kh',
        'phone',
        'payment_status',
        'payment_proof',
        'approved_at',
    ];

    protected $casts = [
        'approved_at' => 'datetime',
        'total'       => 'decimal:2',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function statusBadgeClass(): string
    {
        return match ($this->status) {
            'approved'  => 'text-bg-success',
            'completed' => 'text-bg-primary',
            'cancelled' => 'text-bg-danger',
            default     => 'text-bg-warning',
        };
    }

    public function paymentBadgeClass(): string
    {
        return match ($this->payment_status) {
            'confirmed'      => 'text-bg-success',
            'proof_uploaded' => 'text-bg-info',
            default          => 'text-bg-secondary',
        };
    }

    public function paymentStatusLabel(): string
    {
        return match ($this->payment_status) {
            'proof_uploaded' => 'Proof Uploaded',
            'confirmed'      => 'Confirmed',
            default          => 'Unpaid',
        };
    }
}
