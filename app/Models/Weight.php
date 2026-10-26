<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Weight extends Model {

    protected $table = 'live_weight';
	protected $hidden = ['created_at', 'updated_at'];

    public function merma(): HasOne
    {
        return $this->hasOne(
            AnimalMerma::class,
            'live_weight_id'
        );
    }

}
