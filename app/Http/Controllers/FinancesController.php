<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class FinancesController extends Controller
{
    public function getFinances($type){
        $data = ['type' => $type];
        return view('finances.home', $data);
    }
}
