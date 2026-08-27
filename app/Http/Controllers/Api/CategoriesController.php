<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Categorie;

use Validator, Str;

class CategoriesController extends Controller
{
    public function postCategoriesAdd(Request $request){
        $ac = $request->input('autocomplete');
        $rules = [
            'name_'.$ac => 'required'
        ];

        $messages = [
            'name_'.$ac.'.required' => 'El nombre de la categoría es requerido',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            $data = ['type' => 'error', 'title' => 'Ha ocurrido un error.', 'msg' => 'Completa la información', 'msgs' => json_encode($validator->errors()->all())];

            return response()->json($data);
        }else{
            $categorie = new Categorie;
            $categorie->name = $request->input('name_'.$ac);
            $categorie->parent_id = $request->input('parent_id');
            $categorie->slug = Str::slug($request->input('name_'.$ac));
            $categorie->icon =  $this->postFileUploadCdn('icon', null, $request, [[64, 64, '64'], [256, 256, '256']]);
            if($categorie->save()){
                $actions = [
                    [
                        'url' => url('/categories/list'),
                        'name' => 'Seguir',
                        'type' => 'primary sl',
                    ],
                ];
                $data = ['type' => 'success', 'title' => config('intecmex.app_name'), 'msg' => 'Se creo correctamente la categoria.', 'actions' => json_encode($actions), 'additional' => json_encode(['hideclose' => true])];

                return response()->json($data);
            }
        }
    }

    public function postCategoriesEdit(Request $request, $id){
        $ac = $request->input('autocomplete');
        $rules = [
            'name_'.$ac => 'required'
        ];

        $messages = [
            'name_'.$ac.'.required' => 'El nombre de la categoría es requerido',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            return back()->withErrors($validator)->with('message', 'Los sentimos, se ha producido un error.')->with('typealert', 'warning');
        }else{
            $categorie = Categorie::find($id);
            $categorie->name = e($request->input('name_'.$ac));
            $categorie->slug = Str::slug($request->input('name_'.$ac));
            $categorie->status = $request->input('status');
            if($request->hasFile('icon')):
                $categorie->icon =  $this->postFileUploadCdn('icon', null, $request, [[64, 64, '64'], [256, 256, '256']]);
            endif;
            if($categorie->save()):
                return back()->with('message', 'Se actualizo correctamente la categoria.')->with('typealert', 'success');
            endif;
        }
    }

    public function getCategoriesDelete($id){
        $c = Categorie::find($id);
        if($c->delete()):
            return back()->with('message', 'Se elimino correctamente la categoría.')->with('typealert', 'primary');
        endif;
    }

    public function getSubcategoriesDelete($id){
        $sc = Categorie::find($id);
        if($sc->delete()):
            return back()->with('message', 'Se elimino correctamente la subcategoria')->with('typealert', 'primary');
        endif;
    }

}
