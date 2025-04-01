@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/mypage_profile.css') }}">
@endsection

@section('content')
<div class="profile-content">
    <h2 class="profile-title">プロフィール設定</h2>

    @if(session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
    @endif

    <form action="{{ route('mypage.profile.update') }}" method="post" enctype="multipart/form-data">
        @csrf

        <div class="profile-image">
            <img src="{{ isset(Auth::user()->profile) && Auth::user()->profile->profile_image ? asset('storage/' . Auth::user()->profile->profile_image) : asset('images/default-avatar.png') }}" alt=" ">
            <label for="profile_image" class="profile-image__button">画像を選択する</label>
            <input type="file" id="profile_image" name="profile_image" class="profile-image__input">
        </div>

        <div class="form__group">
            <label for="name">ユーザー名</label>
            <input type="text" name="name" value="{{ old('name', isset(Auth::user()->profile) ? Auth::user()->profile->username : '') }}" required>
        </div>

        <div class="form__group">
            <label for="postal_code">郵便番号</label>
            <input type="text" name="postal_code" value="{{ old('postal_code', isset(Auth::user()->profile) ? Auth::user()->profile->postal_code : '') }}">
        </div>

        <div class="form__group">
            <label for="address">住所</label>
            <input type="text" name="address" value="{{ old('address', isset(Auth::user()->profile) ? Auth::user()->profile->address : '') }}">
        </div>

        <div class="form__group">
            <label for="building">建物名</label>
            <input type="text" name="building" value="{{ old('building', isset(Auth::user()->profile) ? Auth::user()->profile->building_name : '') }}">
        </div>

        <button type="submit" class="form__button-submit">更新する</button>
    </form>
</div>

@endsection