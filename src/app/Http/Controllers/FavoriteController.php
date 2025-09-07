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
    if (!auth()->check()) {
        return response()->json(['error' => 'Unauthorized'], 401);
    }

    $userId = auth()->id();

    $query = $product->favorites()->where('user_id', $userId);

    $favorited = false;
    if ($query->exists()) {
        $query->delete();
    } else {
        $product->favorites()->create(['user_id' => $userId]);
        $favorited = true;
    }

    return response()->json([
        'favorited' => $favorited,
        'count'     => $product->favorites()->count(),
    ]);
}
}
