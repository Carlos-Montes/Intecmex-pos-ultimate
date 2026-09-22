<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Product, App\Models\Categorie, App\Models\Unit;

class ProductsController extends Controller
{
    public function getProducts($status){ 
        switch ($status) {
            case '0':
                $products = Product::with(['cat', 'getSubcategory'])->where('status', '0')->orderBy('id', 'desc')->paginate(25);
                break;
            case '1':
                $products = Product::with(['cat', 'getSubcategory'])->where('status', '1')->orderBy('id', 'desc')->paginate(25);
                break;
            case 'all':
                $products = Product::with(['cat', 'getSubcategory'])->orderBy('id', 'desc')->paginate(25);
                break;
            case 'trash':
                $products = Product::with(['cat', 'getSubcategory'])->onlyTrashed()->orderBy('id', 'desc')->paginate(25);
                break;
        }
        $data = ['products' => $products];
    	return view('products.home', $data);
    }

    public function getProductsType($type){
        switch ($type) {
            case '1':
                $unit = Unit::get();
                $data = ['type' => $type, 'listUnits' => $unit];
                return view('units.home', $data);
                break;
            case '2':
                $data = ['type' => $type];
                return view('brands.home', $data);
                break;
            case '3':
                $data = ['type' => $type];
                return view('prints.home', $data);
                break;
        }
       
    }

    public function getProductsAdd(){
        $cats = Categorie::where('status', '1')->where('parent_id', '0')->pluck('name', 'id');
        $data = ['cats' => $cats];
        return view('products.add', $data);
    }

    public function getProductsEdit($id){
        $p = Product::findOrFail($id);
        $cats = Categorie::where('status', '1')->where('parent_id', '0')->pluck('name', 'id');
        $data = ['cats' => $cats, 'p' => $p];
        return view('products.edit', $data);
    }
}



