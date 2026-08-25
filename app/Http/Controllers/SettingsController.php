<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SettingsController extends Controller {
    
    public function getSettings(){
        return view('settings.settings');
    }

    
}
