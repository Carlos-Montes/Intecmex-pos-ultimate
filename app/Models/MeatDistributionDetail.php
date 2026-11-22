<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MeatDistributionDetail extends Model
{
    protected $table = 'meat_distribution_details';

    protected $fillable = [
        'meat_distribution_id',
        'product_id',
        'quantity_kg',
    ];

    protected $casts = [
        'quantity_kg' => 'decimal:2',
    ];

    public function distribution()
    {
        return $this->belongsTo(
            MeatDistribution::class,
            'meat_distribution_id'
        );
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }
}
