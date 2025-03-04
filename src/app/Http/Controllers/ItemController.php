<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Item;

class ItemController extends Controller
{
    public function index(Request $request)
    {
        // クエリパラメータからタブのカテゴリを取得（デフォルト: おすすめ）
        $category = $request->query('category', 'recommended');

        // ログインユーザー情報を取得
        $user = auth()->user();

        // おすすめ商品の取得（自分の出品を除外して、いいね数順に並べる）
        $recommendedProducts = Item::withCount('favorites')
            ->when($user, function ($query) use ($user) {
                return $query->where('user_id', '!=', $user->id); // 自分の出品商品を除外
            })
            ->orderByDesc('favorites_count') // いいね数の降順
            ->take(10)
            ->get();

        // ログインユーザーがいいねした商品を取得（マイリスト）
        $userFavorites = $user
            ? Item::whereIn('id', $user->favorites()->pluck('item_id'))->get()
            : collect(); // 未ログインの場合は空のコレクションを返す

        return view('items_index', compact('recommendedProducts', 'userFavorites', 'category'));
    }
}

