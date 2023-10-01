<?php

namespace App\Http\Controllers;

use App\Models\Optional;
use App\Models\Product;
use App\Models\Registration;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class CheckoutController extends Controller
{
    /**
     * Show the form to create a new blog post.
     */
    public function create(Request $request): RedirectResponse
    {
        \Stripe\Stripe::setApiKey(env("STRIPE_SECRET_KEY"));

        $line_items = array();

        $registration = Registration::find($request["reg"]);

        if ($registration->reservation) {
            $line_items = [["price" => "price_1NvL3BLnAzN3QPcU8FcaSorF", "quantity" => 1]];
        } else {
            $line_items = [["price" => "price_1NvL2CLnAzN3QPcUka5kMIwR", "quantity" => 1]];
        }

        // den högre summan ska betalas vid slutförande
        if ($request['completeregistration'] != null && boolval($request['completeregistration']) == true) {
            $line_items = [["price" => "price_1NvL2CLnAzN3QPcUka5kMIwR", "quantity" => 1]];
        }

        $optionals = Optional::where('registration_uid', $registration->registration_uid)->get();
        foreach ($optionals as $option) {
            $product = Product::find($option->productID);
            if ($product->price_id &&  !boolval($request['completeregistration'])) {
                array_push($line_items, array("price" => $product->price_id, "quantity" => 1));
            }
        }
        //behöver hantera cancel
        Session::put('finalreg', boolval($request['completeregistration']));
        Session::put('registration', $request["reg"]);

        $YOUR_DOMAIN = env("APP_URL");

        $checkout_session = \Stripe\Checkout\Session::create([
            'client_reference_id' => $registration->registration_uid,
            'line_items' => [$line_items],
            'mode' => 'payment',
            'metadata' => ["finalregistration" => boolval($request['completeregistration'])],
            'success_url' => $YOUR_DOMAIN . '/checkout/success',
            'cancel_url' => $YOUR_DOMAIN . '/checkout/cancel',
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
        // handle caneled final registration
        $final = Session::get('finalreg');
        if ($final) {
            $registration = Registration::find(Session::get("registration"));
            $registration->reservation = true;
            $registration->reservation_valid_until = '2023-12-31';
            $registration->save();
        } else {
            $registration = Registration::find(Session::get("registration"));
            if($registration){
                $registration->delete();
            }

        }
        return view('checkout.cancel'); // , compact('customer'));
    }
}
