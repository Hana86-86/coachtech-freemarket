<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Favorite;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;


class FavoriteController extends Controller
{
    public function toggle(Request $request, Product $product)
{
        if (auth()->check()) {
        $col = 'user_id';
        $val = auth()->id();
    } else {
        $token = $request->cookie('vtoken');
        if (!$token) {
            $token = (string) \Illuminate\Support\Str::uuid();
            cookie()->queue(cookie('vtoken', $token, 60*24*365)); // 1年
        }
        $col = 'visitor_token';
        $val = $token;
    }

    $query = $product->favorites()->where($col, $val);

    $favorited = false;
    if ($query->exists()) {
        $query->delete();        // 解除
    } else {
        $product->favorites()->create([$col => $val]); // 付与
        $favorited = true;
    }

    return response()->json([
        'favorited' => $favorited,
        'count'     => $product->favorites()->count(),
    ]);
}
}
