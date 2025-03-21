<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\UserProfile;
use App\Models\Item;

class MypageController extends Controller
{
    public function edit()
    {
        $user = Auth::user();

        // ✅ `first_login` が false の場合は `/` にリダイレクト
        if (!$user->first_login) {
            return redirect('/');
        }

        // ✅ 購入履歴を取得
        $userProfile = $user->profile;
        $purchasedItems = $userProfile ? $userProfile->purchasedItems : collect([]);

        return view('mypage.profile', compact('purchasedItems'));
    }


    public function update(Request $request)
{
    $user = Auth::user();
    logger()->info("プロフィール更新開始: user_id: {$user->id}");

    // バリデーション
    $validated = $request->validate([
        'profile_image' => 'nullable|image|max:2048',
        'name' => 'required|string|max:255',
        'postal_code' => 'nullable|regex:/^\d{3}-?\d{4}$/',
        'address' => 'nullable|string|max:255',
        'building' => 'nullable|string|max:255',
]);

    logger()->info("バリデーション成功: " . json_encode($validated));

    // 画像がある場合は保存
    if ($request->hasFile('profile_image')) {
        $path = $request->file('profile_image')->store('profile_images', 'public');
        $validated['profile_image'] = $path;
    }

    // プロフィールを保存（既存があれば更新、なければ作成）
    $user->profile()->updateOrCreate(
        ['user_id' => $user->id],
        [
            'username' => $validated['name'],
            'postal_code' => $validated['postal_code'],
            'address' => $validated['address'],
            'building_name' => $validated['building'],
            'profile_image' => $validated['profile_image'] ?? optional($user->profile)->profile_image,
        ]
    );
    logger()->info("プロフィール保存完了");

    // 初回ログインフラグが true の場合のみ false に更新
    if ($user->first_login) {
        $user->first_login = false;
        $user->save();

        // セッション情報を更新
        Auth::setUser($user);
        session()->regenerate();

        logger()->info("🎉 first_login フラグ更新完了 -> ホーム画面にリダイレクト");
        return redirect('/');
    }

    // すでに first_login が false の場合（2回目以降の編集）
    logger()->info("first_login はすでに false -> プロフィール編集画面に戻る");
    return redirect()->route('mypage.profile.edit');
}

    public function index(Request $request)
    {
        $user = Auth::user();
        $tab = $request->query('page', 'buy'); // デフォルトは "buy" (購入履歴)

        if ($tab === 'buy') {
            $items = Item::where('buyer_id', $user->id)->get();
        } elseif ($tab === 'sell') {
            $items = Item::where('user_id', $user->id)->get();
        } else {
            return redirect()->route('mypage.index', ['page' => 'buy']);
        }

        return view('mypage.index', compact('user', 'items', 'tab'));
    }

}