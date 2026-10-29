<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MeatDistribution extends Model
{
    protected $table = 'meat_distributions';

    protected $fillable = [
        'animal_merma_id',
        'user',
    ];

    public function animalMerma()
    {
        return $this->belongsTo(AnimalMerma::class, 'animal_merma_id');
    }

    public function details()
    {
        return $this->hasMany(
            MeatDistributionDetail::class,
            'meat_distribution_id'
        );
    }
}
