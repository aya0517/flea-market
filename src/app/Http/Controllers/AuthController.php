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
        return redirect('/email/verify');
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

            logger()->info("first_login の値: " . json_encode($user->first_login));

            if ($user->first_login) {
                logger()->info("初回ログイン: /mypage/profile にリダイレクト");
                return redirect()->route('mypage.profile.edit');
            }

            return redirect('/');
        }

        logger()->error("ログイン失敗: ", ['email' => $credentials['email']]);
        return back()->withErrors([
            'email' => 'ログイン情報が正しくありません。',
        ]);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
