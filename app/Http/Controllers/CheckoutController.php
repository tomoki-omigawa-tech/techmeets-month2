<?php

namespace App\Http\Controllers;

use App\Models\Purchase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Stripe\Checkout\Session;
use Stripe\Stripe;

class CheckoutController extends Controller
{
    public function create(Request $request)
    {
        Stripe::setApiKey(config('services.stripe.secret'));

        $session = Session::create([
            'payment_method_types' => ['card'],
            'line_items' => [[
                'price_data' => [
                    'currency' => 'jpy',
                    'product_data' => ['name' => 'テスト商品'],
                    'unit_amount' => 1000, // 1,000円
                ],
                'quantity' => 1,
            ]],
            'mode' => 'payment',
            'success_url' => route('checkout.success') . '?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url' => route('checkout.cancel'),
        ]);

        return redirect($session->url);
    }

    public function success(Request $request)
    {
        Stripe::setApiKey(config('services.stripe.secret'));

        $sessionId = $request->query('session_id');

        if ($sessionId) {
            try {
                $checkoutSession = Session::retrieve($sessionId);

                // 二重保存防止：同じsession_idがまだ無ければ保存
                Purchase::firstOrCreate(
                    ['stripe_session_id' => $checkoutSession->id],
                    [
                        'user_id'      => auth()->id(),
                        'product_name' => 'テスト商品',
                        'amount'       => $checkoutSession->amount_total,
                        'status'       => 'completed',
                    ]
                );
            } catch (\Exception $e) {
                Log::error('購入履歴の保存に失敗: ' . $e->getMessage());
            }
        }

        return view('checkout.success');
    }

    public function cancel(Request $request)
    {
        return view('checkout.cancel');
    }
}
