@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/purchase_show.css') }}">
@endsection

@section('content')
<div class="purchase-container">
    <div class="purchase-left">
        <div class="product-details">
            <img src="{{ asset($item->image_path) }}" alt="{{ $item->name }}" class="product-image">
            <div class="product-info">
                <h2 class="product-title">{{ $item->name }}</h2>
                <p class="price">¥{{ number_format($item->price) }}</p>
            </div>
        </div>

        <div class="payment-method">
            <h3>支払い方法</h3>
            <select id="payment" class="payment-select">
                <option value="" selected disabled>選択してください</option>
                <option value="convenience">コンビニ支払い</option>
                <option value="card">カード支払い</option>
            </select>
        </div>

        <div class="shipping-address">
            <h3>配送先</h3>
            <a href="{{ route('purchase.address', ['item_id' => $item->id]) }}" class="change-address">変更する</a>
        </div>
        <p class="shipping-details">〒 {{ Auth::user()->userProfile->postal_code ?? '未設定' }}</p>
        <p class="shipping-details">{{ Auth::user()->userProfile->address ?? '未設定' }}</p>
        <p class="shipping-details">{{ Auth::user()->userProfile->building_name ?? '' }}</p>
    </div>

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

        @if(session('error'))
            <div class="alert">
                {{ session('error') }}
            </div>
        @endif

        @if(session('success'))
            <div class="alert">
                {{ session('success') }}
            </div>
        @endif
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

