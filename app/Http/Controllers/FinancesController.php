<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Account;
use App\Models\Expense;
use App\Models\User;

class FinancesController extends Controller
{

    public function __construct(){
        $this->middleware('user.status');
        $this->middleware('user.permissions');
    }
    
    public function getFinances($type){
        $accounts = Account::all();
        $expenses = Expense::all();
        $providers = User::where('role', '3')->pluck('name', 'id');
 
        $data = ['type' => $type, 'accounts' => $accounts, 'expenses' => $expenses, 'providers' => $providers];
        return view('finances.home', $data);
    }
}
