<?php

namespace App\Http\Controllers;

use App\Http\Requests\PurchaseRequest;
use Illuminate\Http\Request;
use App\Models\UserProfile;
use App\Models\Item;
use Stripe\Stripe;
use Stripe\Checkout\Session;
use Illuminate\Support\Facades\Auth;

class PurchaseController extends Controller
{
    public function show($item_id)
    {
        $item = Item::findOrFail($item_id);

        $userProfile = auth()->user()->profile;
        $postal_code = $userProfile ? $userProfile->postal_code : '郵便番号が登録されていません';
        $shipping_address = $userProfile ? ($userProfile->address . ' ' . $userProfile->building_name) : '住所が登録されていません';

        return view('purchase.show', compact('item', 'postal_code', 'shipping_address'));
    }

    public function processPayment(PurchaseRequest $request)
    {
        $validated = $request->validated();

        Stripe::setApiKey(env('STRIPE_SECRET'));

        $item = Item::findOrFail($validated['item_id']);

        if ($item->is_sold) {
            return redirect()->route('purchase.show', ['item_id' => $item->id])
                ->with('error', 'この商品は既に購入されています。');
        }

        $paymentMethod = $validated['payment_method'];

        if ($paymentMethod === 'konbini') {
            $item->update([
                'is_sold' => true,
                'buyer_id' => Auth::id()
            ]);

            return redirect()->route('purchase.show', ['item_id' => $item->id])
                ->with('success', '購入が成功しました。');
        }

        $checkoutSession = Session::create([
            'payment_method_types' => ['card'],
            'line_items' => [[
                'price_data' => [
                    'currency' => 'jpy',
                    'product_data' => [
                        'name' => $item->name,
                    ],
                    'unit_amount' => $item->price,
                ],
                'quantity' => 1,
            ]],
            'mode' => 'payment',
            'success_url' => route('purchase.show', ['item_id' => $item->id]),
            'cancel_url' => route('purchase.cancel', ['item_id' => $item->id]),
        ]);

        return redirect($checkoutSession->url);
    }

    public function paymentSuccess(Request $request)
    {
        $item = Item::findOrFail($request->item_id);

        if ($item->is_sold) {
            return redirect()->route('items.index');
        }

        $item->update([
            'is_sold' => true,
            'buyer_id' => Auth::id()
        ]);

        return redirect()->route('items.detail',['item' => $item->id]);
    }

    public function editAddress($item_id)
    {
        $item = Item::findOrFail($item_id);
        $userProfile = UserProfile::where('user_id', Auth::id())->first();

        return view('purchase.address_edit', compact('item', 'userProfile'));
    }

    public function updateAddress(Request $request, $item_id)
    {
        $request->validate([
            'postal_code' => 'required|max:10',
            'address' => 'required|string|max:255',
            'building_name' => 'nullable|string|max:255',
        ]);

        $userProfile = UserProfile::updateOrCreate(
            ['user_id' => Auth::id()],
            [
                'postal_code' => $request->postal_code,
                'address' => $request->address,
                'building_name' => $request->building_name
            ]
        );

        return redirect()->route('purchase.show', ['item_id' => $item_id]);
    }
}
