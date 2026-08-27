<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\User;

use Validator, Auth, Cookie, Mail, Hash, Str;

class ConnectController extends Controller{
    public function postLogin(Request $request){
        $ac = $request->input('autocomplete');

        $rules = [
            'email_'.$ac => 'required|email',
            'password_'.$ac => 'required|min:8'
        ];
        
        $messages = [
            'email_'.$ac.'.required' => 'El correo electrónico es requerido',
            'email_'.$ac.'.email' => 'El formato del correo electrónico es erroneo',
            'password_'.$ac.'.required' => 'La contraseña es requerida',
            'password_'.$ac.'.min' => 'La contraseña debe tener al menos 8 caracteres'
        ];

        $validator = Validator::make($request->all(), $rules, $messages);
        
        if($validator->fails()):
            $data = ['type' => 'error', 'title' => '¡Ha ocurrido un error!', 'msg' => 'Completa la información', 'msgs' => json_encode($validator->errors()->all())];
            return response()->json($data);
        else:
            if(Auth::attempt(['email' => $request->input('email_'.$ac), 'password' => $request->input('password_'.$ac)])):
                $data = ['type' => 'success'];
                return response()->json($data);
            else:
                $data = ['type' => 'error', 'title' => '¡Ha ocurrido un error!', 'msg' => 'Correo electrónico o contraseña incorrecta'];
                return response()->json($data);
            endif;
        endif;

    }
}
