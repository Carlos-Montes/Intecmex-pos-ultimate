<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Brand;
use Validator;

class BrandsController extends Controller
{
    public function postBrandsAdd(Request $request){
        $ac = $request->input('autocomplete');

        $rules = [
            'name_'.$ac => 'required',
            'description_'.$ac => 'required'
        ];

        $messages = [
            'name_'.$ac.'.required' => 'El nombre de la marca es requerida',
            'description_'.$ac.'.required' => 'La descripcion de la marca es requerida'
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {

            $data = ['type' => 'error', 'title' => 'Ha ocurrido un error.', 'msg' => 'Completa la información', 'msgs' => json_encode($validator->errors()->all())];
            return response()->json($data);

        }else{
            $uni = new Brand;
            $uni->name = e($request->input('name_'.$ac));
            $uni->description = $request->input('description_'.$ac);
            $uni->status = '1';
            if($uni->save()){
                $actions = [
                    [
                        'url' => url('/products/list/2'),
                        'name' => 'Seguir',
                        'type' => 'primary sl',
                    ],
                ];
                $data = ['type' => 'success', 'title' => config('intecmex.app_name'), 'msg' => 'Se creo correctamente la marca.', 'actions' => json_encode($actions), 'additional' => json_encode(['hideclose' => true])];

                return response()->json($data);
            }
        }
    }

    public function postBrandsEdit(Request $request, $id){
        $ac = $request->input('autocomplete');

         $rules = [
            'name_'.$ac => 'required',
            'description_'.$ac => 'required'
        ];

        $messages = [
            'name_'.$ac.'.required' => 'El nombre de la marca es requerida',
            'description_'.$ac.'.required' => 'La descripcion de la marca es requerida'
        ];

         $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            return back()->withErrors($validator)->with('message', 'Los sentimos, se ha producido un error.')->with('typealert', 'warning');
        }else{
            $uni = Brand::find($id);
            $uni->name = e($request->input('name_'.$ac));
            $uni->description = $request->input('description_'.$ac);
            $uni->status = $request->input('status');
            if($uni->save()){
                return back()->with('message', 'Se actualizo correctamente la marca.')->with('typealert', 'success');
            }
        }
    }


    public function postBrandsDelete($id){
        $u = Brand::find($id);
        if($u->delete()):
            return back()->with('message', 'Se elimino correctamente la marca.')->with('typealert', 'primary');
        endif;
    }
}
