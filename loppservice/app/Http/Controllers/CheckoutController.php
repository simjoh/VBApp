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


        $optionals = Optional::where('registration_uid', $registration->registration_uid)->get();

        if (!App::isProduction()) {
            foreach ($optionals as $option) {
                $product = Product::find($option->productID);

                if ($product->categoryID == 1) {
                    array_push($line_items, array('price' => env('STRIPE_TEST_PRODUCT_JERSEY'), "quantity" => 1));
                }

                if ($product->categoryID == 2) {
                    array_push($line_items, array('price' => 'price_1ORZvfLnAzN3QPcUjEIDAfvB', "quantity" => 1));
                }
            }
        }



        foreach ($optionals as $option) {
            $product = Product::find($option->productID);
            if ($product->price_id) {
                // Skip category 1 products when not in production
                if (App::isProduction()) {
                    array_push($line_items, array("price" => $product->price_id, "quantity" => 1));
                }
            }
        }


        //behöver hantera cancel
        Session::put('registration', $request["reg"]);
        $YOUR_DOMAIN = env("APP_URL");
        $is_final = $request->boolean('is_final_registration_on_event') == true ? 'true' : 'false';

        // Conditionally add /public only in production
        $public_path = App::isProduction() ? '/public' : '';

        $checkout_session = \Stripe\Checkout\Session::create([
            'client_reference_id' => $registration->registration_uid,
            'line_items' => [$line_items],
            'mode' => 'payment',
            'metadata' => [
                'is_final_registration_on_event' => $is_final,
                'event_type' => $request["event_type"]
            ],
            'allow_promotion_codes' => true,
            'success_url' => $YOUR_DOMAIN . $public_path . '/checkout/success?event_type=' . $request["event_type"],
            'cancel_url' => $YOUR_DOMAIN . $public_path . '/checkout/cancel?registration=' . $registration->registration_uid . '&is_final_registration_on_event=' . $is_final . '&event_type=' . $request['event_type'],
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
        if ($request['event_type'] === 'MSR') {
            return view('checkout.success', ['message' => 'Thank you for your registration/reservation. We have sent a confirmation by email to the address you provided in the registration form.', 'checkemailmessage' => 'Please check that you have received an email. If not then check your spam folder and if found there, please change your spam filter settings for the address "info@midnightsunrandonnee.se" so you will not miss future emails.']); // , compact('customer'));
        } else {
            return view('checkout.brmsuccess', ['message' => 'Tack för din anmälan. Ett bekräftelsemail har skickats till den epostadress du angav i anmälningsformuläret.', 'checkemailmessage' => 'Kontrollera att du fått ett mail med uppgifter om din anmälan. Om inte kontrollera om mailet hamnat i skräpposten.']);
        }
    }

    public function cancel(Request $request)
    {
        event(new CanceledPaymentEvent($request->query('registration'), $request->boolean('is_final_registration_on_event'), false, $request['event_type']));
        return view('checkout.cancel', ['message' => 'You have caneled payment of your registration/reservation']);
    }
}
