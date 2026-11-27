<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Weight, App\Models\Product, App\Models\User;

class InventoryController extends Controller
{
    public function __construct(){
        $this->middleware('user.status');
        $this->middleware('user.permissions');
    }


    public function getInventory($status){
        switch ($status) {
            case 'live-weight':
                $pig = Weight::where('categorie', '0')->whereDate('created_at', now()->toDateString())->count();
                $cattle = Weight::where('categorie', '1')->whereDate('created_at', now()->toDateString())->count();
                $supplier = User::where('role', '3')->pluck('name', 'id');
                $data = ['count_pig' => $pig, 'count_cattle' => $cattle, 'supplier' => $supplier];
                return view('inventory.live_weight', $data);
                break;
            case '0':
                $data = ['type' => $status];
                return view('inventory.shrinkage_calculation', $data);
                break;
            case '1':
                $product = Product::with('meatInventory')->where('status', '1')->get();
                $data = ['type' => $status, 'product' => $product];
                return view('inventory.adjust_inventory', $data);
                break;
            default:
                # code...
                break;
        }
        
    }
}
