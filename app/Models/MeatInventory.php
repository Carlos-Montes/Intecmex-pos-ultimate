<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MeatInventory extends Model
{
    protected $table = 'meat_inventories';

    protected $fillable = [
        'product_id',
        'quantity_kg',
    ];

    protected $casts = [
        'quantity_kg' => 'decimal:2',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }
}
