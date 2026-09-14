<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\User;

use Validator, Hash;

class UsersController extends Controller{

    public function postUserEditData(Request $request, $id){
        $ac = $request->input('autocomplete');

       $rules = [
            'name_'.$ac => 'required',
            'phone_'.$ac => 'required',
        ];

       $messages = [
            'name_'.$ac.'.required' => 'El nombre es requerido',
            'phone_'.$ac.'.required' => 'El telefono es requerido'
        ];
        
        $validator = Validator::make($request->all(), $rules, $messages);

        if($validator->fails()):
            return back()->withErrors($validator)->with('message', 'Los sentimos, se ha producido un error.')->with('typealert', 'warning');
        else:
            $edit_user = User::find($id);
            $edit_user->name = e($request->input('name_'.$ac));
            $edit_user->phone = e($request->input('phone_'.$ac));
            $edit_user->gender = $request->input('gender');
            $edit_user->role = $request->input('role');

            if($edit_user->save()):
                return back()->with('message', 'Se actualizo correctamente la información del usuario.')->with('typealert', 'success');
            endif;
        endif;
    }
    

    public function postUserEditPassword(Request $request, $id) {
        $ac = $request->input('autocomplete');

        $rules = [
            'password_'.$ac => 'required|min:8',
            'cpassword_'.$ac => 'required|min:8'
        ];

        $messages = [
            'password_'.$ac.'.required' => 'La contraseña es requerida',
            'password_'.$ac.'.min' => 'La contraseña debe tener al menos 8 caracteres',
            'cpassword_'.$ac.'.required' => 'La confirmación de contraseña es requerida',
            'cpassword_'.$ac.'.min' => 'La confirmación de contraseña debe tener al menos 8 caracteres'
        ];

        $validator = Validator::make($request->all(), $rules, $messages);
        
        if($validator->fails()):
            return back()->withErrors($validator)->with('message', 'Los sentimos, se ha producido un error.')->with('typealert', 'warning');
        else:
            $user_p = User::find($id);
            if($request->input('password_'.$ac) && $request->input('cpassword_'.$ac)):
                if($request->input('password_'.$ac) == $request->input('cpassword_'.$ac)):
                    $user_p->password = Hash::make($request->input('password_'.$ac));
                else:
                    $data = ['type' => 'error', 'title' => 'Ha ocurrido un error.', 'msg' => 'Las contraseñas no coinciden'];
                    return response()->json($data);
                endif;
            endif;
            if($user_p->save()):
                return back()->with('message', 'Se actualizo correctamente la contraseña del usuario.')->with('typealert', 'success');
            endif;
        endif;
    }
    

    public function postUserEditPermissions(Request $request, $id){
        $user = User::find($id);
        $user->permissions = $request->except(['_token', 'id']);

        if($user->save()){
            return back()->with('message', 'Se actualizo correctamente los permisos del usuario.')->with('typealert', 'success');
        }
    }



    public function getUserInactive($id){
        $user = User::find($id);
        $user->status = 1;
        if($user->save()):
            return back()->with('message', 'Se elimino correctamente el usuario.')->with('typealert', 'success');
        endif;
    }


}
