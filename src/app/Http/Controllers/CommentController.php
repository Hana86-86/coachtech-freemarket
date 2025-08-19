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
        $product->comments()->create([
            'user_id' => $request->user()->id,
            'body' => $request->validated()['body'],
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
