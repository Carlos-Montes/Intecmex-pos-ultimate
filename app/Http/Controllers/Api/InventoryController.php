<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Weight, App\Models\AnimalLoss;
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


    public function postAnimalMerma(Request $request)
    {
        $rules = [
            'live_weight_id' => 'required|integer|min:1|exists:live_weight,id',
            'merma_percentage' => 'required|numeric|between:0,100'
        ];

        $messages = [
            'live_weight_id.required' => 'Selecciona un animal.',
            'live_weight_id.exists' => 'El animal seleccionado no existe.',
            'merma_percentage.required' => 'Ingresa el porcentaje de merma.',
            'merma_percentage.numeric' => 'El porcentaje debe ser numérico.',
            'merma_percentage.between' => 'El porcentaje debe estar entre 0 y 100.'
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            $data = [ 'type' => 'error', 'title' => 'Ha ocurrido un error.', 'msg' => 'Completa la información correctamente.', 'msgs' => json_encode($validator->errors()->all()) ];
            return response()->json($data);

        } else {
            // 1. Buscamos el registro del peso vivo.
            $animal = Weight::find($request->live_weight_id);

            if (!$animal || !is_numeric($animal->weight) || $animal->weight <= 0) {
                return response()->json([ 'type' => 'error', 'title' => 'Ha ocurrido un error.', 'msg' => 'El peso vivo del animal no es válido.' ]);
            }

            // 2. Comprobamos que no tenga una merma registrada.
            $mermaExistente = AnimalLoss::where( 'live_weight_id', $animal->id )->exists();

            if ($mermaExistente) {
                return response()->json([ 'type' => 'error', 'title' => 'Registro duplicado.', 'msg' => 'Este animal ya tiene una merma registrada.' ]);
            }

            // 3. Calculamos los pesos.
            $pesoVivo = (float) $animal->weight;
            $porcentaje = (float) $request->merma_percentage;

            $pesoMerma = round($pesoVivo * $porcentaje / 100, 2);
            $pesoCanal = round($pesoVivo - $pesoMerma, 2);

            // 4. Guardamos la información.
            $merma = new AnimalLoss();
            $merma->live_weight_id = $animal->id;
            $merma->live_weight_kg = $pesoVivo;
            $merma->merma_percentage = $porcentaje;
            $merma->merma_weight = $pesoMerma;
            $merma->carcass_weight = $pesoCanal;
            
            

            if($merma->save()):
                $loss = Weight::findOrFAil($animal->id);
                $loss->status = 1;
                $loss->save();
                // 6. Respondemos al JavaScript.
                $actions = [ 'url' => url('/inventory/live-weight'), 'name' => 'Seguir', 'type' => 'primary sl'];
                $data = ['type' => 'success', 'title' => config('intecmex.app_name'), 'msg' => 'Se registro correctamente el peso.', 'actions' => json_encode($actions), 'additional' => json_encode(['hideclose' => true])];
                return response()->json($data);
            endif;

        }
    }
}
