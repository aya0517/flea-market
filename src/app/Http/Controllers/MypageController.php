<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\UserProfile;
use App\Models\Item;

class MypageController extends Controller
{
    public function edit()
    {
        $user = Auth::user();
        $userProfile = $user->profile;
        $purchasedItems = $userProfile ? $userProfile->purchasedItems : collect([]);

        return view('mypage.profile', compact('purchasedItems'));
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'profile_image' => 'nullable|image|max:2048',
            'name' => 'required|string|max:255',
            'postal_code' => 'nullable|regex:/^\d{3}-?\d{4}$/',
            'address' => 'nullable|string|max:255',
            'building' => 'nullable|string|max:255',
        ]);

        if ($request->hasFile('profile_image')) {
            $path = $request->file('profile_image')->store('profile_images', 'public');
            $validated['profile_image'] = $path;
        }

        $user->profile()->updateOrCreate(
            ['user_id' => $user->id],
            [
                'username' => $validated['name'],
                'postal_code' => $validated['postal_code'],
                'address' => $validated['address'],
                'building_name' => $validated['building'],
                'profile_image' => $validated['profile_image'] ?? optional($user->profile)->profile_image,
            ]
        );

        if ($user->first_login) {
            $user->first_login = false;
            $user->save();

            Auth::setUser($user);
            session()->regenerate();

            return redirect('/');
        }

        return redirect()->route('mypage.profile.edit');
    }

    public function index(Request $request)
    {
        $user = Auth::user();
        $tab = $request->query('page', 'buy');

        if ($tab === 'buy') {
            $items = Item::where('buyer_id', $user->id)->get();
        } elseif ($tab === 'sell') {
            $items = Item::where('user_id', $user->id)->get();
        } else {
            return redirect()->route('mypage.index', ['page' => 'buy']);
        }

        return view('mypage.index', compact('user', 'items', 'tab'));
    }
}
