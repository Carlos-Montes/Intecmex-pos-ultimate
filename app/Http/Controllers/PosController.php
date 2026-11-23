<?php

namespace App\Http\Controllers;

use App\Models\CashRegisterSession;
use App\Models\Product;
use Illuminate\Support\Facades\DB;

class PosController extends Controller
{
    public function getHome()
    {
        $session = CashRegisterSession::with(['account', 'user'])
            ->where('user_id', auth()->id())
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
        $products = Product::with([
            'meatInventory:product_id,quantity_kg',
            'cat:id,name',
            'getSubcategory:id,name',
        ])
            ->where('status', 1)
            ->whereHas('cat', function ($query) {
                $query->where('status', 1)
                    ->whereNull('deleted_at');
            })
            ->whereHas('getSubcategory', function ($query) {
                $query->where('status', 1)
                    ->whereNull('deleted_at');
            })
            ->orderBy('name')
            ->get([
                'id',
                'code',
                'name',
                'price',
                'image',
                'category_id',
                'subcategory_id',
            ]);

        $products->transform(function ($product) {

            $product->image_url = ! empty($product->image)
                ? getFileUrl($product->image, '256')
                : null;

            $product->category_name = $product->cat->name ?? '';
            $product->subcategory_name = $product->getSubcategory->name ?? '';

            $product->quantity_kg = (float) (
                $product->meatInventory->quantity_kg ?? 0
            );

            unset(
                $product->image,
                $product->cat,
                $product->getSubcategory,
                $product->meatInventory
            );

            return $product;
        });

        return response()->json([
            'products' => $products,
        ]);
    }

    public function getCustomers()
    {
        $customers = DB::table('users')
            ->where('role', 2)
            ->orderBy('name')
            ->get([
                'id',
                'name',
                'email',
                'indiscount',
            ]);

        return response()->json([
            'customers' => $customers,
        ]);
    }
}
