@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/mypage_index.css') }}">
@endsection

@section('content')
<div class="container">
    <div class="profile-header">
        <div class="profile-info">
            <img src="{{ isset($user->profile) && $user->profile->profile_image ? asset('storage/' . $user->profile->profile_image) : asset('images/default-profile.png') }}" alt="プロフィール画像" class="profile-image">
            <h2 class="username">{{ $user->name }}</h2>
        </div>
        <a href="{{ route('mypage.profile.edit') }}" class="btn edit-profile">プロフィールを編集</a>
    </div>

    <div class="tabs">
        <a href="{{ route('mypage.index', ['page' => 'sell']) }}" class="{{ $tab === 'sell' ? 'active' : '' }}">出品した商品</a>
        <a href="{{ route('mypage.index', ['page' => 'buy']) }}" class="{{ $tab === 'buy' ? 'active' : '' }}">購入した商品</a>
    </div>

    <div class="items-grid">
        @if($items->isEmpty())
            <p class="no-items">該当する商品がありません。</p>
        @else
            @foreach($items as $item)
                <div class="item-card">
                    <a href="{{ route('items.detail', ['item' => $item->id]) }}">
                        <img src="{{ asset($item->image_path) }}" alt="商品画像" class="item-image">
                        <p class="item-name">{{ $item->name }}</p>
                    </a>
                </div>
            @endforeach
        @endif
    </div>
</div>
@endsection
