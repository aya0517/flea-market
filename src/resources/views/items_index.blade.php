@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/items_index.css') }}">
@endsection

@section('content')
<div class="container">
    <!-- ナビゲーション -->
    <div class="nav-tabs">
        <a href="{{ route('items.index', ['category' => 'recommended']) }}"
            class="{{ $category == 'recommended' ? 'active' : '' }}">
            おすすめ
        </a>
        <a href="{{ route('items.index', ['category' => 'mylist']) }}"
            class="{{ $category == 'mylist' ? 'active' : '' }}">
            マイリスト
        </a>
    </div>

    @if ($category == 'recommended')
        <div class="product-list">
            @foreach ($recommendedProducts as $product)
            <div class="product-card">
                <img src="{{ $product->image_url }}" alt="{{ $product->name }}">
                <p class="product-name">{{ $product->name }}</p>
            </div>
            @endforeach
        </div>
    @elseif ($category == 'mylist')
        <div class="product-list">
            @foreach ($userFavorites as $product)
            <div class="product-card">
                <img src="{{ $product->image_url }}" alt="{{ $product->name }}">
                <p class="product-name">{{ $product->name }}</p>
            </div>
            @endforeach
        </div>
    @endif
</div>
@endsection
