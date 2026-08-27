<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Categorie;

class CategoriesController extends Controller{
    
    public function getCategories(){
        $subcategories = Categorie::where('parent_id', '=', 'id')->get();
        $listCategories = Categorie::where('parent_id', 0)->get();
        $data = ['cats' => $subcategories, 'listCategories' => $listCategories];
        return view('categories.home', $data);
    }

    public function getSubCategories($id){
        $subcategories = Categorie::where('parent_id', '=', $id)->get();
        $data = ['subcategories' => $subcategories];
        return view('categories.subcategories', $data);
    }
}
