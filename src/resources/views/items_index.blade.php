@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/items_index.css') }}">
@endsection

@section('content')
<div class="container">
    <!-- ナビゲーション -->
    <div class="nav-tabs">
        <a href="{{ route('items.index', array_merge(request()->query(), ['tab' => 'recommended'])) }}" class="{{ $category == 'recommended' ? 'active' : '' }}">
            おすすめ
        </a>
        <a href="{{ route('items.index', array_merge(request()->query(), ['tab' => 'mylist'])) }}" class="{{ $category == 'mylist' ? 'active' : '' }}">
            マイリスト
        </a>
    </div>

    <!-- 検索結果表示 -->
    @if($search)
        <p>「{{ $search }}」の検索結果</p>
    @endif

    <div class="product-list">
    @forelse ($products as $product)
        <div class="product-card">
            <a href="{{ route('items.detail', ['item' => $product->id]) }}">
                <img src="{{ asset($product->image_path) }}" alt="{{ $product->name }}">
                <p class="product-name">{{ $product->name }}</p>
            </a>
            @if ($product->is_sold)
                <p class="sold-label">Sold</p>
            @endif
        </div>
    @empty
        <p>該当する商品がありません。</p>
    @endforelse

    @if ($product->is_sold)
        <p class="sold-label">Sold</p>
    @endif

</div>
@endsection
