<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class WebhookController extends Controller
{
    /**
     * Show the form to create a new blog post.
     */
    public function index(Request $request): RedirectResponse
    {
        // webhook.php
        //
        // Use this sample code to handle webhook events in your integration.
        //
        // 1) Paste this code into a new file (webhook.php)
        //
        // 2) Install dependencies
        //   composer require stripe/stripe-php
        //
        // 3) Run the server on http://localhost:4242
        //   php -S localhost:4242

        // require 'vendor/autoload.php';

        // The library needs to be configured with your account's secret key.
        // Ensure the key is kept out of any version control system you might be using.
        $stripe = new \Stripe\StripeClient('sk_test_...');

        // This is your Stripe CLI webhook secret for testing your endpoint locally.
        $endpoint_secret = 'whsec_01bc2acacc6c5b44abae2d1e6a792e512b1b62b16bed7159628f3448b2ee6165';

        $payload = @file_get_contents('php://input');
        $sig_header = $_SERVER['HTTP_STRIPE_SIGNATURE'];
        $event = null;

        try {
          $event = \Stripe\Webhook::constructEvent(
            $payload, $sig_header, $endpoint_secret
          );
        } catch(\UnexpectedValueException $e) {
          // Invalid payload
          http_response_code(400);
          exit();
        } catch(\Stripe\Exception\SignatureVerificationException $e) {
          // Invalid signature
          http_response_code(400);
          exit();
        }

        // Handle the event
        echo 'Received unknown event type ' . $event->type;

        http_response_code(200);
    }
}
