<?php

namespace App\Http\Controllers;

use App\Events\CompletedRegistrationSuccessEvent;
use App\Events\FailedPaymentEvent;
use App\Events\NonParticipantOrderSuccesEvent;
use App\Events\PreRegistrationSuccessEvent;
use App\Models\NonParticipantOptionals;
use App\Models\Order;
use App\Models\Registration;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
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
        } catch (\UnexpectedValueException $e) {
            // Invalid payload
            Log::debug("Stripe webhooks: Invalid payload");
            return response('', 400);
        } catch (\Stripe\Exception\SignatureVerificationException $e) {
            // Invalid signature
            Log::debug("Stripe webhooks: Invalid signature");
            return response('', 400);
        }

        switch ($event->type) {
            case 'checkout.session.completed':
                $session = $event->data->object;
                Log::debug("$session" . 'session.completed');

                // tex middagar mm
                if (filter_var($session->metadata->no_participant_order, FILTER_VALIDATE_BOOLEAN)) {
                    Log::debug('no_participant_order');
                    if ($this->nocostorder($session)) {

                    } else {
                        $this->create_non_participant_order($session);
                        $this->fullfill_non_participant_order($session);
                    }

                    break;
                }

                if ($this->nocostorder($session)) {
                    $this->create_no_cost_order($session);

                    $final_reg_payment = filter_var($session->metadata->is_final_registration_on_event, FILTER_VALIDATE_BOOLEAN);
                    if ($session->payment_status == 'paid') {
                        if ($final_reg_payment) {
                            $this->fulfill_no_cost_final_registration_order($session);
                        } else {
                            $this->fullfill_no_cost_order($session);
                        }
                    }
                } else {
                    $this->create_order($session);

                    $final_reg_payment = filter_var($session->metadata->is_final_registration_on_event, FILTER_VALIDATE_BOOLEAN);
                    if ($session->payment_status == 'paid') {
                        if ($final_reg_payment) {
                            $this->fulfill_final_registration_order($session);
                        } else {
                            $this->fullfill_order($session);
                        }
                    }
                }


                break;

            case 'checkout.session.async_payment_succeeded':
                $session = $event->data->object;
                Log::debug("$session" . 'async_payment_succeeded');

                // tex middagar mm
                if (filter_var($session->metadata->no_participant_order, FILTER_VALIDATE_BOOLEAN)) {
                    Log::debug('no_participant_order');
                    if ($this->nocostorder($session)) {

                    } else {
                        $this->fullfill_non_participant_order($session);
                    }
                    break;
                }


                if ($this->nocostorder($session)) {
                    $final_reg_payment = filter_var($session->metadata->is_final_registration_on_event, FILTER_VALIDATE_BOOLEAN);
                    if ($final_reg_payment) {
                        $this->fulfill_no_cost_final_registration_order($session);
                    } else {
                        $this->fullfill_no_cost_order($session);
                    }
                } else {
                    // Fulfill the purchase
                    $final_reg_payment = filter_var($session->metadata->is_final_registration_on_event, FILTER_VALIDATE_BOOLEAN);
                    if ($final_reg_payment) {
                        $this->fulfill_final_registration_order($session);
                    } else {
                        $this->fullfill_order($session);
                    }
                }

                break;

            case 'checkout.session.async_payment_failed':
                $session = $event->data->object;
                $this->failed_order($session);
                break;

            case 'checkout.session.expired':
                $session = $event->data->object;
                Log::debug("Stripe webhooks: Session expirered " . $session->client_reference_id);
                $this->session_expirered($session);
                break;
            case 'payment_intent.canceled':
                $session = $event->data->object;
                Log::debug("Stripe webhooks: cancel " . $session);
                break;

            default:
                Log::debug("Stripe webhooks: Received unknown event type");
        }

        return response('', 200);
    }

    // Create order, payment may still be pending
    private function create_order($session)
    {

        $registration = Registration::find($session->client_reference_id);
        Log::debug('creating order' . $registration);
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


    private function create_non_participant_order($session)
    {
        $registration = NonParticipantOptionals::find($session->client_reference_id);
        Log::debug('creating non participant order' . $registration);
        if (!$registration) {
            http_response_code(404);
            exit();
        }
        $order = new Order();
        $order->order_id = Uuid::uuid4();
        $order->registration_uid = $registration->optional_uid;
        $order->payment_intent_id = $session->payment_intent;
        $order->payment_status = $session->payment_status;
        $order->save();
    }

    private function create_no_cost_order(mixed $session)
    {
        $registration = Registration::find($session->client_reference_id);
        if (!$registration) {
            http_response_code(404);
            exit();
        }
        $order = new Order();
        $order->order_id = Uuid::uuid4();
        $order->registration_uid = $registration->registration_uid;
        $order->payment_intent_id = 'promotion_code';
        $order->payment_status = 'paid';
        $order->save();
    }

    // Set after payment fullfilled
    private function fullfill_order($session)
    {
        $order = Order::firstWhere('payment_intent_id', $session->payment_intent);
        $order->payment_status = $session->payment_status;
        $order->save();

        $registration = Registration::find($session->client_reference_id);
        $payment_intent = Session::get($session->payment_intent);
        if ($payment_intent == null) {
            if ($registration->reservation) {
                // Events to be triggered for an reservation
                event(new PreRegistrationSuccessEvent($registration));
            } else {
                // Events to be triggered for a full registration
                event(new CompletedRegistrationSuccessEvent($registration));
            }
        }
        Session::put($session->payment_intent, $session->payment_intent);
    }


    private function fullfill_non_participant_order($session)
    {
        $order = Order::firstWhere('payment_intent_id', $session->payment_intent);
        $order->payment_status = $session->payment_status;
        $order->save();

        $registration = NonParticipantOptionals::find($session->client_reference_id);
        $payment_intent = Session::get($session->payment_intent);
        if ($payment_intent == null) {
            Log::debug('Send NonParticipantOrderSuccesEvent');
            event(new NonParticipantOrderSuccesEvent($registration));
        }
        Session::put($session->payment_intent, $session->payment_intent);
    }

    // Om man har en kod som ger 0 i kostnad
    private function fullfill_no_cost_order(mixed $session)
    {
        $registration = Registration::find($session->client_reference_id);
        if ($registration->reservation) {
            // Events to be triggered for an reservation
            event(new PreRegistrationSuccessEvent($registration));
        } else {
            // Events to be triggered for a full registration
            event(new CompletedRegistrationSuccessEvent($registration));
        }
        Session::put($session->payment_intent, $session->payment_intent);
    }

    private function fulfill_final_registration_order($session)
    {
        $order = Order::firstWhere('payment_intent_id', $session->payment_intent);
        $order->payment_status = $session->payment_status;
        $order->save();
        $registration = Registration::find($session->client_reference_id);
        event(new CompletedRegistrationSuccessEvent($registration));
        Session::put($session->payment_intent, $session->payment_intent);
    }

    // Om man har en kod som ger 0 i kostnad
    private function fulfill_no_cost_final_registration_order(mixed $session)
    {
        $registration = Registration::find($session->client_reference_id);
        event(new CompletedRegistrationSuccessEvent($registration));
        Session::put($session->payment_intent, $session->payment_intent);
    }


    private function failed_order($session)
    {
        $order = Order::firstWhere('payment_intent_id', $session->payment_intent);
        $order->payment_status = $session->payment_status;
        $order->save();

        $registration = Registration::find($session->client_reference_id);

        // Send an email to the customer asking them to retry their order
        $this->email_customer_about_failed_payment($session, $registration);

    }

    private function session_expirered($session)
    {
        $registration = Registration::find($session->client_reference_id);
        $metadata = $session->metadata;
        Log::debug('Metadata: ' . $metadata);
    }


    private function email_customer_about_failed_payment($session, $registration)
    {
        $metadata = $session->metadata;
        event(new FailedPaymentEvent($registration->registration_uid, boolval($metadata->is_final_registration_on_event)));
    }

    private function nocostorder(mixed $session)
    {
        if ($session->payment_intent == null) {
            // kolla om vi har en full_discount
            if ($session->total_details->amount_discount > 0 && $session->amount_subtotal == $session->total_details->amount_discount) {
                Log::debug("This is a no cost order for registration " . $session->client_reference_id);
                return true;
            }
        }
        return false;

    }


}
