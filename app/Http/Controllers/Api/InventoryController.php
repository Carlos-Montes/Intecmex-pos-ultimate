<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Weight;

use Validator, Auth;

class InventoryController extends Controller{

    public function posWeightAdd(Request $request){
        $ac = $request->input('autocomplete');

        $rules = [
            'weight_'.$ac => 'required'
        ];

        $messages = [
            'weight_'.$ac.'.required' => 'El peso es requerido',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            $data = ['type' => 'error', 'title' => 'Ha ocurrido un error.', 'msg' => 'Completa la información', 'msgs' => json_encode($validator->errors()->all())];
            return response()->json($data);
        }else{
            $w = new Weight;
            $w->user = Auth::user()->id;
            $w->categorie = $request->input('category');
            $w->weight = $request->input('weight_'.$ac);
            $w->registration_date = date('d-m-Y');
            if($w->save()){

                $actions = [
                    [
                        'url' => url('/inventory/live-weight'),
                        'name' => 'Seguir',
                        'type' => 'primary sl',
                    ],
                ];

                $data = ['type' => 'success', 'title' => config('intecmex.app_name'), 'msg' => 'Se registro correctamente el peso.', 'actions' => json_encode($actions), 'additional' => json_encode(['hideclose' => true])];

                return response()->json($data);
            }
        }
    }
}
