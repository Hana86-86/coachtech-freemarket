<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;
use App\Http\Requests\ProductStoreRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\str;
use Illuminate\Support\Facades\Storage;


class ProductController extends Controller
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
        return view('products.index', compact('products'));
    }

    // 商品詳細
    public function show($id)
    {
        $product = Product::findOrFail($id); // 該当商品がなければエラー
        return view('products.show', compact('product'));

        $product = Product::with(['category','comments.user'])
            ->withCount(['comments','favorites'])
            ->findOrFail($id);
    }

    // 出品
    public function create()
    {
        $categories = Category::orderBy('name')->get(); // タグ風に並べる
        $conditions = ['新品・未使用','未使用に近い','目立った傷や汚れなし','やや傷や汚れあり','状態が悪い'];
        return view('products.create', compact('categories', 'conditions'));
    }
    public function store(ProductStoreRequest $request)
    {
        $validated = $request->validated();

        // 画像保存
        $path = null;
        if ($request->hasFile('image')) {
            $saved = $request->file('image')->store(
                'products/'.now()->format('Y/m'),
                'public'
                );
                $path = 'storage/' . $saved;
        }
        $product = Product::create([
            'user_id'     => auth()->id(),
            'title'       => $validated['title'],
            'brand'       => $validated['brand'] ?? null,
            'description' => $validated['description'],
            'category_id' => $validated['category_id'],
            'condition'   => $validated['condition'],
            'price'       => $validated['price'],
            'image_path'  => $path,
            'sale_status' => Product::SALE_STATUS_PUBLIC, //初期状態「公開中」
        ]);

        return redirect()->route('products.show', $product)->with('success','出品しました！');
    }



}
