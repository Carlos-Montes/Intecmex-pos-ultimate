<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Account;

use Validator;

class FinancesController extends Controller
{
    public function postAccountsAdd(Request $request){
        $rules = [
            'account_number' => 'required',
            'name' => 'required',
            'balance' => 'required',
        ];

        $messages = [
            'account_number.required' => 'El número de cuenta es requerido',
            'name.required' => 'El nombre es requerido',
            'balance.required' => 'El balance es requerido',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        if($validator->fails()){
            return response()->json(['type' => 'error', 'title' => 'Ha ocurrido un error.', 'msg' => 'Completa toda la información correctamente.', 'msgs' => json_encode($validator->errors()->all())]);
        }

        $account = new Account;
        $account->account_number = $request->input('account_number');
        $account->name = $request->input('name');
        $account->balance = $request->input('balance');
        $account->status = $request->input('status');

        if($account->save()){
            return response()->json(['type' => 'success', 'title' => config('intecmex.app_name'), 'msg' => 'Se creó correctamente la cuenta contable.']);
        }

        return response()->json(['type' => 'error', 'title' => 'Ha ocurrido un error.', 'msg' => 'No se pudo guardar la cuenta.']);
    }

    public function postAccountsEdit(Request $request, $id){
        $rules = [
            'account_number' => 'required',
            'name' => 'required',
            'balance' => 'required',
        ];

        $messages = [
            'account_number.required' => 'El número de cuenta es requerido',
            'name.required' => 'El nombre es requerido',
            'balance.required' => 'El balance es requerido',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        if($validator->fails()){
            return response()->json(['type' => 'error', 'title' => 'Ha ocurrido un error.', 'msg' => 'Completa toda la información correctamente.', 'msgs' => json_encode($validator->errors()->all())]);
        }

        $account = Account::find($id);
        $account->account_number = $request->input('account_number');
        $account->name = $request->input('name');
        $account->balance = $request->input('balance');
        $account->status = $request->input('status');

        if($account->save()){
            return response()->json(['type' => 'success', 'title' => config('intecmex.app_name'), 'msg' => 'Se actualizó correctamente la cuenta contable.']);
        }

        return response()->json(['type' => 'error', 'title' => 'Ha ocurrido un error.', 'msg' => 'No se pudo actualizar la cuenta.']);
    }

    public function getAccountsDelete($id){
        $account = Account::find($id);
        if($account->delete()){
            return back()->with('message', 'Se eliminó correctamente la cuenta contable.')->with('typealert', 'primary');
        }
    }
}
