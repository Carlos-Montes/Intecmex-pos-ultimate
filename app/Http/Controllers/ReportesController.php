<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ReportesController extends Controller
{
    public function getHome(){
        return view('reportes.home');
    }
}