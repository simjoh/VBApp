<?php

namespace App\Http\Controllers;

use App\Models\Registration;
use App\Models\Optional;
use App\Models\Product;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\Log;

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
          $line_items = [["price" => "price_1NrHBYLnAzN3QPcUumT5kAA2", "quantity" => 1]];
      } else {
          $line_items = [["price" => "price_1NrHCELnAzN3QPcU6FPhBD8o", "quantity" => 1]];
      }

      $optionals = Optional::where('registration_uid', $registration->registration_uid)->get();
      foreach ($optionals as $option) {
          $product = Product::find($option->productID);
          if ($product->price_id) {
              array_push($line_items, array("price" => $product->price_id, "quantity" => 1));
          }
      }

      $YOUR_DOMAIN = env("APP_URL");

      $checkout_session = \Stripe\Checkout\Session::create([
          'client_reference_id' => $registration->registration_uid,
          'line_items' => [$line_items],
          'mode' => 'payment',
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
        return view('checkout.cancel'); // , compact('customer'));
    }
}
