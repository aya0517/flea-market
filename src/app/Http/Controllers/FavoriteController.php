<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Favorite;
use App\Models\Item;

class FavoriteController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'item_id' => 'required|exists:items,id',
        ]);

        Favorite::firstOrCreate([
            'user_id' => Auth::id(),
            'item_id' => $request->item_id,
        ]);

        return back();
    }

    public function destroy($item_id)
    {
        Favorite::where('user_id', Auth::id())
            ->where('item_id', $item_id)
            ->delete();

        return back();
    }
}
