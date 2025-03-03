@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/verify-email.css') }}">
@endsection

@section('content')
<div class="verify-email__container">
    <p class="verify-email__text">登録していただいたメールアドレスに認証メールを送信しました。</p>
    <p class="verify-email__text">メール認証を完了してください。</p>

    <a href="https://mail.google.com/" class="verify-email__button" target="_blank">認証はこちらから</a>

    <form method="POST" action="{{ route('verification.resend') }}" class="verify-email__form">
        @csrf
        <button type="submit" class="verify-email__resend">認証メールを再送する</button>
    </form>
</div>
@endsection