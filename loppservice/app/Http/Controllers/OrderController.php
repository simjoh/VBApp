<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\NonParticipantOptionals;
use App\Models\Product;
use Illuminate\Http\Request;


//används för fristående beställningar frikopplade från en registering på ett event
class OrderController extends Controller
{
    public function index(Request $request)
    {
        $requestedform = $request->formname;
        $event = Event::find($request['uid']);
//       dd($request);
        return view('orderform.' . $requestedform, ['event' => $event, 'dinnerproduct' => 1006]);
    }

    public function placeorder(Request $request)
    {
        $reg_uid = "";
        $requestedform = $request->formname;
        $priceIds = Product::where('productID', $request['save'])->pluck('price_id');
        $course_uid = $request['course_uid'];
        $product = $request->input('save');
        if ($product) {
            $optional = new NonParticipantOptionals();
            $optional->course_uid = $course_uid;
            $optional->productID = $product;
            $optional->firstname = $request['first_name'];
            $optional->surname = $request['last_name'];
            $optional->email = $request['email'];
            $optional->quantity = $request['quantity'];
            $optional->additional_information = $request['extra-info'];
            $optional->save();
        }
        if (!$priceIds->count() > 0) {
            return back()->withErrors(['same' => 'tets'])->withInput();
        }
        return to_route('noregistercheckout', ['registration_uid' => $reg_uid, 'nonparticipantoptional' => $optional->optional_uid, 'price_ids' => $priceIds->toArray(), 'noregistrationOrder' => true, 'quantity' => $request['quantity']]);
    }
}
