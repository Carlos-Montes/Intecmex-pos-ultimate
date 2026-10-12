<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Weight extends Model {

    protected $table = 'live_weight';
	protected $hidden = ['created_at', 'updated_at'];

}
