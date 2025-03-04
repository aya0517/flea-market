<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\LoginRequest;

class AuthController extends Controller
{
    public function index()
    {
        return view('index');
    }

    public function showRegisterForm()
    {
        return view('auth.register');
    }

    public function register(RegisterRequest $request)
    {
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'first_login' => true,
        ]);

        Auth::login($user);

        try {
            $user->sendEmailVerificationNotification();
            logger()->info("認証メール送信: 成功", ['user_id' => $user->id, 'email' => $user->email]);
        } catch (\Exception $e) {
            logger()->error("認証メール送信失敗: " . $e->getMessage());
            return back()->with('error', 'メール送信に失敗しました: ' . $e->getMessage());
        }

        logger()->info("リダイレクト先: /email/verify");
        return redirect('/email/verify')->with('success', '認証メールを送信しました。メールを確認してください。');
    }


    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(LoginRequest $request)
    {
        $credentials = $request->only('email', 'password');

        logger()->info("ログイン試行: ", ['email' => $credentials['email']]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            $user = Auth::user();
            logger()->info("ログイン成功: ", ['user' => $user, 'session_id' => session()->getId()]);

            // デバッグ用ログ
            logger()->info("first_login の値: " . json_encode($user->first_login));

            // 初回ログイン時: /mypage/profile にリダイレクトし、プロフィール更新後に / に遷移
            if ($user->first_login) {
                logger()->info("初回ログイン: first_login フラグを false に更新し、/mypage/profile にリダイレクト");
                return redirect()->route('mypage.profile.edit');
            }

            // 2回目以降のログインはそのまま `/` に遷移
            logger()->info("Redirecting to: /");
            return redirect('/')->with('success', 'ログインしました！');
        }

        logger()->error("ログイン失敗: ", ['email' => $credentials['email']]);
        return back()->withErrors([
            'email' => 'ログイン情報が正しくありません。',
        ]);
    }

    public function profileUpdate(Request $request)
{
    $user = Auth::user();
    $user->name = $request->input('name');
    $user->postal_code = $request->input('postal_code');
    $user->address = $request->input('address');
    $user->building = $request->input('building');

    if ($request->hasFile('profile_image')) {
        $path = $request->file('profile_image')->store('profile_images', 'public');
        $user->profile_image = $path;
    }

    // 初回ログインのフラグを false に更新
    if ($user->first_login) {
        $user->first_login = false;
    }

    $user->save();

    return redirect()->route('items.index')->with('success', 'プロフィールを更新しました。');
}


    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    public function profile()
    {
        return view('mypage.profile');
    }
}
