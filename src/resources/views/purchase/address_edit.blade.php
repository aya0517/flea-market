@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/address_edit.css') }}">
@endsection

@section('content')
<div class="container">
    <h2>住所の変更</h2>
    <form action="{{ route('purchase.address.update', ['item_id' => $item->id]) }}" method="POST">
        @csrf
        <div class="form-group">
            <label for="postal_code">郵便番号</label>
            <input type="text" id="postal_code" name="postal_code" class="form-control" value="{{ old('postal_code', $userProfile->postal_code ?? '') }}" required>
        </div>

        <div class="form-group">
            <label for="address">住所</label>
            <input type="text" id="address" name="address" class="form-control" value="{{ old('address', $userProfile->address ?? '') }}" required>
        </div>

        <div class="form-group">
            <label for="building_name">建物名</label>
            <input type="text" id="building_name" name="building_name" class="form-control" value="{{ old('building_name', $userProfile->building_name ?? '') }}">
        </div>

        <button type="submit" class="btn btn-primary">更新</button>
        <a href="{{ route('purchase.show', ['item_id' => $item->id]) }}" class="btn btn-secondary">キャンセル</a>
    </form>
</div>
@endsection
