<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Profile;
use App\Http\Requests\AddressRequest;

class ShippingAddressController extends Controller
{
    public function edit(Request $request)
    {
        // product_id をクエリで受けて、保存後に確認画面へ戻す
        $productId = $request->query('product_id');
        $profile = Profile::firstOrNew(['user_id' => Auth::id()]);
        
        return view('purchase.address', compact('profile', 'productId'));
    }

    public function update(AddressRequest $request)
    {
        $validated = $request->validate([
            'postal_code' => ['required','regex:/^\d{3}-\d{4}$/'], // 123-4567
            'address'     => 'required|string|max:255',
            'building'    => 'nullable|string|max:255',
            'product_id'  => 'nullable|integer',
        ]);
        // 取得
        $profile = \App\Models\Profile::firstOrNew(['user_id' => auth()->id()]);
        // 更新
        \App\Models\Profile::updateOrCreate(
            ['user_id' => Auth::id()],
            [
                'postal_code' => $validated['postal_code'],
                'address'     => $validated['address'],
                'building'    => $validated['building'] ?? null,
            ]
        );

        // 住所変更後は購入確認へ戻す(product_id があればそこへ)
        if (!empty($validated['product_id'])) {
            return redirect()->route('purchase.confirm', $validated['product_id'])
                ->with('success', '配送先を更新しました。');
        }

        return back()->with('success', '配送先を更新しました。');

    }
}
