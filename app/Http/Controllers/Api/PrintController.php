<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Product;

use DNS1D;

class PrintController extends Controller
{
    public function getSearchProduct($code){
        $product = Product::where('code', $code)->first();
        return response()->json($product);
    }

    public function generateBarcode($code){
        $barcode = DNS1D::getBarcodeSVG($code, 'C128', 2, 45, 'black', true);
        return response($barcode)->header('Content-Type', 'image/svg+xml');
    }

    
}
