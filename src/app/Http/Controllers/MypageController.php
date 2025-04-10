<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\UserProfile;
use App\Models\Item;
use App\Http\Requests\AddressRequest;
use App\Http\Requests\ProfileRequest;

class MypageController extends Controller
{
    public function edit()
    {
        $user = Auth::user();
        $userProfile = $user->profile;
        $purchasedItems = $userProfile ? $userProfile->purchasedItems : collect([]);

        return view('mypage.profile', compact('purchasedItems'));
    }

    public function update(AddressRequest $addressRequest, ProfileRequest $profileRequest)
    {
        $user = Auth::user();
        $addressValidated = $addressRequest->validated();
        $profileValidated = $profileRequest->validated();

        $validated = array_merge($addressValidated, $profileValidated);

        if ($addressRequest->hasFile('profile_image')) {
            $validated['profile_image'] = $addressRequest->file('profile_image')->store('profile_images', 'public');
        } else {
            $validated['profile_image'] = optional($user->profile)->profile_image;
        }

        $user->profile()->updateOrCreate(
            ['user_id' => $user->id],
            [
                'username'      => $validated['name'],
                'postal_code'   => $validated['postal_code'],
                'address'       => $validated['address'],
                'building_name' => $validated['building_name'],
                'profile_image' => $validated['profile_image'],
            ]
        );

        if ($user->first_login) {
            $user->update(['first_login' => false]);
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
