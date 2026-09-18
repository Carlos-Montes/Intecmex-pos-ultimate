<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ProductsController extends Controller
{
    public function getProductos($status){ 
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
}



