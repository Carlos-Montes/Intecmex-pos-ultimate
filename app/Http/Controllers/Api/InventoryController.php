<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Weight;
use Auth;
use Illuminate\Http\Request;
use Validator;

class InventoryController extends Controller
{
    public function posWeightAdd(Request $request)
    {
        $ac = $request->input('autocomplete');

        $rules = [
            'weight_'.$ac => 'required',
        ];

        $messages = [
            'weight_'.$ac.'.required' => 'El peso es requerido',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            $data = ['type' => 'error', 'title' => 'Ha ocurrido un error.', 'msg' => 'Completa la información', 'msgs' => json_encode($validator->errors()->all())];

            return response()->json($data);
        } else {
            $w = new Weight;
            $w->user = Auth::user()->id;
            $w->categorie = $request->input('category');
            $w->weight = $request->input('weight_'.$ac);
            $w->status = 0;
            $w->registration_date = now()->toDateString();
            if ($w->save()) {

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

    public function posWeightEdit(Request $request)
    {
        $rules = [
            'weight' => 'required',
        ];

        $messages = [
            'weight.required' => 'El peso es requerido',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            $data = ['type' => 'error', 'title' => 'Ha ocurrido un error.', 'msg' => 'Completa la información', 'msgs' => json_encode($validator->errors()->all())];

            return response()->json($data);
        } else {
            $w = Weight::findOrFail($request->id);
            $w->weight = $request->weight;
            if ($w->save()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Peso actualizado correctamente.',
                    'id' => $w->id,
                    'weight' => $w->weight,
                ]);
            }
        }
    }

    public function getSearchAnimalsDate(Request $request, $date)
    {
        $pig = Weight::where('categorie', '0')->where('status', 0)->where('registration_date', $date)->get();
        $cattle = Weight::where('categorie', '1')->where('status', 0)->where('registration_date', $date)->get();
        $data = ['pig' => $pig, 'cattle' => $cattle];

        return response()->json($data);
    }
}
