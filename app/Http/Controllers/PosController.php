<?php

namespace App\Http\Controllers;

use App\Models\CashRegisterSession;
use Illuminate\Support\Facades\DB;

class PosController extends Controller
{
    public function getHome()
    {
        $session = CashRegisterSession::with(['account', 'user'])
            ->where('status', 'open')
            ->first();

        $categories = DB::table('categories')
            ->where('status', 1)
            ->whereNull('deleted_at')
            ->where(function ($query) {
                $query->whereNull('parent_id')
                    ->orWhere('parent_id', 0);
            })
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get(['id', 'name']);

        return view('pos.home', compact('session', 'categories'));
    }

    public function getProducts()
    {
        $products = DB::table('products as p')
            ->join('categories as c', 'c.id', '=', 'p.category_id')
            ->join('categories as sc', 'sc.id', '=', 'p.subcategory_id')
            ->where('p.status', 1)
            ->whereNull('p.deleted_at')
            ->where('c.status', 1)
            ->whereNull('c.deleted_at')
            ->where('sc.status', 1)
            ->whereNull('sc.deleted_at')
            ->select([
                'p.id',
                'p.code',
                'p.name',
                'p.price',
                'p.image',
                'p.category_id',
                'c.name as category_name',
                'p.subcategory_id',
                'sc.name as subcategory_name',
            ])
            ->orderBy('p.name')
            ->get();

        $products->transform(function ($product) {

            $product->image_url = ! empty($product->image)
                ? getFileUrl($product->image, '256')
                : null;

            unset($product->image);

            return $product;
        });

        return response()->json([
            'products' => $products,
        ]);
    }
}
