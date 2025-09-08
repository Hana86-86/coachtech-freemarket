<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\ProfileController;
use Laravel\Fortify\Fortify;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\ShippingAddressController;
use App\Http\Controllers\CommentController;
use App\Http\Requests\CommentStoreRequest;
use App\Http\Controllers\StripeWebhookController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/
//===========================
//公開ルート（誰でもアクセス可）
//===========================

Route::get('/', function () {
    return redirect('/products');
});

// 商品一覧
Route::get('/products', [ProductController::class, 'index'])->name('products.index');
// 商品詳細 + 数値制約
Route::get('/products/{product}', [ProductController::class, 'show'])->whereNumber('product')->name('products.show');

// コンビニ決済
Route::post('/stripe/webhook',[StripeWebhookController::class, 'handle'])->name('stripe.webhook');

/*
|---------------------------------------------
| Fortify / メール認証ルート
|---------------------------------------------
*/

Fortify::registerView(function () {
    return view('auth.register');
});

Route::get('/email/verify', function () {
    return view('auth.verify-email');
})->middleware('auth')->name('verification.notice');

Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
    $request->fulfill();
    if ($request->user()->is_first_login) {
        return redirect()->route('profile.edit');
    }
    return redirect()->route('products.index'); 
})->middleware(['auth', 'signed'])->name('verification.verify');

Route::post('/email/verification-notification', function (Request $request) {
    $request->user()->sendEmailVerificationNotification();
    return back()->with('status', 'verification-link-sent');
})->middleware(['auth', 'throttle:6,1'])->name('verification.send');

//===========================
//認証は必要だが verified は不要
//===========================
Route::middleware(['auth'])->group(function () {
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::post('/profile', [ProfileController::class, 'store'])->name('profile.store');
});

Route::middleware('auth')->get('/profile/purchases', [ProfileController::class, 'purchases'])->name('profile.purchases');

//==============================
// 認証・メール認証が必要なルート
//==============================
Route::middleware(['auth','verified'])->group(function () {

    // 出品画面表示
    Route::get('/sell',[ProductController::class,'create'])->name('products.create');
    // 出品処理
    Route::post('/sell',[ProductController::class,'store'])->name('products.store');

    // 購入処理
    Route::get('/purchase/confirm/{id}',[PurchaseController::class, 'confirm'])->name('purchase.confirm');
    // 決済処理
    Route::post('/purchase/checkout/{id}', [PurchaseController::class, 'checkout'])->name('purchase.checkout');
    Route::get('/purchase/success/{id}', [PurchaseController::class, 'success'])->name('purchase.success');

    // 配送先編集
    Route::get('/purchase/address', [ShippingAddressController::class, 'edit'])->name('purchase.address.edit');
    Route::post('/purchase/address',[ShippingAddressController::class, 'update'])->name('purchase.address.update');

    // いいね
    Route::post('/products/{product}/favorite-toggle', [FavoriteController::class, 'toggle'])->name('favorites.toggle');

    // コメント投稿
    Route::post('/products/{product}/comments', [CommentController::class, 'store'])->name('comments.store');
    // コメント削除
    Route::delete('/comments/{comment}', [CommentController::class, 'destroy'])->name('comments.destroy');

    // マイページ（プロフィール確認 + 出品/購入一覧
    Route::get('/profile',[ProfileController::class,'show'])->name('profile.show');
    // プロフィール編集
    
    Route::put('/profile',[ProfileController::class, 'update'])->name('profile.update');
    // プロフィール画像削除
    Route::delete('/profile/avatar', [ProfileController::class, 'destroyAvatar'])->name('profile.avatar.destroy');

    // 出品者用
    Route::get('/products/{product}/edit', [ProductController::class, 'edit'])->name('products.edit');
    Route::delete('/products/{product}', [ProductController::class, 'destroy'])->name('products.destroy');
    Route::put('/products/{product}', [ProductController::class, 'update'])->name('products.update');



});
