@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/items_index.css') }}">
@endsection

@section('content')
<div class="container">
    <div class="nav-tabs">
        <a href="{{ route('items.index', array_merge(request()->query(), ['tab' => 'recommended'])) }}" class="{{ $category == 'recommended' ? 'active' : '' }}">
            おすすめ
        </a>
        <a href="{{ route('items.index', array_merge(request()->query(), ['tab' => 'mylist'])) }}" class="{{ $category == 'mylist' ? 'active' : '' }}">
            マイリスト
        </a>
    </div>

    @if($search)
        <p>「{{ $search }}」の検索結果</p>
    @endif

    <div class="product-list">
    @forelse ($items as $item)
        <div class="product-card">
            <a href="{{ route('items.detail', ['item' => $item->id]) }}">
                <img src="{{ asset($item->image_path) }}" alt="{{ $item->name }}">
            </a>

            <div class="product-info">
                <a href="{{ route('items.detail', ['item' => $item->id]) }}">
                    <p class="product-name">{{ $item->name }}</p>
                </a>

                @if ($item->is_sold)
                    <p class="sold-label">Sold</p>
                @endif
            </div>
        </div>
    @empty
        <p>該当する商品がありません。</p>
    @endforelse
</div>

@endsection
