<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Categorie extends Model{
    protected $table = 'categories';
	protected $hidden = ['created_at', 'updated_at'];

    public function children(){
        return $this->hasMany(Categorie::class, 'parent_id');
    }

}
