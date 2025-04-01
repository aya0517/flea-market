<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Item;
use Illuminate\Support\Facades\Storage;

class ItemController extends Controller
{
    public function index(Request $request)
    {
        $category = $request->query('tab', 'recommended');
        $user = auth()->user();
        $search = $request->query('search');

        if ($category === 'recommended') {
            $items = Item::withCount('favorites')
                ->when($user, fn($query) => $query->where('user_id', '!=', $user->id))
                ->when($search, fn($query) => $query->where('name', 'like', "%$search%"))
                ->orderByDesc('favorites_count')
                ->paginate(10);
        } else {
            $items = $user
                ? $user->favorites()
                    ->when($search, fn($query) => $query->where('name', 'like', "%$search%"))
                    ->paginate(10)
                : collect();
        }

        return view('items_index', compact('items', 'category', 'search'));
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

        return redirect()->route('items.index');
    }
}
