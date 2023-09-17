<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CheckoutController extends Controller
{
    /**
     * Show the form to create a new blog post.
     */
    public function create(Request $request): RedirectResponse
    {
      $stripeSecretKey = "";

      /*
       * Create products and prices
       *

      $stripe = new \Stripe\StripeClient($stripeSecretKey);
      $product = $stripe->products->create(['name' => 'Final Registration']);

      echo $product;

      // Preregistration prod_OZP9ItUvD9HgA2
      // Full registration prod_OZPCexqDhvEruR
      // Final registration prod_OZPENSPXjGGtFu  (Pre + Final = full registration)

      $price1 = $stripe->prices->create([
          'product' => 'prod_OZP9ItUvD9HgA2',
          'unit_amount' => 50000,
          'currency' => 'sek',
      ]);
      $price2 = $stripe->prices->create([
          'product' => 'prod_OZPENSPXjGGtFu',
          'unit_amount' => 250000,
          'currency' => 'sek',
      ]);
      $price3 = $stripe->prices->create([
          'product' => 'prod_OZPCexqDhvEruR',
          'unit_amount' => 300000,
          'currency' => 'sek',
      ]);

      // Pre price_1NmGUHAA4Elik9x6JFNpqv6A
      // Final: price_1NmGUIAA4Elik9x6aOWQNmSE
      // Full: price_1NmGUIAA4Elik9x6Yt4Z4vLM
      *
      */


        $stripeSecretKey = "sk_test_51NCDZ7AA4Elik9x6HuKE2aoJbXlSoHi9CQVz7xR25gFULK5m3oH4a0sMdspkGxyz8mWTct5en3visr0sX6dNnwOC00rgk9hVYD";
      \Stripe\Stripe::setApiKey($stripeSecretKey);

      $YOUR_DOMAIN = 'http://localhost:8082';

      $checkout_session = \Stripe\Checkout\Session::create([
          'line_items' => [[
              # Provide the exact Price ID (e.g. pr_1234) of the product you want to sell
              'price' => 'price_1NmGUHAA4Elik9x6JFNpqv6A',
              'quantity' => 1,
          ]],
          'mode' => 'payment',
          'success_url' => $YOUR_DOMAIN . '/checkout/success',
          'cancel_url' => $YOUR_DOMAIN . '/checkout/cancel',
      ]);

      return redirect($checkout_session->url);
    }

    public function index(Request $request)
    {
        return view('checkout.index'); // , compact('customer'));
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
