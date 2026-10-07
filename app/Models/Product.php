<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model{
    
	use SoftDeletes;

    protected $dates = ['deleted_at'];
	protected $table = 'products';
	protected $hidden = ['created_at', 'updated_at'];

    public function cat(){
		return $this->hasOne(Categorie::class, 'id', 'category_id')->withTrashed();
	}

	public function getSubcategory(){
		return $this->hasOne(Categorie::class, 'id', 'subcategory_id');
	}
}
