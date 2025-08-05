<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;

class ItemController extends Controller
{
    // 商品一覧（検索付き）
    public function index(Request $request)
    {
        $query = Product::query();
        // 検索処理
        if ($request->filled('keyword')) {
            $query->where('title','like','%' . $request->keyword .'%')
                ->orWhere('description','like','%'. $request->keyword .'%');
        }

        $products = $query->get();
        return view('items.index', compact('products'));
    }

    // 商品詳細
    public function show($id)
    {
        $product = Product::findOrFail($id);
        return view('items.show', compact('product'));
    }


}
