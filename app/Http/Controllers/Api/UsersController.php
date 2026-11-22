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


    public function postAddNewUser(Request $request){

        switch ($request->input('type_user')) {
            case '0':
                return $this->postUserAdministraitorAndVendor($request);
                break;
            case '1':
                return $this->postUserAdministraitorAndVendor($request);
                break;
            case '2':
                return $this->postUserCustomer($request);
                break;
            case '3':
                return $this->postUserSupplier($request);
                break;
        }

    }

    public function postUserAdministraitorAndVendor($request){
        $ac = $request->input('autocomplete');
     
        $rules = [
                'name_'.$ac => 'required',
                'password_'.$ac => 'required|min:8',
                'password_confirmation_'.$ac => 'required|min:8',
                'email_'.$ac => 'required|email|unique:users,email'
            ];

        $messages = [
            'name_'.$ac.'.required' => 'El nombre es requerido',
            'password_'.$ac.'.required' => 'La contraseña es requerida',
            'password_'.$ac.'.min' => 'La contraseña debe tener al menos 8 caracteres',
            'password_confirmation_'.$ac.'.required' => 'La confirmación de contraseña es requerida',
            'password_confirmation_'.$ac.'.min' => 'La confirmación de contraseña debe tener al menos 8 caracteres',
            'email_'.$ac.'.required' => 'El correo electrónico es requerido',
            'email_'.$ac.'.email' => 'Ingresa un correo electrónico válido',
            'email_'.$ac.'.unique' => 'Este correo electrónico ya se encuentra registrado en el sistema. Por favor, ingresa uno diferente.'
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            $data = ['type' => 'error', 'title' => 'Ha ocurrido un error.', 'msg' => 'Completa la información', 'msgs' => json_encode($validator->errors()->all())];
            return response()->json($data);
        }else{
            $u = new User;
            $u->status = 0;
            $u->role = $request->input('type_user');
            $u->name = $request->input('name_'.$ac);
            $u->email = $request->input('email_'.$ac);
            $u->phone = $request->input('phone_'.$ac);
            $u->password = Hash::make($request->input('password_'.$ac));
            $u->gender = $request->input('gender');
            if($u->save()):
                $actions = [
                    [
                        'url' => url('/users/list/all'),
                        'name' => 'Seguir',
                        'type' => 'primary sl',
                    ],
                ];
                $data = ['type' => 'success', 'title' => config('intecmex.app_name'), 'msg' => 'Se creo correctamente el usuario.', 'actions' => json_encode($actions), 'additional' => json_encode(['hideclose' => true])];
                return response()->json($data);
            endif;
        }
    }


    public function postUserCustomer($request){
        $ac = $request->input('autocomplete');
     
        $rules = [
                'customer_name_'.$ac => 'required',
                'customer_phone_'.$ac => 'required',
                'customer_email_'.$ac => 'required|email|unique:users,email',
                'customer_discount_'.$ac => 'required'
            ];

        $messages = [
            'customer_name_'.$ac.'.required' => 'El nombre es requerido',
            'customer_email_'.$ac.'.required' => 'El correo electrónico es requerido',
            'customer_email_'.$ac.'.email' => 'Ingresa un correo electrónico válido',
            'customer_email_'.$ac.'.unique' => 'Este correo electrónico ya se encuentra registrado en el sistema. Por favor, ingresa uno diferente.',
            'customer_phone_'.$ac.'.required' => 'El telefono es requerido',
            'customer_discount_'.$ac.'.required' => 'El descuento es requerido'
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            $data = ['type' => 'error', 'title' => 'Ha ocurrido un error.', 'msg' => 'Completa la información', 'msgs' => json_encode($validator->errors()->all())];
            return response()->json($data);
        }else{
            $u = new User;
            $u->status = 0;
            $u->role = $request->input('type_user');
            $u->name = $request->input('customer_name_'.$ac);
            $u->email = $request->input('customer_email_'.$ac);
            $u->phone = $request->input('customer_phone_'.$ac);
            $u->indiscount = $request->input('customer_discount_'.$ac);
            if($u->save()):
                 $actions = [
                    [
                        'url' => url('/users/list/all'),
                        'name' => 'Seguir',
                        'type' => 'primary sl',
                    ],
                ];
                $data = ['type' => 'success', 'title' => config('intecmex.app_name'), 'msg' => 'Se creo correctamente el usuario.', 'actions' => json_encode($actions), 'additional' => json_encode(['hideclose' => true])];
                return response()->json($data);
            endif;
        }
    }
    

    public function postUserSupplier($request){
        $ac = $request->input('autocomplete');
     
        $rules = [
                'supplier_name_'.$ac => 'required',
                'supplier_phone_'.$ac => 'required',
                'supplier_email_'.$ac => 'required|email|unique:users,email'
            ];

        $messages = [
            'supplier_name_'.$ac.'.required' => 'El nombre del proveedor es requerido',
            'supplier_email_'.$ac.'.required' => 'El correo electrónico del proveedor es requerido',
            'supplier_email_'.$ac.'.email' => 'Ingresa un correo electrónico válido',
            'supplier_email_'.$ac.'.unique' => 'Este correo electrónico ya se encuentra registrado en el sistema. Por favor, ingresa uno diferente.',
            'supplier_phone_'.$ac.'.required' => 'El telefono del proveedor es requerido',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            $data = ['type' => 'error', 'title' => 'Ha ocurrido un error.', 'msg' => 'Completa la información', 'msgs' => json_encode($validator->errors()->all())];
            return response()->json($data);
        }else{
            $u = new User;
            $u->status = 0;
            $u->role = $request->input('type_user');
            $u->name = $request->input('supplier_name_'.$ac);
            $u->email = $request->input('supplier_email_'.$ac);
            $u->phone = $request->input('supplier_phone_'.$ac);
            if($u->save()):
                 $actions = [
                    [
                        'url' => url('/users/list/all'),
                        'name' => 'Seguir',
                        'type' => 'primary sl',
                    ],
                ];
                $data = ['type' => 'success', 'title' => config('intecmex.app_name'), 'msg' => 'Se creo correctamente el usuario.', 'actions' => json_encode($actions), 'additional' => json_encode(['hideclose' => true])];
                return response()->json($data);
            endif;
        }
    }

}
