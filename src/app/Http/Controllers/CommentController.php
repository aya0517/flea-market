<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Item;
use App\Models\Comment;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class CommentController extends Controller
{

    public function store(Request $request, Item $item)
    {

        $validator = Validator::make($request->all(), [
            'content' => ['required', 'string', 'max:255'],
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }


        Comment::create([
            'user_id' => Auth::id(),
            'item_id' => $item->id,
            'content' => $request->content,
        ]);

        return redirect()->route('items.show', $item->id)->with('success', 'コメントを投稿しました！');
    }
}
