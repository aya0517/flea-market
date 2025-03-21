@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/purchase_show.css') }}">
@endsection

@section('content')
<div class="purchase-container">
    <!-- 左側の商品情報 -->
    <div class="purchase-left">
        <div class="product-details">
            <img src="{{ asset($item->image_path) }}" alt="{{ $item->name }}" class="product-image">
            <div class="product-info">
                <h2 class="product-title">{{ $item->name }}</h2>
                <p class="price">¥{{ number_format($item->price) }}</p>
            </div>
        </div>

        <!-- 支払い方法 -->
        <div class="payment-method">
            <h3>支払い方法</h3>
            <select id="payment" class="payment-select">
                <option value="" selected disabled>選択してください</option>
                <option value="convenience">コンビニ支払い</option>
                <option value="card">カード支払い</option>
            </select>
        </div>

        <!-- 配送先 -->
        <div class="shipping-address">
            <h3>配送先</h3>
            <a href="{{ route('purchase.address', ['item_id' => $item->id]) }}" class="change-address">変更する</a>
        </div>
        <p class="shipping-details">〒 {{ Auth::user()->userProfile->postal_code ?? '未設定' }}</p>
        <p class="shipping-details">{{ Auth::user()->userProfile->address ?? '未設定' }}</p>
        <p class="shipping-details">{{ Auth::user()->userProfile->building_name ?? '' }}</p>
    </div>

    <!-- 右側の商品代金・支払い方法・購入ボタン -->
    <div class="purchase-right">
        <div class="summary-box">
            <table>
                <tr>
                    <td>商品代金</td>
                    <td>¥{{ number_format($item->price) }}</td>
                </tr>
                <tr>
                    <td>支払い方法</td>
                    <td id="selected-payment">選択してください</td>
                </tr>
            </table>
        </div>
        <form id="purchase-form" method="POST" action="{{ route('purchase.process') }}">
            @csrf
            <input type="hidden" name="payment_method" id="payment_method" value="">
            <input type="hidden" name="item_id" value="{{ $item->id }}">
            <button type="submit" class="purchase-button">購入する</button>
        </form>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function() {
    const paymentSelect = document.getElementById("payment");
    const selectedPayment = document.getElementById("selected-payment");
    const hiddenInput = document.getElementById("payment_method");

    function updatePaymentText() {
        let selectedOption = paymentSelect.options[paymentSelect.selectedIndex].text;
        selectedPayment.innerText = selectedOption;
        hiddenInput.value = paymentSelect.value;
    }

    paymentSelect.addEventListener("change", function() {
        updatePaymentText();
    });
});
</script>
@endsection

