@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/sell.css') }}">
@endsection

@section('content')
<div class="container">
    <h2 class="sell-title">商品の出品</h2>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <form action="{{ route('sell.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="form-group">
            <label for="image">商品画像</label>
            <input type="file" id="image" name="image" class="form-control">
            @error('image')
                <p class="error-message" style="color: red;">{{ $message }}</p>
            @enderror
        </div>

        <h3 class="section-title">商品の詳細</h3>

        <div class="form-group">
            <label>カテゴリー</label>
            <div class="category-list">
                @foreach($categories as $category)
                    <label class="category-item">
                        <input type="checkbox" name="categories[]" value="{{ $category->id }}"
                            {{ in_array($category->id, old('categories', [])) ? 'checked' : '' }}>
                        {{ $category->name }}
                    </label>
                @endforeach
            </div>
            @error('categories')
                <p class="error-message" style="color: red;">{{ $message }}</p>
            @enderror
        </div>

        <div class="form-group">
            <label for="condition">商品の状態</label>
            <select name="condition_id" id="condition" class="form-control">
                <option value="">選択してください</option>
                @foreach($conditions as $condition)
                    <option value="{{ $condition->id }}"
                        {{ old('condition_id') == $condition->id ? 'selected' : '' }}>
                        {{ $condition->name }}
                    </option>
                @endforeach
            </select>
            @error('condition_id')
                <p class="error-message" style="color: red;">{{ $message }}</p>
            @enderror
        </div>

        <div class="form-group">
            <label for="name">商品名</label>
            <input type="text" id="name" name="name" class="form-control"
                value="{{ old('name') }}">
            @error('name')
                <p class="error-message" style="color: red;">{{ $message }}</p>
            @enderror
        </div>

        <div class="form-group">
            <label for="brand">ブランド名</label>
            <input type="text" id="brand" name="brand" class="form-control"
                value="{{ old('brand') }}">
        </div>

        <div class="form-group">
            <label for="description">商品の説明</label>
            <textarea id="description" name="description" class="form-control" rows="4">{{ old('description') }}</textarea>
            @error('description')
                <p class="error-message" style="color: red;">{{ $message }}</p>
            @enderror
        </div>

        <div class="form-group">
            <label for="price">販売価格</label>
            <div class="price-input">
                <span>¥</span>
                <input type="number" id="price" name="price" class="form-control"
                    value="{{ old('price') }}">
            </div>
            @error('price')
                <p class="error-message" style="color: red;">{{ $message }}</p>
            @enderror
        </div>

        <button type="submit" class="sell-button">出品する</button>
    </form>
</div>
@endsection
