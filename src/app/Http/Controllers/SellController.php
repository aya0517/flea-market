<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Condition;
use App\Models\Item;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\ItemRequest;

class SellController extends Controller
{
    public function create()
    {
        $categories = Category::all();
        $conditions = Condition::all();

        return view('sell.create', compact('categories', 'conditions'));
    }

    public function store(ItemRequest $request)
    {
        $validated = $request->validated();

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $fileName = time() . '_' . preg_replace('/\s+/', '_', $file->getClientOriginalName());
            $imagePath = $file->storeAs('images/items', $fileName, 'public');
        }

        $item = new Item();
        $item->user_id = Auth::id();
        $item->name = $validated['name'];
        $item->brand = $request->brand;
        $item->description = $validated['description'];
        $item->price = $validated['price'];
        $item->condition_id = $validated['condition'];
        $item->image_path = $imagePath ?? null;
        $item->save();

        $item->categories()->attach($validated['categories']);

        return redirect()->route('mypage.index')->with('success', '商品を出品しました。');
    }
}
