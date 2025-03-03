@extends('layouts.app')

@section('content')
<div class="container">
    <div class="tabs">
        <button class="tab-button active" data-tab="recommended">おすすめ</button>
        <button class="tab-button" data-tab="mylist">マイリスト</button>
    </div>

    <div id="recommended" class="tab-content active">
        <div class="product-list">
            @foreach ($recommendedProducts as $product)
            <div class="product-card">
                <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}">
                <p>{{ $product->name }}</p>
            </div>
            @endforeach
        </div>
    </div>

    <div id="mylist" class="tab-content">
        <div class="product-list">
            @foreach ($userFavorites as $product)
            <div class="product-card">
                <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}">
                <p>{{ $product->name }}</p>
            </div>
            @endforeach
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const tabButtons = document.querySelectorAll('.tab-button');
        const tabContents = document.querySelectorAll('.tab-content');

        tabButtons.forEach(button => {
            button.addEventListener('click', function() {
                tabButtons.forEach(btn => btn.classList.remove('active'));
                tabContents.forEach(content => content.classList.remove('active'));

                this.classList.add('active');
                document.getElementById(this.dataset.tab).classList.add('active');
            });
        });
    });
</script>
@endsection