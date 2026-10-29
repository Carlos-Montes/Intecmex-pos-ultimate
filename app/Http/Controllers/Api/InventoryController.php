<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AnimalLoss;
use App\Models\Weight;
use Auth;
use DB;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
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
            'merma_percentage' => 'required|numeric|between:0,100',
        ];

        $messages = [
            'live_weight_id.required' => 'Selecciona un animal.',
            'live_weight_id.exists' => 'El animal seleccionado no existe.',
            'merma_percentage.required' => 'Ingresa el porcentaje de merma.',
            'merma_percentage.numeric' => 'El porcentaje debe ser numérico.',
            'merma_percentage.between' => 'El porcentaje debe estar entre 0 y 100.',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            $data = ['type' => 'error', 'title' => 'Ha ocurrido un error.', 'msg' => 'Completa la información correctamente.', 'msgs' => json_encode($validator->errors()->all())];

            return response()->json($data);

        } else {
            // 1. Buscamos el registro del peso vivo.
            $animal = Weight::find($request->live_weight_id);

            if (! $animal || ! is_numeric($animal->weight) || $animal->weight <= 0) {
                return response()->json(['type' => 'error', 'title' => 'Ha ocurrido un error.', 'msg' => 'El peso vivo del animal no es válido.']);
            }

            // 2. Comprobamos que no tenga una merma registrada.
            $mermaExistente = AnimalLoss::where('live_weight_id', $animal->id)->exists();

            if ($mermaExistente) {
                return response()->json(['type' => 'error', 'title' => 'Registro duplicado.', 'msg' => 'Este animal ya tiene una merma registrada.']);
            }

            // 3. Calculamos los pesos.
            $pesoVivo = (float) $animal->weight;
            $porcentaje = (float) $request->merma_percentage;

            $pesoMerma = round($pesoVivo * $porcentaje / 100, 2);
            $pesoCanal = round($pesoVivo - $pesoMerma, 2);

            // 4. Guardamos la información.
            $merma = new AnimalLoss;
            $merma->live_weight_id = $animal->id;
            $merma->live_weight_kg = $pesoVivo;
            $merma->merma_percentage = $porcentaje;
            $merma->merma_weight = $pesoMerma;
            $merma->carcass_weight = $pesoCanal;

            if ($merma->save()) {
                $loss = Weight::findOrFAil($animal->id);
                $loss->status = 1;
                $loss->save();
                // 6. Respondemos al JavaScript.
                $actions = ['url' => url('/inventory/live-weight'), 'name' => 'Seguir', 'type' => 'primary sl'];
                $data = ['type' => 'success', 'title' => config('intecmex.app_name'), 'msg' => 'Se registro correctamente el peso.', 'actions' => json_encode($actions), 'additional' => json_encode(['hideclose' => true])];

                return response()->json($data);
            }

        }
    }

    public function getSearchAnimalsWeight(Request $request, $type)
    {
        // Total de kilos de canal para Cerdo (0) o Res (1).
        $canal = DB::table('animal_mermas as am')
            ->join('live_weight as lw', 'lw.id', '=', 'am.live_weight_id')
            ->where('lw.categorie', $type)
            ->selectRaw('COALESCE(SUM(am.carcass_weight), 0) as kilos_canal')
            ->first();

        // Historial de kilos distribuidos por categoría.
        $distribuidos = DB::table('meat_distributions as md')
            ->join(
                'meat_distribution_details as mdd',
                'mdd.meat_distribution_id',
                '=',
                'md.id'
            )
            ->where('md.category', $type)
            ->selectRaw('COALESCE(SUM(mdd.quantity_kg), 0) as kilos_distribuidos')
            ->first();

        $kilos_canal = (float) $canal->kilos_canal;
        $kilos_distribuidos = (float) $distribuidos->kilos_distribuidos;

        return response()->json([
            'category' => (int) $type,
            'kilos_canal' => $kilos_canal,
            'kilos_distribuidos' => $kilos_distribuidos,
            'kilos_restantes' => $kilos_canal - $kilos_distribuidos,
        ]);
    }

    public function postWeightAnimals(Request $request)
    {
        $validated = $request->validate([
            'category' => 'required|integer|in:0,1',
            'products' => 'required|array|min:1',
            'products.*.product_id' => 'required|integer|distinct|exists:products,id',
            'products.*.quantity_kg' => 'required|numeric|gt:0',
        ]);

        try {
            $result = DB::transaction(function () use ($validated) {

                $category = (int) $validated['category'];

                // 1. Obtener los kilos totales de canal de la categoría.
                $canal = DB::table('animal_mermas as am')
                    ->join('live_weight as lw', 'lw.id', '=', 'am.live_weight_id')
                    ->where('lw.categorie', $category)
                    ->sum('am.carcass_weight');

                // 2. Obtener los kilos distribuidos anteriormente.
                $distribuido = DB::table('meat_distributions as md')
                    ->join(
                        'meat_distribution_details as mdd',
                        'mdd.meat_distribution_id',
                        '=',
                        'md.id'
                    )
                    ->where('md.category', $category)
                    ->sum('mdd.quantity_kg');

                $totalCanal = (float) $canal;
                $totalAnterior = (float) $distribuido;

                // 3. Calcular los kilos que se van a asignar.
                $totalAsignado = collect($validated['products'])
                    ->sum(fn ($product) => (float) $product['quantity_kg']);

                $disponible = $totalCanal - $totalAnterior;

                // 4. Evitar distribuir más kilos de los disponibles.
                if ($totalAsignado > $disponible + 0.000001) {
                    throw ValidationException::withMessages([
                        'products' => [
                            'La distribución supera los kilos disponibles. Saldo actual: '
                            .number_format(max(0, $disponible), 2).' kg.',
                        ],
                    ]);
                }

                // 5. Crear el encabezado de la distribución.
                $distributionId = DB::table('meat_distributions')->insertGetId([
                    'category' => $category,
                    'user' => auth()->id(),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                // 6. Guardar cada detalle y actualizar el inventario.
                foreach ($validated['products'] as $product) {

                    $productId = (int) $product['product_id'];
                    $quantity = (float) $product['quantity_kg'];

                    // Registrar el detalle de la distribución.
                    DB::table('meat_distribution_details')->insert([
                        'meat_distribution_id' => $distributionId,
                        'product_id' => $productId,
                        'quantity_kg' => $quantity,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);

                    // Buscar el inventario del producto.
                    $inventory = DB::table('meat_inventories')
                        ->where('product_id', $productId)
                        ->lockForUpdate()
                        ->first();

                    if ($inventory) {
                        // Si ya existe, acumular los nuevos kilos.
                        DB::table('meat_inventories')
                            ->where('product_id', $productId)
                            ->update([
                                'quantity_kg' => (float) $inventory->quantity_kg + $quantity,
                                'updated_at' => now(),
                            ]);
                    } else {
                        // Si no existe, crear el inventario inicial.
                        DB::table('meat_inventories')->insert([
                            'product_id' => $productId,
                            'quantity_kg' => $quantity,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                    }
                }

                // 7. Devolver el resumen actualizado.
                return [
                    'distribution_id' => $distributionId,
                    'channel_weight' => $totalCanal,
                    'distributed_before' => $totalAnterior,
                    'total_assigned' => $totalAsignado,
                    'distributed_after' => $totalAnterior + $totalAsignado,
                    'available_after' => max(0, $disponible - $totalAsignado),
                ];
            });

            return response()->json([
                'success' => true,
                'message' => 'Distribución guardada y el inventario actualizado correctamente.',
                'data' => $result,
            ]);

        } catch (ValidationException $e) {
            throw $e;
        } catch (\Throwable $e) {
            report($e);

            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'error' => class_basename($e),
                'file' => basename($e->getFile()),
                'line' => $e->getLine(),
            ], 500);
        }
    }
}
