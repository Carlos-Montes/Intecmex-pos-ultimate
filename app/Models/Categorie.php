<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Categorie extends Model{

	use SoftDeletes;

    protected $table = 'categories';
	protected $hidden = ['created_at', 'updated_at'];

    public function children(){
        return $this->hasMany(Categorie::class, 'parent_id');
    }
}
