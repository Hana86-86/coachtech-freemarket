<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Comment;
use App\Http\Requests\CommentStoreRequest;

class CommentController extends Controller
{
    public function store(CommentStoreRequest $request, Product $product)
    {
        if ($product->sale_status === Product::SALE_STATUS_SOLD) {
            abort(403, '売却済み商品のためコメントできません。');
    }
        $validated = $request->validated();
        $product->comments()->create([
            'user_id' => auth()->id(),
            'body' => $validated['body'],
        ]);

        return back()->with('success', 'コメントを投稿しました。');

    }
    public function destroy(Comment $comment)
    {
        $this->authorize('delete', $comment);

        $comment->delete();

        return back()->with('success', 'コメントを削除しました。');
    }
}
