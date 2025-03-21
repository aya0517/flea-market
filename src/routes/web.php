<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\MypageController;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\SellController;

Route::middleware(['web'])->group(function () {
    // ホームページ（商品一覧）
    Route::get('/', [ItemController::class, 'index'])->name('items.index');

    // 商品詳細ページ
    Route::get('/item/{item}', [ItemController::class, 'detail'])->name('items.detail');

    // ユーザー登録
    Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register.form');
    Route::post('/register', [AuthController::class, 'register'])->name('register');

    // ログイン・ログアウト
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login.form');
    Route::post('/login', [AuthController::class, 'login'])->name('login');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // マイページ（認証が必要）
    Route::middleware(['auth'])->group(function () {
        Route::get('/mypage/profile', [MypageController::class, 'edit'])->name('mypage.profile.edit');
        Route::post('/mypage/profile', [MypageController::class, 'update'])->name('mypage.profile.update');
        Route::post('/item/{item}/comment', [CommentController::class, 'store'])->name('comments.store');
        Route::get('/mypage', [MypageController::class, 'index'])->name('mypage.index');
    });

    // メール認証
    Route::get('/email/verify', function () {
        return view('auth.verify-email');
    })->middleware('auth')->name('verification.notice');

    Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
        $request->fulfill();
        return redirect()->route('items.index');
    })->middleware(['auth', 'signed'])->name('verification.verify');

    Route::post('/email/resend', function (Request $request) {
        $request->user()->sendEmailVerificationNotification();
        return back()->with('message', '認証メールを再送しました。');
    })->middleware(['auth', 'throttle:6,1'])->name('verification.resend');

    Route::post('/items/{item}/favorite', [ItemController::class, 'toggleFavorite'])->name('items.favorite');

    // 購入関連
    Route::middleware(['auth'])->group(function () {
        Route::get('/purchase/{item_id}', [PurchaseController::class, 'show'])->name('purchase.show');
        Route::post('/purchase/process', [PurchaseController::class, 'processPayment'])->name('purchase.process');

        // 購入成功時に `item_id` を含める
        Route::get('/purchase/success/{item_id}', [PurchaseController::class, 'paymentSuccess'])->name('purchase.success');
        Route::get('/purchase/cancel', [PurchaseController::class, 'paymentCancel'])->name('purchase.cancel');

        // 配送先変更
        Route::get('/purchase/address/{item_id}', [PurchaseController::class, 'editAddress'])->name('purchase.address');
        Route::post('/purchase/address/{item_id}', [PurchaseController::class, 'updateAddress'])->name('purchase.address.update');

        // 購入履歴
        Route::get('/mypage', [MypageController::class, 'index'])->name('mypage.index');
    });

    // 商品コメント（認証が必要）
    Route::post('/items/{item}/comments', [CommentController::class, 'store'])->middleware('auth')->name('comments.store');

     // 商品販売ページ
    Route::middleware(['auth'])->group(function () {
        Route::get('/sell', [SellController::class, 'create'])->name('sell.create');
        Route::post('/sell', [SellController::class, 'store'])->name('sell.store');
    });
});
