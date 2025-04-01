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

        $userProfile = $user->profile;
        $purchasedItems = $userProfile ? $userProfile->purchasedItems : collect([]);

        return view('mypage.profile', compact('purchasedItems'));
    }


    public function update(Request $request)
{
    $user = Auth::user();
    logger()->info("プロフィール更新開始: user_id: {$user->id}");

    $validated = $request->validate([
        'profile_image' => 'nullable|image|max:2048',
        'name' => 'required|string|max:255',
        'postal_code' => 'nullable|regex:/^\d{3}-?\d{4}$/',
        'address' => 'nullable|string|max:255',
        'building' => 'nullable|string|max:255',
]);

    logger()->info("バリデーション成功: " . json_encode($validated));

    if ($request->hasFile('profile_image')) {
    logger()->info("✅ アップロードされたファイル名: " . $request->file('profile_image')->getClientOriginalName());

    $path = $request->file('profile_image')->store('profile_images', 'public');
    logger()->info("✅ 保存先パス: " . $path);

    $validated['profile_image'] = $path;
} else {
    logger()->error("❌ 画像ファイルがアップロードされていません！");
}


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


    if ($user->first_login) {
        $user->first_login = false;
        $user->save();

        Auth::setUser($user);
        session()->regenerate();

        logger()->info("🎉 first_login フラグ更新完了 -> ホーム画面にリダイレクト");
        return redirect('/');
    }

    logger()->info("first_login はすでに false -> プロフィール編集画面に戻る");
    return redirect()->route('mypage.profile.edit');
}

    public function index(Request $request)
    {
        $user = Auth::user();
        $tab = $request->query('page', 'buy');

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