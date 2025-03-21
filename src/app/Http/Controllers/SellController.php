<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Condition;
use App\Models\Item;
use Illuminate\Support\Facades\Auth;

class SellController extends Controller
{
    public function create()
    {
        $categories = Category::all();
        $conditions = Condition::all();

        return view('sell.create', compact('categories', 'conditions'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'image' => 'required|image|max:2048',
            'name' => 'required|string|max:255',
            'brand' => 'nullable|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:1',
            'condition_id' => 'required|exists:conditions,id',
            'categories' => 'required|array',
            'categories.*' => 'exists:categories,id',
        ]);

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $fileName = time() . '_' . preg_replace('/\s+/', '_', $file->getClientOriginalName());
            $file->move(public_path('images/items'), $fileName);
            $imagePath = 'images/items/' . $fileName;
        }

        $item = new Item();
        $item->user_id = Auth::id();
        $item->name = $request->name;
        $item->brand = $request->brand;
        $item->description = $request->description;
        $item->price = $request->price;
        $item->condition_id = $request->condition_id;
        $item->image_path = $imagePath ?? null;
        $item->save();

        $item->categories()->attach($request->categories);

        return redirect()->route('mypage.index');
    }
}
