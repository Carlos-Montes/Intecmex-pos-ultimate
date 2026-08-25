<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\User;

use Auth;

class AccountController extends Controller {

    public function getProfileEdit(){
        $user = User::find(Auth::id());
        $data = ['user' => $user];
        return view('account.profile_edit', $data);
    }
}
