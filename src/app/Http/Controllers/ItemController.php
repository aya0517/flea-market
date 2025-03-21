<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Item;
use Illuminate\Support\Facades\Storage;

class ItemController extends Controller
{
    public function index(Request $request)
    {
        $category = $request->query('tab', 'recommended'); // デフォルト: おすすめ
        $user = auth()->user();
        $search = $request->query('search');

        if ($category === 'recommended') {
            // おすすめ商品（自分の出品を除外、いいね数順）
            $products = Item::withCount('favorites')
                ->when($user, function ($query) use ($user) {
                    return $query->where('user_id', '!=', $user->id);
                })
                ->when($search, function ($query) use ($search) {
                    return $query->where('name', 'like', '%' . $search . '%');
                })
                ->orderByDesc('favorites_count')
                ->paginate(10); // ページネーション適用
        } else {
            // マイリスト（自分がいいねした商品）
            $products = $user
                ? Item::whereIn('id', $user->favorites()->pluck('item_id'))
                    ->when($search, function ($query) use ($search) {
                        return $query->where('name', 'like', '%' . $search . '%');
                    })
                    ->paginate(10)
                : collect();
        }

        return view('items_index', compact('products', 'category', 'search'));
    }

    public function detail($id)
    {
        $item = Item::with(['favorites', 'comments', 'condition', 'categories'])->findOrFail($id);

        $user = auth()->user();
        $isLiked = $user ? $item->isFavoritedBy($user) : false;

        return view('items_detail', compact('item', 'isLiked'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'brand' => 'nullable|string|max:255',
            'price' => 'required|integer',
            'description' => 'required|string',
            'condition_id' => 'nullable|exists:conditions,id',
            'image' => 'required|image|max:2048',
            'categories' => 'nullable|array',
            'categories.*' => 'exists:categories,id',
        ]);

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $fileName = time() . '_' . preg_replace('/\s+/', '_', $file->getClientOriginalName());
            $file->move(public_path('images/items'), $fileName);
            $imagePath = 'images/items/' . $fileName;
        }

        // 商品を保存
        $item = Item::create([
            'name' => $request->name,
            'brand' => $request->brand,
            'price' => $request->price,
            'description' => $request->description,
            'condition_id' => $request->condition_id,
            'image_path' => $imagePath ?? null,
            'user_id' => auth()->id(),
        ]);

        if ($request->has('categories')) {
            $item->categories()->sync($request->categories);
        }

        return redirect()->route('items.index')->with('success', '商品を出品しました！');
    }
}
