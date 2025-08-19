<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Comment;
use App\Models\Favorite;
use App\Models\User;
use App\Models\Purchase;

class MypageController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        // 出品商品を取得
        $myProducts = Product::where('user_id',$user->id)
        ->latest()->paginate(8, ['*'], 'my');
        // コメントを取得(商品を一緒に取得)
        $comments = Comment::where('user_id', $user->id)
        ->with('product')->latest()->paginate(8, ['*'], 'comment');
        // お気に入り商品を取得
        $favorites = Favorite::where('user_id', $user->id)
        ->with('product')->latest()->paginate(8, ['*'], 'favorite');
        // 購入履歴を取得
        $purchases = Purchase::where('user_id', $user->id)
        ->with('product')->latest()->paginate(8, ['*'], 'buy');
        // いいねした商品を取得
        $likeProducts = $user->likeProducts()
        ->with('category')->latest('favorites.created_at')
        ->paginate(8, ['products.*'], 'like');

        return view('mypage.index', compact('user', 'myProducts', 'comments', 'favorites', 'purchases', 'likeProducts'));
    }
}
