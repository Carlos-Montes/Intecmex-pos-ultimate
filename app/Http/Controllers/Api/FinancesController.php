<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Account;
use App\Models\Expense;
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

    public function postExpensesAdd(Request $request){
        $rules = [
            'account_id' => 'required',
            'amount' => 'required',
            'date' => 'required',
        ];

        $messages = [
            'account_id.required' => 'La cuenta contable es requerida',
            'amount.required' => 'El monto es requerido',
            'date.required' => 'La fecha es requerida',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        if($validator->fails()){
            return response()->json(['type' => 'error', 'title' => 'Ha ocurrido un error.', 'msg' => 'Completa toda la información correctamente.', 'msgs' => json_encode($validator->errors()->all())]);
        }

        $expense = new Expense;
        $expense->account_id = $request->input('account_id');
        $expense->supplier_id = $request->input('supplier_id');
        $expense->concept = $request->input('concept');
        $expense->amount = $request->input('amount');
        $expense->date = $request->input('date');
        $expense->observations = $request->input('observations');

        if($expense->save()){
            $account = Account::find($expense->account_id);
            if($account){
                $account->balance -= $expense->amount;
                $account->save();
            }
            return response()->json(['type' => 'success', 'title' => config('intecmex.app_name'), 'msg' => 'Se guardó correctamente el gasto.']);
        }

        return response()->json(['type' => 'error', 'title' => 'Ha ocurrido un error.', 'msg' => 'No se pudo guardar el gasto.']);
    }

    public function postExpensesEdit($id, Request $request){
        $validator = Validator::make($request->all(), [
            'account_id' => 'required',
            'amount' => 'required',
            'date' => 'required',
        ]);

        if($validator->fails()){
            return response()->json(['type' => 'error', 'title' => 'Error', 'msg' => 'Completa la información requerida.']);
        }

        $expense = Expense::find($id);
        if(!$expense){
            return response()->json(['type' => 'error', 'title' => 'Error', 'msg' => 'Gasto no encontrado.']);
        }

        $oldAccount = Account::find($expense->account_id);
        if($oldAccount){
            $oldAccount->balance += $expense->amount; 
            $oldAccount->save();
        }

        $expense->account_id = $request->input('account_id');
        $expense->supplier_id = $request->input('supplier_id');
        $expense->concept = $request->input('concept');
        $expense->amount = $request->input('amount');
        $expense->date = $request->input('date');
        $expense->observations = $request->input('observations');

        if($expense->save()){
            $account = Account::find($expense->account_id);
            if($account){
                $account->balance = $account->balance - $expense->amount;
                $account->save();
            }
            return response()->json(['type' => 'success', 'title' => config('intecmex.app_name'), 'msg' => 'Se guardó correctamente el gasto.']);
        }
    }

    public function getExpensesDelete($id){
        $expense = Expense::find($id);
        if($expense){
            $account = Account::find($expense->account_id);
            if($account){
                $account->balance += $expense->amount;
                $account->save();
            }

            $expense->delete();
        }
        return back();
    }
}
