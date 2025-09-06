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
        $productId = $request->query('product_id');
        $profile   = Profile::firstOrNew(['user_id' => Auth::id()]);

        $sessionShipping = session('checkout_shipping');

        return view('purchase.address', compact('profile', 'productId', 'sessionShipping'));
    }

    public function update(AddressRequest $request)
    {
        $validated = $request->validate([
            'postal_code' => ['required','regex:/^\d{3}-\d{4}$/'], // 123-4567
            'address'     => 'required|string|max:255',
            'building'    => 'nullable|string|max:255',
            'product_id'  => 'nullable|integer',
        ]);
        $shipping = $request->safe()->only(['postal_code', 'address', 'building']);

        session()->put('checkout_shipping', [
            'postal_code' => $validated['postal_code'],
            'address'     => $validated['address'],
            'building'    => $validated['building'] ?? null,
        ]);

        // 住所変更後は購入確認へ戻す
        if (!empty($validated['product_id'])) {
            return redirect()->route('purchase.confirm', [
                'id'      => $validated['product_id'],
                'resume'  => 1,
                ]) ->with('success', '配送先を更新しました。');
        }

        return back()->with('success', '配送先を更新しました。');



    }
}
