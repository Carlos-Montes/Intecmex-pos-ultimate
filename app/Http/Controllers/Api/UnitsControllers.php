<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Unit;
use Validator;

class UnitsControllers extends Controller{

    public function postUnitsAdd(Request $request){
        $ac = $request->input('autocomplete');

        $rules = [
            'name_'.$ac => 'required',
            'nomenclatura_'.$ac => 'required'
        ];

        $messages = [
            'name_'.$ac.'.required' => 'El nombre de la unidad es requerida',
            'nomenclatura_'.$ac.'.required' => 'La nomenglatura de la unidad es requerida'
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            $data = ['type' => 'error', 'title' => 'Ha ocurrido un error.', 'msg' => 'Completa la información', 'msgs' => json_encode($validator->errors()->all())];
 
            return response()->json($data);
        }else{
            $uni = new Unit;
            $uni->name = e($request->input('name_'.$ac));
            $uni->nomenclatura = $request->input('nomenclatura_'.$ac);
            $uni->status = '1';
            if($uni->save()){
                $actions = [
                    [
                        'url' => url('/products/list/1'),
                        'name' => 'Seguir',
                        'type' => 'primary sl',
                    ],
                ];
                $data = ['type' => 'success', 'title' => config('intecmex.app_name'), 'msg' => 'Se creo correctamente la unidad.', 'actions' => json_encode($actions), 'additional' => json_encode(['hideclose' => true])];

                return response()->json($data);
            }
        }
    }

    public function postUnitsEdit(Request $request, $id){
        $ac = $request->input('autocomplete');

        $rules = [
            'name_'.$ac => 'required',
            'nomenclatura_'.$ac => 'required'
        ];

        $messages = [
            'name_'.$ac.'.required' => 'El nombre de la unidad es requerida',
            'nomenclatura_'.$ac.'.required' => 'La nomenglatura de la unidad es requerida'
        ];

         $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            return back()->withErrors($validator)->with('message', 'Los sentimos, se ha producido un error.')->with('typealert', 'warning');
        }else{
            $uni = Unit::find($id);
            $uni->name = e($request->input('name_'.$ac));
            $uni->nomenclatura = $request->input('nomenclatura_'.$ac);
            $uni->status = $request->input('status');
            if($uni->save()){
                return back()->with('message', 'Se actualizo correctamente la unidad.')->with('typealert', 'success');
            }
        }
    }


    public function postUnitsDelete($id){
        $u = Unit::find($id);
        if($u->delete()):
            return back()->with('message', 'Se elimino correctamente la unidad.')->with('typealert', 'primary');
        endif;
    }
}
