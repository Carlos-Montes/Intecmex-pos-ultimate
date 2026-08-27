<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ConnectController extends Controller{
    
    public function getLogin(Request $request){
        return view('connect.login');
    }

    public function getLogout(Request $request){
        auth()->logout();
        return redirect()->route('connect.login');
    }

}
