<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Profile;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    public function create()
    {
        return view('profile.create');
    }

    public function store(Request $request)
    {

    $request->validate([
        'phone'       => 'required|string|max:20',
        'postal_code' => 'required|string|max:10',
        'address'     => 'required|string|max:255',
        'building'    => 'nullable|string|max:255',
        'profile_image' => 'nullable|image|max:2048',
    ]);

    // プロフィールを保存
    Profile::create([
        'user_id'       => auth()->id(),
        'phone'         => $request->phone,
        'postal_code'   => $request->postal_code,
        'address'       => $request->address,
        'building'      => $request->building,
        'profile_image' => $request->file('profile_image')?->store('profiles', 'public'),
    ]);

    // 初回ログインフラグを false に更新
    $request->user()->update(['is_first_login' => false]);

    return redirect()->route('items.index')
        ->with('success', 'プロフィールが登録されました。');
}
}