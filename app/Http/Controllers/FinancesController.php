<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Account;
use App\Models\Expense;

class FinancesController extends Controller
{
    public function getFinances($type){
        $accounts = Account::all();
        $expenses = Expense::all();

        $data = ['type' => $type, 'accounts' => $accounts, 'expenses' => $expenses];
        return view('finances.home', $data);
    }
}
