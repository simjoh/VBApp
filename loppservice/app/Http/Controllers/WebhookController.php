<?php

namespace App\Http\Controllers;

use App\Events\CompletedRegistrationSuccessEvent;
use App\Events\PreRegistrationSuccessEvent;
use App\Models\Order;
use App\Models\Registration;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\Log;
use Ramsey\Uuid\Uuid;

class WebhookController extends Controller
{
    /**
     * Show the form to create a new blog post.
     */
    public function index()
    {

        $stripe = new \Stripe\StripeClient(env("STRIPE_SECRET_KEY"));

        // This is your Stripe CLI webhook secret for testing your endpoint locally.
        $endpoint_secret = env("STRIPE_CLI_WEBHOOK_SECRET");

        $payload = @file_get_contents('php://input');
        $sig_header = $_SERVER['HTTP_STRIPE_SIGNATURE'];
        $event = null;

        try {
          $event = \Stripe\Webhook::constructEvent(
            $payload, $sig_header, $endpoint_secret
          );
        } catch(\UnexpectedValueException $e) {
          // Invalid payload
            Log::debug("Stripe webhooks: Invalid payload");
          return respons('', 400);
        } catch(\Stripe\Exception\SignatureVerificationException $e) {
          // Invalid signature
            Log::debug("Stripe webhooks: Invalid signature");
          return respons('', 400);
        }

        switch ($event->type) {
        case 'checkout.session.completed':
            $session = $event->data->object;

            $this->create_order($session);

            if ($session->payment_status == 'paid') {
                $this->fullfill_order($session);
            }

        case 'checkout.session.async_payment_succeeded':
            $session = $event->data->object;

            // Fulfill the purchase
            $this->fulfill_order($session);

            break;

        case 'checkout.session.async_payment_failed':
            $session = $event->data->object;

            $this->failed_order($session);
            break;

        default:
            Log::debug("Stripe webhooks: Received unknown event type");
        }

        return response('', 200);
    }

    // Create order, payment may still be pending
    private function create_order($session) {
        $registration = Registration::find($session->client_reference_id);

        if (!$registration) {
            http_response_code(404);
            exit();
        }

        $order = new Order();
        $order->order_id = Uuid::uuid4();
        $order->registration_uid = $registration->registration_uid;
        $order->payment_intent_id = $session->payment_intent;
        $order->payment_status = $session->payment_status;
        $order->save();
    }

    // Set after payment fullfilled
    private function fullfill_order($session) {
        $order = Order::firstWhere('payment_intent_id', $session->payment_intent);
        $order->payment_status = $session->payment_status;
        $order->save();
        
        $registration = Registration::find($session->client_reference_id);
        $user = Registration::find($session->client_reference_id)->get()->first();

        if ($registration->reservation) {
            // Events to be triggered for an reservation
            event(new PreRegistrationSuccessEvent($user));
        } else {
            // Events to be triggered for a full registration
            event(new CompletedRegistrationSuccessEvent($user));
        }
    }

    private function failed_order($session) {
        $order = Order::firstWhere('payment_intent_id', $session->payment_intent);
        $order->payment_status = $session->payment_status;
        $order->save();

        $registration = Registration::find($session->client_reference_id);

        // Send an email to the customer asking them to retry their order
        email_customer_about_failed_payment($session, $registration);

    }

    private function email_customer_about_failed_payment($session, $registration) {
    }
}
