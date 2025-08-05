<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Favorite;
use Illuminate\Support\Facades\Auth;

class FavoriteController extends Controller
{
    // お気に入り追加
    public function store($productId)
    {
        Favorite::firstOrCreate([
            'user_id' => Auth::id(),
            'product_id' => $productId,
        ]);

        return back()->with('success','お気に入りを追加しました！');
    }

    // お気に入り解除
    public function destroy($productId)
    {
        Favorite::where('user_id', Auth::id())
                ->where('product_id', $productId)
                ->delete();

        return back()->with('success', 'お気に入りを削除しました！');
    }

    // マイリスト表示
    public function index()
    {
        $favorites = Favorite::with('product')
                            ->where('user_id', Auth::id())
                            ->get();

        return view('favorites.index', compact('favorites'));
    }
}
