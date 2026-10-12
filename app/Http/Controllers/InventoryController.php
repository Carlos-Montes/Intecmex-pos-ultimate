<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Weight;

class InventoryController extends Controller
{
    public function getInventory($status){
        switch ($status) {
            case 'live-weight':
                $pig = Weight::where('categorie', '0')->whereDate('created_at', now()->toDateString())->count();
                $cattle = Weight::where('categorie', '1')->whereDate('created_at', now()->toDateString())->count();
                $data = ['count_pig' => $pig, 'count_cattle' => $cattle];
                return view('inventory.live_weight', $data);
                break;
            
            default:
                # code...
                break;
        }
        
    }
}
