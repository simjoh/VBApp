<?php

namespace App\Http\Controllers;

use App\Events\CanceledPaymentEvent;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Stripe\Stripe;

class NoRegisterCheckoutController extends Controller
{

    public function create(Request $request): RedirectResponse
    {
        $this->setApiKey();
        $priceIDS = $request["price_ids"];
        $line_items = array();

        $optional = $request['nonparticipantoptional'];
        foreach ($priceIDS as $option) {
            array_push($line_items, array("price" => $option, "quantity" => $request['quantity']));
        }
        $YOUR_DOMAIN = env("APP_URL");
        $checkout_session = \Stripe\Checkout\Session::create([
            'client_reference_id' => $optional,
            'line_items' => [$line_items],
            'mode' => 'payment',
            'metadata' => [
                'no_participant_order' => true,
                'event_type' => 'MSR'
            ],
            'allow_promotion_codes' => true,
            'success_url' => $YOUR_DOMAIN . '/public/optionals/checkout/success?event_type=MSR',
            'cancel_url' => $YOUR_DOMAIN . '/public/optionals/checkout/cancel?nonpaticipantoptional=' . $optional . '&no_participant_order=' . 'true',
        ]);
        return redirect($checkout_session->url);
    }

    public function success(Request $request)
    {
        return view('checkout.success', ['message' => 'Thank you for your resevation', 'checkemailmessage' => 'Please check that you have received an email. If not then check your spam folder and if found there, please change your spam filter settings for the address "info@midnightsunrandonnee.se" so you will not miss future emails.']); // , compact('customer'));
    }

    public function cancel(Request $request)
    {
        event(new CanceledPaymentEvent($request->query('nonpaticipantoptional'), false, true));
        return view('checkout.cancel', ['message' => 'You have caneled payment']);
    }

    private function setApiKey()
    {
        Stripe::setApiKey(env("STRIPE_SECRET_KEY"));
    }
}
