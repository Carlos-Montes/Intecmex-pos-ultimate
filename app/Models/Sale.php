<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Sale extends Model
{
    protected $table = 'sales';

    protected $fillable = [
        'folio',
        'user_id',
        'customer_id',
        'cash_register_session_id',
        'subtotal',
        'discount_amount',
        'tax_amount',
        'total',
        'payment_method',
        'cash_received',
        'change_amount',
        'status',
        'sold_at',
        'notes',
        'customer_discount_amount',
        'manual_discount_type',
        'manual_discount_value',
        'manual_discount_amount',
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'total' => 'decimal:2',
        'cash_received' => 'decimal:2',
        'change_amount' => 'decimal:2',
        'sold_at' => 'datetime',
        'customer_discount_amount' => 'decimal:2',
        'manual_discount_value' => 'decimal:2',
        'manual_discount_amount' => 'decimal:2',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    public function cashRegisterSession(): BelongsTo
    {
        return $this->belongsTo(
            CashRegisterSession::class,
            'cash_register_session_id'
        );
    }

    public function details(): HasMany
    {
        return $this->hasMany(SaleDetail::class);
    }
}
