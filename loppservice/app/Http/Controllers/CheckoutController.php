<?php

namespace App\Http\Controllers;

use App\Events\CanceledPaymentEvent;
use App\Models\Optional;
use App\Models\Product;
use App\Models\Registration;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

class CheckoutController extends Controller
{
    /**
     * Show the form to create a new blog post.
     */
    public function create(Request $request): RedirectResponse
    {
        Log::debug("Sending payment reguest" . $request["reg"]);


        \Stripe\Stripe::setApiKey(env("STRIPE_SECRET_KEY"));

        $line_items = array();

         $registration = Registration::find($request["reg"]);

         $line_items = [["price" => $request->price_id, "quantity" => 1]];

        if ($request['is_final_registration_on_event'] != null && $request->boolean('is_final_registration_on_event')) {
            Log::debug("Sending final registration payment reguest for " . $request["reg"]);
            $line_items = [["price" => $request->price_id, "quantity" => 1]];
        }

//        if ($registration->reservation) {
//            Log::debug("Sending reservation payment reguest for " . $request["reg"]);
//            $line_items = [["price" => "price_1NvL3BLnAzN3QPcU8FcaSorF", "quantity" => 1]];
//        } else {
//            Log::debug("Sending registration payment reguest for " . $request["reg"]);
//            $line_items = [["price" => "price_1NvL2CLnAzN3QPcUka5kMIwR", "quantity" => 1]];
//        }
//
//        // den högre summan ska betalas vid slutförande
//        if ($request['is_final_registration_on_event'] != null && $request->boolean('is_final_registration_on_event')) {
//            Log::debug("Sending final registration payment reguest for " . $request["reg"]);
//            $line_items = [["price" => "price_1NvL2CLnAzN3QPcUka5kMIwR", "quantity" => 1]];
//        }

//        if(!App::isProduction()){
//            $line_items = [["price" => env("STRIPE_TEST_PRODUCT"), "quantity" => 1]];
//            if(!$request->boolean('is_final_registration_on_event')){
//                array_push($line_items, array('price' => env('STRIPE_TEST_PRODUCT_JERSEY'),"quantity" => 1));
//            }
//        }

        $optionals = Optional::where('registration_uid', $registration->registration_uid)->get();
        foreach ($optionals as $option) {
            $product = Product::find($option->productID);
            if ($product->price_id && !$request->boolean('is_final_registration_on_event')) {
                array_push($line_items, array("price" => $product->price_id, "quantity" => 1));
            }
        }
        //behöver hantera cancel
        Session::put('registration', $request["reg"]);
        $YOUR_DOMAIN = env("APP_URL");
        $is_final = $request->boolean('is_final_registration_on_event') == true ? 'true' : 'false';
        $checkout_session = \Stripe\Checkout\Session::create([
            'client_reference_id' => $registration->registration_uid,
            'line_items' => [$line_items],
            'mode' => 'payment',
            'metadata' => [
                'is_final_registration_on_event' => $is_final
            ],
            'allow_promotion_codes' => true,
            'success_url' => $YOUR_DOMAIN . '/checkout/success',
            'cancel_url' => $YOUR_DOMAIN . '/checkout/cancel?registration=' . $registration->registration_uid . '&is_final_registration_on_event=' . $is_final ,
        ]);

        return redirect($checkout_session->url);
    }

    public function index(Request $request)
    {
        $registration = Registration::find($request["reg"]);
        return view('checkout.index')->with(['registration' => $registration]);
    }

    public function success(Request $request)
    {
        return view('checkout.success'); // , compact('customer'));
    }

    public function cancel(Request $request)
    {
        event(new CanceledPaymentEvent($request->query('registration'), $request->boolean('is_final_registration_on_event')));
        return view('checkout.cancel');
    }
}
