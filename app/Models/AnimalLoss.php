<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AnimalLoss extends Model
{
    protected $table = 'animal_mermas';

    protected $hidden = ['created_at', 'updated_at'];

    protected function casts(): array
    {
        return [
            'live_weight_kg' => 'decimal:2',
            'merma_percentage' => 'decimal:2',
            'merma_weight' => 'decimal:2',
            'carcass_weight' => 'decimal:2',
        ];
    }

    public function liveWeight(): BelongsTo
    {
        return $this->belongsTo(
            LiveWeight::class,
            'live_weight_id'
        );
    }
}
