<?php

namespace App\Http\Controllers;

use App\Models\Optional;
use App\Models\Product;
use App\Models\Registration;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Stripe\Stripe;

class NoRegisterCheckoutController extends Controller
{

    public function create(Request $request): RedirectResponse {


        Log::debug("Sending payment reguest");

        $priceIDS = array();

        $priceIDS = $request["price_ids"];

        Stripe::setApiKey(env("STRIPE_SECRET_KEY"));

        $line_items = array();

        foreach ($priceIDS as $option) {
                array_push($line_items, array("price" => $option, "quantity" => 1));
        }

        $registration = "test";

        //behöver hantera cancel
        Session::put('registration', $request["reg"]);
        $YOUR_DOMAIN = env("APP_URL");
        $is_final = $request->boolean('is_final_registration_on_event') == true ? 'true' : 'false';
        $checkout_session = \Stripe\Checkout\Session::create([
            'client_reference_id' => "test",
            'line_items' => [$line_items],
            'mode' => 'payment',
            'metadata' => [
                'is_final_registration_on_event' => $is_final
            ],
            'allow_promotion_codes' => true,
            'success_url' => $YOUR_DOMAIN . '/checkout/success',
            'cancel_url' => $YOUR_DOMAIN . '/checkout/cancel?registration=' . $registration . '&is_final_registration_on_event=' . $is_final ,
        ]);

        return redirect($checkout_session->url);
    }

    //
}
