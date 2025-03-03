<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Item;

class ItemController extends Controller
{
    public function index()
    {
        // おすすめ商品の取得
        $recommendedProducts = Item::withCount('favorites')
            ->orderByDesc('favorites_count')
            ->take(10)
            ->get();

        // ユーザーがお気に入りした商品
        $userFavorites = auth()->check()
            ? Item::whereIn('id', auth()->user()->favorites()->pluck('item_id'))->get()
            : collect();

        return view('items_index', compact('recommendedProducts', 'userFavorites'));
    }
}
