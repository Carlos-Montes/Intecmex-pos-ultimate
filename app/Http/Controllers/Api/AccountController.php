<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\User;

use Validator, Hash, Auth;

class AccountController extends Controller{


    public function postProfileUpdate(Request $request){
        $logout = false;
        $ac = $request->input('autocomplete');

        $rules = [
            'name_'.$ac => 'required',
            'phone_'.$ac => 'required',
        ];

        $messages = [
            'name_'.$ac.'.required' => 'Su nombre es requerido',
            'phone_'.$ac.'.required' => 'Su telefono es requerido',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            $data = ['type' => 'error', 'title' => 'Ha ocurrido un error.', 'msg' => 'Completa la información', 'msgs' => json_encode($validator->errors()->all())];

            return response()->json($data);
        } else {
            $user = User::find(Auth::id());
            $user->name = $request->input('name_'.$ac);
            $user->phone = $request->input('phone_'.$ac);
            $user->gender = $request->input('gender');

            if ($request->input('password_'.$ac) && $request->input('cpassword_'.$ac)) {
                if ($request->input('password_'.$ac) == $request->input('cpassword_'.$ac)) {
                    $user->password = Hash::make($request->input('password_'.$ac));
                    $logout = true;
                } else {
                    $data = ['type' => 'error', 'title' => 'Ha ocurrido un error.', 'msg' => 'Las contraseñas no coinciden'];

                    return response()->json($data);
                }
            }

            if ($request->hasFile('avatar')) {
                $user->avatar = $this->postFileUploadCdn('avatar', null, $request, [[64, 64, '64'], [256, 256, '256']]);
            }

            if ($user->save()) {
                if ($logout) {
                    Auth::logout();
                }
                $actions = [
                    [
                        'url' => url('/'),
                        'name' => 'Seguir',
                        'type' => 'primary sl',
                    ],
                ];

                $data = ['type' => 'success', 'title' => config('intecmex.app_name'), 'msg' => 'Información actualizada con éxito.', 'actions' => json_encode($actions), 'additional' => json_encode(['hideclose' => true])];

                return response()->json($data);
            }
        }
    }
}
