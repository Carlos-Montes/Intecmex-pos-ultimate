<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\User;

class UsersController extends Controller
{
    public function getUsers($type){
        $users = User::when($type != 'all', function ($query) use ($type){
                                $query->where('role', $type);
                            })
                  ->paginate(10);
        $data = ['type' => $type, 'users' => $users];
        return view('users.home', $data);

    }

    public function getUsersViews($id){
        $user = User::find($id);
        $data = ['type' => $user->role, 'user' => $user];
        return view('users.users_view', $data);
    }

    public function getUsersPermissions($id){
        $user = User::find($id);
        $data = ['type' => $user->role, 'user' => $user];
        return view('users.users_permissions', $data);
    }

}
