<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Account;

class FinancesController extends Controller
{
    public function getFinances($type){
        $accounts = Account::all();
        $data = ['type' => $type, 'accounts' => $accounts];
        return view('finances.home', $data);
    }
}
