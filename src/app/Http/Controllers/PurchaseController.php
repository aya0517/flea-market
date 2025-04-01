<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UserProfile;
use App\Models\Item;
use Stripe\Stripe;
use Stripe\Checkout\Session;
use Illuminate\Support\Facades\Auth;

class PurchaseController extends Controller
{
    // 商品購入画面の表示
    public function show($item_id)
    {
        $item = Item::findOrFail($item_id);

        $userProfile = auth()->user()->profile;
        $postal_code = $userProfile ? $userProfile->postal_code : '郵便番号が登録されていません';
        $shipping_address = $userProfile ? ($userProfile->address . ' ' . $userProfile->building_name) : '住所が登録されていません';

        return view('purchase.show', compact('item', 'postal_code', 'shipping_address'));
    }

    // 決済処理
    public function processPayment(Request $request)
    {
        Stripe::setApiKey(env('STRIPE_SECRET'));

        $item = Item::findOrFail($request->item_id);

        if ($item->is_sold) {
            return redirect()->back()->with('error', 'この商品は既に購入されています。');
        }

        $paymentMethod = $request->payment_method;

        $checkoutSession = Session::create([
            'payment_method_types' => $paymentMethod === 'card' ? ['card'] : ['konbini'],
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
            'success_url' => route('purchase.success', ['item_id' => $item->id]),
            'cancel_url' => route('purchase.cancel'),
        ]);

        return redirect($checkoutSession->url);
    }

    // 決済成功後の処理
    public function paymentSuccess(Request $request)
    {
        $item = Item::findOrFail($request->item_id);

        if ($item->is_sold) {
            return redirect()->route('items.index');
        }

        // 購入者情報を更新
        $item->update([
            'is_sold' => true,
            'buyer_id' => Auth::id()
        ]);

        return redirect()->route('items.detail', ['item' => $item_id]);
    }

    // 決済キャンセル
    public function paymentCancel()
    {
        return view('purchase.cancel');
    }

    // 住所変更画面の表示
    public function editAddress($item_id)
    {
        $item = Item::findOrFail($item_id);
        $userProfile = UserProfile::where('user_id', Auth::id())->first();

        return view('purchase.address_edit', compact('item', 'userProfile'));
    }

    // 住所を更新する処理
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
