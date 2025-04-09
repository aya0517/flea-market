<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\MypageController;
use App\Http\Controllers\FavoriteController;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\SellController;

Route::middleware(['web'])->group(function () {
    Route::get('/', [ItemController::class, 'index'])->name('items.index');

    Route::get('/item/{item}', [ItemController::class, 'detail'])->name('items.detail');

    Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register.form');
    Route::post('/register', [AuthController::class, 'register'])->name('register');

    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login.form');
    Route::post('/login', [AuthController::class, 'login'])->name('login');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::middleware(['auth'])->group(function () {
        Route::get('/mypage/profile', [MypageController::class, 'edit'])->name('mypage.profile.edit');
        Route::post('/mypage/profile', [MypageController::class, 'update'])->name('mypage.profile.update');
        Route::post('/item/{item}/comment', [CommentController::class, 'store'])->name('comments.store');
        Route::get('/mypage', [MypageController::class, 'index'])->name('mypage.index');
    });

    Route::get('/email/verify', function () {
        return view('auth.verify-email');
    })->middleware('auth')->name('verification.notice');

    Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
    $request->fulfill();

    $user = auth()->user();

        if (!$user->profile) {
        logger()->info("メール認証後: プロフィール未登録なので編集画面へリダイレクト");
        return redirect()->route('mypage.profile.edit');
    }

    if ($user && $user->first_login) {

        logger()->info("メール認証後: 初回プロフィール編集ページへリダイレクト");
        return redirect()->route('mypage.profile.edit');
    }

    return redirect()->route('items.index');
    })->middleware(['auth', 'signed'])->name('verification.verify');

    Route::post('/email/resend', function (Request $request) {
        $request->user()->sendEmailVerificationNotification();
        return back()->with('message', '認証メールを再送しました。');
    })->middleware(['auth', 'throttle:6,1'])->name('verification.resend');

    Route::post('/items/{item}/favorite', [ItemController::class, 'toggleFavorite'])->name('items.favorite');

    Route::middleware(['auth'])->group(function () {
        Route::get('/purchase/{item_id}', [PurchaseController::class, 'show'])->name('purchase.show');
        Route::post('/purchase/process', [PurchaseController::class, 'processPayment'])->name('purchase.process');

        Route::get('/purchase/success/{item_id}', [PurchaseController::class, 'paymentSuccess'])->name('purchase.success');
        Route::get('/purchase/cancel', [PurchaseController::class, 'paymentCancel'])->name('purchase.cancel');

        Route::get('/purchase/address/{item_id}', [PurchaseController::class, 'editAddress'])->name('purchase.address');
        Route::post('/purchase/address/{item_id}', [PurchaseController::class, 'updateAddress'])->name('purchase.address.update');

        Route::get('/mypage', [MypageController::class, 'index'])->name('mypage.index');
    });

    Route::post('/items/{item}/comments', [CommentController::class, 'store'])->middleware('auth')->name('comments.store');

    Route::post('/favorites', [FavoriteController::class, 'store'])->name('favorites.store');
    Route::delete('/favorites/{item}', [FavoriteController::class, 'destroy'])->name('favorites.destroy');

    Route::middleware(['auth'])->group(function () {
        Route::get('/sell', [SellController::class, 'create'])->name('sell.create');
        Route::post('/sell', [SellController::class, 'store'])->name('sell.store');
    });
});