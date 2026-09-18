<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Account extends Model
{
    protected $table = 'accounts';

    protected $fillable = ['account_number', 'name', 'balance', 'status'];

    protected $hidden = ['created_at', 'updated_at'];
}
