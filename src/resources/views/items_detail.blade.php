@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
<link rel="stylesheet" href="{{ asset('css/items_detail.css') }}">
@endsection

@section('content')
<div class="container item-detail">
    <!-- 商品画像 -->
    <div class="item-image">
        <img src="{{ asset($item->image_path) }}" alt="{{ $item->name }}">
    </div>

    <!-- 商品情報 -->
    <div class="item-info">
        <h2>{{ $item->name }}</h2>
        <p class="brand">{{ $item->brand }}</p>
        <p class="price">￥{{ number_format($item->price) }}<span class="tax">（税込）</span></p>

        <!-- いいね＆コメント -->
<div class="item-icons">
    <!-- いいねアイコン -->
    <span class="like-icon" data-item-id="{{ $item->id }}">
        @auth
            @php
                $isLiked = $item->favorites->contains('user_id', auth()->id());
            @endphp
            <form action="{{ $isLiked ? route('favorites.destroy', $item->id) : route('favorites.store') }}"
                method="POST" class="inline-form favorite-form">
                @csrf
                @if ($isLiked)
                    @method('DELETE')
                @else
                    <input type="hidden" name="item_id" value="{{ $item->id }}">
                @endif

                <button type="submit" class="icon-button">
                    <i class="fa-regular fa-star {{ $isLiked ? 'active' : '' }}"></i>
                    <span class="count">{{ $item->favorites->count() }}</span>
                </button>
            </form>
        @endauth

        @guest
            <!-- ログインしてない場合は静的表示 -->
            <i class="fa-regular fa-star"></i>
            <span class="count">{{ $item->favorites->count() }}</span>
        @endguest
    </span>

    <!-- コメントアイコン -->
    <span class="comment-icon">
        <a href="#comment-section" class="icon-button">
            <i class="fa-regular fa-comment"></i>
            <span class="count">{{ $item->comments->count() }}</span>
        </a>
    </span>
</div>


        <!-- 購入ボタン -->
        <a href="{{ route('purchase.show', ['item_id' => $item->id]) }}" class="purchase-button">購入手続きへ</a>

        <!-- 商品の情報 -->
        @if ($item->categories->isNotEmpty())
            <div class="categories">
                <span>カテゴリ:</span>
                @foreach($item->categories as $category)
                    <span class="category-tag">{{ $category->name }}</span>
                @endforeach
            </div>
        @else
        <p>この商品にはカテゴリーがありません。</p>
        @endif

        <div class="condition">
            <span>商品の状態: {{ $item->condition->name }}</span>
        </div>

        <h3>商品説明</h3>
        <p class="description">{{ $item->description }}</p>

        <!-- コメントセクション -->
        <h3>コメント ({{ $item->comments->count() }})</h3>
        <div class="comment-section">
            @foreach ($item->comments as $comment)
                <div class="comment">
                    <div class="comment-icon">
                        <img src="{{ isset($comment->user->profile) && $comment->user->profile->profile_image ? asset('storage/' . $comment->user->profile->profile_image) : asset('images/default-profile.png') }}" alt="Profile">
                        <span class="user-name">{{ $comment->user->name }}</span>
                    </div>
                    <div class="bubble">
                        {{ $comment->content }}
                    </div>
                </div>
            @endforeach

        <form
        method="POST"
        action="{{ Auth::check() ? route('comments.store', ['item' => $item->id]) : route('login') }}"
        @if (!Auth::check()) onsubmit="redirectToLogin(event)" @endif
        >
            @csrf
            <textarea
                name="content"
                placeholder="商品へのコメント"
                onclick="handleCommentClick()"
            >{{ old('content') }}</textarea>

            @error('content')
                <p class="error-message" style="color: red;">{{ $message }}</p>
            @enderror

            <button type="submit">
                コメントを送信する
            </button>
        </form>
    </div>

    <script>
        function handleCommentClick() {
            @if (!Auth::check())
                window.location.href = "{{ route('login') }}";
            @endif
        }

        function redirectToLogin(e) {
            e.preventDefault();
                window.location.href = "{{ route('login') }}";
        }
    </script>
</div>
@endsection

@section('script')
<script>
document.addEventListener("DOMContentLoaded", function() {
    console.log("✅ ページが読み込まれました！"); 

    let csrfMetaTag = document.querySelector('meta[name="csrf-token"]');
    let csrfToken = csrfMetaTag ? csrfMetaTag.content : "";

    document.querySelectorAll(".like-icon").forEach(icon => {
        console.log("✅ .like-icon が見つかりました！", icon); 

        icon.addEventListener("click", function() {
            console.log("✅ いいねボタンがクリックされました！"); 

            let itemId = this.getAttribute("data-item-id");
            let iconElement = this.querySelector("i");
            let countElement = this.querySelector(".count");

            let isFavorited = iconElement.classList.contains("active");

            iconElement.classList.toggle("active");

            let currentCount = parseInt(countElement.textContent);
            countElement.textContent = isFavorited ? currentCount - 1 : currentCount + 1;

            console.log(`✅ APIリクエストを送信: /items/${itemId}/favorite`);

            fetch(`/items/${itemId}/favorite`, {
                method: "POST",
                headers: {
                    "X-CSRF-TOKEN": csrfToken,
                    "Content-Type": "application/json"
                }
            })
            .then(response => response.json())
            .then(data => {
                console.log("✅ サーバーのレスポンス:", data);

                if (!data.favorited) {
                    iconElement.classList.remove("active");
                    countElement.textContent = data.favorites_count;
                } else {
                    iconElement.classList.add("active");
                    countElement.textContent = data.favorites_count;
                }
            })
            .catch(error => console.error("❌ fetch() のエラー:", error));
        });
    });
});
</script>
@endsection
