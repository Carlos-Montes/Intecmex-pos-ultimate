<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Categorie;
use App\Models\Product;
use Illuminate\Http\Request;
use Str;
use Validator;

class ProductController extends Controller
{
    public function getSubCategories($parent)
    {
        $categories = Categorie::where('parent_id', $parent)->get();

        return response()->json($categories);
    }

    public function postProductAdd(Request $request){
        $ac = $request->input('autocomplete');

        $rules = [
            'name_'.$ac => 'required',
            'code_'.$ac => 'required',
            'icon' => 'required|image',
        ];

        $messages = [
            'name_'.$ac.'.required' => 'El nombre del producto es requerido',
            'code_'.$ac.'.required' => 'El código del producto es requerido',
            'icon.required' => 'Seleccione una imagen destacada',
            'icon.image' => 'El archivo no es una imagen',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            return back()->withErrors($validator)->with('message', 'Se ha producido un error.')->with('typealert', 'danger')->withInput();
        }

        $product = new Product;
        $product->status = '0';
        $product->code = e($request->input('code_'.$ac));
        $product->name = e($request->input('name_'.$ac));
        $product->slug = Str::slug($request->input('name_'.$ac));
        $product->category_id = $request->input('category');
        $product->subcategory_id = $request->input('subcategory');
        $product->image = $this->postFileUploadCdn('icon',null,$request,[[64, 64, '64'],[256, 256, '256']]);
        $product->in_discount = $request->input('indiscount');
        $product->discount = $request->input('discount_'.$ac);
        $product->content = e($request->input('content_'.$ac));

        if ($product->save()) {
            return redirect('/product/'.$product->id.'/edit')->with('message', 'Guardado con éxito.')->with('typealert', 'success');
        }
    }
}
