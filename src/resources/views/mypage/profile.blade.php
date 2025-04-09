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
            @php
                $profileImage = optional(Auth::user()->profile)->profile_image;
            @endphp

            <img src="{{ $profileImage ? asset('storage/' . $profileImage) : asset('images/default-avatar.png') }}">

            <label for="profile_image" class="profile-image__button">画像を選択する</label>
            <input type="file" id="profile_image" name="profile_image" class="profile-image__input">
            @error('profile_image')
                <p class="error-message" style="color: red;">{{ $message }}</p>
            @enderror
        </div>

        <div class="form__group">
            <label for="name">ユーザー名</label>
            <input type="text" name="name" value="{{ old('name', Auth::user()->profile->username ?? '') }}">
            @error('name')
                <p class="error-message" style="color: red;">{{ $message }}</p>
            @enderror
        </div>

        <div class="form__group">
            <label for="postal_code">郵便番号</label>
            <input type="text" name="postal_code" value="{{ old('postal_code', Auth::user()->profile->postal_code ?? '') }}">
            @error('postal_code')
                <p class="error-message" style="color: red;">{{ $message }}</p>
            @enderror
        </div>

        <div class="form__group">
            <label for="address">住所</label>
            <input type="text" name="address" value="{{ old('address', Auth::user()->profile->address ?? '') }}">
            @error('address')
                <p class="error-message" style="color: red;">{{ $message }}</p>
            @enderror
        </div>

        <div class="form__group">
            <label for="building_name">建物名</label>
            <input type="text" name="building_name" value="{{ old('building_name', Auth::user()->profile->building_name ?? '') }}">
            @error('building_name')
                <p class="error-message" style="color: red;">{{ $message }}</p>
            @enderror
        </div>

        <button type="submit" class="form__button-submit">更新する</button>
    </form>
</div>
@endsection
