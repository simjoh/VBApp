<?php

namespace App\Http\Controllers;

use App\Models\Contactinformation;
use App\Models\Event;
use App\Models\Person;
use App\Models\Product;
use App\Models\Registration;
use Illuminate\Http\Request;


//används för fristående beställningar frikopplade från en registering på ett event
class OrderController extends Controller
{
    public function index(Request $request)
    {
        $requestedform = $request->formname;
        $event = Event::find($request['uid']);
        return view('orderform.' . $requestedform, ['event' => $event, 'dinnerproduct' => 1006]);
    }


    public function placeorder(Request $request)
    {

        $requestedform = $request->formname;
        $event = Event::find($request['uid']);

        $contacts = Contactinformation::where('email', $request['email'])->get();
        $priceIds = Product::where('productID',$request['save'])->pluck('price_id');
        $person = Person::find($contacts->person_person_uid);

        if(!$person){
            // kan vara en deltagare men inte säkert ändå

        } else {
            // är det en deltagare
           $registration =  Registration::where('person_uid', $contacts->person_person_uid)->where('course_uid', $event->event_uid);

        }

        if(!$priceIds->count() > 0){
            return back()->withErrors(['same' => 'tets'])->withInput();
        }

        return to_route('noregistercheckout', ['reg' => $contacts, 'price_ids' => $priceIds->items(), 'noregistrationOrder' => true]);

    }
}
