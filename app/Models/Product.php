<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model{
    
    protected $dates = ['deleted_at'];
	protected $table = 'products';
	protected $hidden = ['created_at', 'updated_at'];

    public function cat(){
		return $this->hasOne(Categorie::class, 'id', 'category_id')->withTrashed();
	}

	public function getSubcategory(){
		return $this->hasOne(Categorie::class, 'parent_id', 'subcategory_id');
	}

	// public function getGallery(){
	// 	return $this->hasMany(PGallery::class, 'product_id', 'id');
	// }

	// public function getInventory(){
	// 	return $this->hasMany(Inventory::class, 'product_id', 'id')->orderBy('price', 'Asc');
	// }

	// public function getPrice(){
	// 	return $this->hasMany(Inventory::class, 'product_id', 'id');
	// }
}
