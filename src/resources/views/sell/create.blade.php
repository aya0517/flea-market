@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/sell.css') }}">
@endsection

@section('content')
<div class="container">
    <h2 class="sell-title">商品の出品</h2>

    <form action="{{ route('sell.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <!-- 商品画像 -->
        <div class="form-group">
            <label for="image">商品画像</label>
            <input type="file" id="image" name="image" class="form-control">
        </div>

        <!-- 商品の詳細 見出し + 下線 -->
        <h3 class="section-title">商品の詳細</h3>

        <!-- カテゴリ選択（複数選択可能） -->
        <div class="form-group">
            <label>カテゴリー</label>
            <div class="category-list">
                @foreach($categories as $category)
                    <label class="category-item">
                        <input type="checkbox" name="categories[]" value="{{ $category->id }}">
                        {{ $category->name }}
                    </label>
                @endforeach
            </div>
        </div>

        <!-- 商品の状態 -->
        <div class="form-group">
            <label for="condition">商品の状態</label>
            <select name="condition_id" id="condition" class="form-control">
                <option value="">選択してください</option>
                @foreach($conditions as $condition)
                    <option value="{{ $condition->id }}">{{ $condition->name }}</option>
                @endforeach
            </select>
        </div>

        <!-- 商品名と説明 見出し + 下線 -->
        <h3 class="section-title">商品名と説明</h3>

        <!-- 商品名 -->
        <div class="form-group">
            <label for="name">商品名</label>
            <input type="text" id="name" name="name" class="form-control" required>
        </div>

        <!-- ブランド名 -->
        <div class="form-group">
            <label for="brand">ブランド名</label>
            <input type="text" id="brand" name="brand" class="form-control">
        </div>

        <!-- 商品説明 -->
        <div class="form-group">
            <label for="description">商品の説明</label>
            <textarea id="description" name="description" class="form-control" rows="4" required></textarea>
        </div>

        <!-- 販売価格 -->
        <div class="form-group">
            <label for="price">販売価格</label>
            <div class="price-input">
                <span>¥</span>
                <input type="number" id="price" name="price" class="form-control" required>
            </div>
        </div>

        <!-- 出品ボタン -->
        <button type="submit" class="sell-button">出品する</button>
    </form>
</div>
@endsection
