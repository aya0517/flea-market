<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\UserProfile;

class MypageController extends Controller
{
    public function edit()
    {
        return view('mypage.profile');
    }

    public function update(Request $request)
    {
        $user = Auth::user();
        logger()->info("プロフィール更新開始: first_login の値（更新前）: " . json_encode($user->first_login));

        // first_login の値をデータベースから直接取得
        $dbUser = \App\Models\User::find($user->id);
        logger()->info("データベースの first_login の値: " . json_encode($dbUser->first_login));

        // バリデーション
        $validated = $request->validate([
            'profile_image' => 'nullable|image|max:2048',
            'name' => 'required|string|max:255',
            'postal_code' => 'nullable|digits:7|integer',
            'address' => 'nullable|string|max:255',
            'building' => 'nullable|string|max:255',
        ]);

        // プロフィール画像の処理
        if ($request->hasFile('profile_image')) {
            $path = $request->file('profile_image')->store('profile_images', 'public');
            $validated['profile_image'] = $path;
        }

        // UserProfile を更新または作成
        UserProfile::updateOrCreate(
            ['user_id' => $user->id],
            [
                'profile_image' => $validated['profile_image'] ?? $user->profile->profile_image ?? null,
                'username' => $validated['name'],
                'postal_code' => $validated['postal_code'] ?? null,
                'address' => $validated['address'] ?? null,
                'building_name' => $validated['building'] ?? null,
            ]
        );

        // **first_login の更新処理**
        if ($user->first_login) {
            logger()->info("プロフィール更新完了: first_login を false に更新");

            // **データベースを直接更新**
            DB::table('users')->where('id', $user->id)->update(['first_login' => false]);

            // **更新後のデータを取得**
            $updatedUser = \App\Models\User::find($user->id);
            logger()->info("データベース更新後の first_login の値: " . json_encode($updatedUser->first_login));

            // **セッションを更新**
            Auth::setUser($updatedUser);

            logger()->info("セッション更新後の first_login の値: " . json_encode(Auth::user()->first_login));

            // `first_login` の更新が適用されたかチェック
            if (Auth::user()->first_login === false) {
                logger()->info("first_login 更新成功: / にリダイレクト");
                return redirect('/')->with('success', 'プロフィールを更新しました！');
            } else {
                logger()->error("first_login 更新失敗: まだ true のまま");
            }
        }

        logger()->info("プロフィール更新完了: 通常ログインなので /mypage/profile にリダイレクト");
        return redirect()->route('mypage.profile.edit')->with('success', 'プロフィールを更新しました！');
    }
}
