<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\MypageController;

Route::middleware(['web'])->group(function () {
    // ホームページ（商品一覧）
    Route::get('/', [ItemController::class, 'index'])->name('items.index');

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
});
