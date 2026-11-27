<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SettingsController extends Controller {
    
    public function __construct(){
        $this->middleware('user.status');
        $this->middleware('user.permissions');
    }


    public function getSettings(){
        return view('settings.settings');
    }

    
}
