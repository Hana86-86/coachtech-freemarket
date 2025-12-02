<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductStoreRequest;
use App\Http\Requests\ProductUpdateRequest;
use App\Models\Category;
use App\Models\Product;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProductController extends Controller
{
    // 商品一覧（おすすめ / マイリスト + キーワード検索）
    public function index(Request $request)
    {
        $tab   = $request->query('tab', 'all');
        $filters = $request->only(['keyword', 'category_id', 'condition', 'price_min', 'price_max', 'sale_status']);

        $query = Product::query()
            ->with('categories')
            ->whereIn('sale_status', [Product::SALE_STATUS_PUBLIC, Product::SALE_STATUS_SOLD]);

        // マイリスト（認証ユーザーのみ表示）
        if ($tab === 'mylist') {
            if (auth()->check()) {
                $query->whereHas('favorites', fn($query) => $query->where('user_id', auth()->id()));
            } else {
                $query->whereRaw('1=0');
            }
        }
        // 自分の商品は一覧から除外
        if (auth()->check()) {
            $query->where('user_id', '!=', auth()->id());
        }
        // タブ：おすすめ(=通常一覧)
        $products = $query
            ->with(['categories'])
            ->withCount(['favorites', 'comments'])
            ->latest()
            ->paginate(12)
            ->withQueryString();

        // キーワード
        if ($request->filled('keyword')) {
            $keyword = $request->input('keyword');

            $query->where(function ($query) use ($keyword) {
                $query->where('title', 'like', "%{$keyword}%")
                    ->orWhere('description', 'like', "%{$keyword}%")
                    ->orWhere('brand', 'like', "%{$keyword}%")
                    ->orWhereHas('categories', function ($query) use ($keyword) {
                        $query->where('name', 'like', "%{$keyword}%");
                    })
                    ->orWhereHas('user', function ($query) use ($keyword) {
                        $query->where('name', 'like', "%{$keyword}%");
                    });
            });
        }
        $query->filter($filters);

        $products = $query->paginate(12)->withQueryString();

        return view('products.index', compact('products', 'tab', 'filters'));
    }
    // 商品詳細
    public function show(Request $request, Product $product)
    {
        $product->loadCount(['favorites', 'comments'])
            ->load(['categories', 'comments.user', 'user']);

        $isFavorited = false;
        if (auth()->check()) {
            $isFavorited = $product->favorites()
                ->where('user_id', auth()->id())
                ->exists();
        }

        $isOwner = auth()->check() && $product->user_id === auth()->id();

        return view('products.show', compact('product', 'isOwner', 'isFavorited'));;
    }
    // 出品編集フォーム
    public function edit(Product $product)
    {
        $this->authorize('update', $product);

        $categories = Category::orderBy('name')->get();
        $conditions = ['新品・未使用', '未使用に近い', '目立った傷や汚れなし', 'やや傷や汚れあり', '状態が悪い'];

        return view('products.create', [
            'product'    => $product,
            'categories' => $categories,
            'conditions' => $conditions,
            'mode'       => 'edit',
        ]);
    }
    // 出品登録
    public function store(ProductStoreRequest $request)
    {
        $validated = $request->validated();

        $path = null;
        if ($request->hasFile('image')) {
            $saved = $request->file('image')->store('products/' . now()->format('Y/m'), 'public');
            $path  = 'storage/' . $saved;
        }
        $product = Product::create([

            'user_id'     => auth()->id(),
            'title'       => $validated['title'],
            'brand'       => $validated['brand'] ?? null,
            'description' => $validated['description'],
            'condition'   => $validated['condition'],
            'price'       => $validated['price'],
            'image_path'  => $path,
            // 公開/売却済み
            'sale_status' => Product::SALE_STATUS_PUBLIC,
        ]);
        // * 複数カテゴリを中間テーブルへ
        $product->categories()->sync($validated['category_id']);

        return redirect()->route('products.show', $product)->with('success', '出品しました！');
    }
    // 出品更新
    public function update(ProductUpdateRequest $request, Product $product)
    {
        $this->authorize('update', $product);

        $validated = $request->validated();

        $data = [
            'title'       => $validated['title'],
            'brand'       => $validated['brand'] ?? null,
            'description' => $validated['description'],
            'condition'   => $validated['condition'],
            'price'       => $validated['price'],
        ];

        if ($request->hasFile('image')) {
            $saved = $request->file('image')->store('products/' . now()->format('Y/m'), 'public');
            $data['image_path'] = 'storage/' . $saved;

            // 旧画像の削除（public ディスク）
            if ($product->image_path && str_starts_with($product->image_path, 'storage/')) {
                \Illuminate\Support\Facades\Storage::disk('public')
                    ->delete(str_replace('storage/', '', $product->image_path));
            }
        }

        $product->update($data);
        // pivotを同期
        $product->categories()->sync($validated['category_id']);

        return redirect()->route('products.show', $product)
            ->with('success', '商品情報を更新しました。');
    }
    // 出品フォーム
    public function create()
    {
        $categories = Category::orderBy('name')->get();
        $conditions = ['新品・未使用', '未使用に近い', '目立った傷や汚れなし', 'やや傷や汚れあり', '状態が悪い'];

        return view('products.create', compact('categories', 'conditions'));
    }
}
