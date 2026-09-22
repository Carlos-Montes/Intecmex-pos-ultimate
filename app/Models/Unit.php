<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Unit extends Model{
    protected $table = 'units';
	protected $hidden = ['created_at', 'updated_at'];
}
