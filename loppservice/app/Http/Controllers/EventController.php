<?php

namespace App\Http\Controllers;

use App\Http\Resources\EventResource;
use App\Models\Event;
use App\Models\EventConfiguration;
use App\Models\Product;
use App\Models\Reservationconfig;
use App\Models\StartNumberConfig;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Ramsey\Uuid\Nonstandard\Uuid;
use Illuminate\Support\Facades\Log;


class EventController extends Controller
{

    public function index(Request $request)
    {
        // Load events with their organizer relationship
        $events = Event::with('organizer')
            ->where('event_type', 'BRM')
            ->get()
            ->sortBy("startdate");

        // Debug: Check if organizer data is present
        foreach ($events as $event) {
            $event->startlisturl = env("APP_URL") . '/startlist/event/' . $event->event_uid . '/showall';
            Log::debug('Event: ' . $event->title . ', Organizer Data: ', [
                'organizer_id' => $event->organizer_id,
                'has_organizer' => $event->organizer ? 'Yes' : 'No',
                'organizer_name' => $event->organizer ? $event->organizer->organization_name : 'None',
                'has_logo' => $event->organizer && $event->organizer->logo_svg ? 'Yes' : 'No',
                'logo_length' => $event->organizer && $event->organizer->logo_svg ? strlen($event->organizer->logo_svg) : 0
            ]);
        }

        $events = $events->groupBy(function ($val) {
            $date = Carbon::parse($val->startdate);
            $months = Config::get('app.swedish_month');
            return $months[$date->format('m')] . " " . $date->format('Y');
        });
        return view('event.show')->with(['allevents' => $events]);
    }

    public function all()
    {
        $events = Event::all();
        if ($events) {
            return response()->json($events, 200);
        }
        return response()->json($events, 204);

    }

    public function create(Request $request)
    {
        $jsonfromfrom = $request->json()->all();

        if (Event::where('title', $jsonfromfrom['title'])->exists()) {
            return response()->json("alreadyexists", 500);
        }

        $event = new Event();
        $event->event_uid = Uuid::uuid4();
        $event->title = $jsonfromfrom['title'];
        $event->description = $jsonfromfrom['description'];
        $event->startdate = $jsonfromfrom['startdate'];
        $event->enddate = $jsonfromfrom['enddate'];
        $event->completed = false;
        $event->save();

        $eventconfiguration = new EventConfiguration();
        $config = $jsonfromfrom['eventconfig'];
        $eventconfiguration->registration_opens = $config['registration_opens'];
        $eventconfiguration->registration_closes = $config['registration_closes'];
        $eventconfiguration->resarvation_on_event = false;
        $eventconfiguration->max_registrations = $config['max_registrations'];
        $event->eventconfiguration()->save($eventconfiguration);

        $startnumberfrom = $config['startnumberconfig'];
        $startnumberconfig = new StartNumberConfig();
        $startnumberconfig->begins_at = $startnumberfrom['begins_at'];
        $startnumberconfig->ends_at = $startnumberfrom['ends_at'];
        $startnumberconfig->increments = $startnumberfrom['increment'];
        $eventconfiguration->startnumberconfig()->save($startnumberconfig);

        $products = $jsonfromfrom['products'];

        foreach ($products as $product) {
            if (Product::where('productID', $product)->exists()) {
                $eventconfiguration->products()->save((Product::where('productID', '=', $product)->firstOrFail()));
            }
        }

        if (filter_var($config['reservation_on_event'], FILTER_VALIDATE_BOOLEAN) === true) {
            $reservationconfig = new Reservationconfig();
            $reservationconfig->use_reservation_until = null;
            $reservationconfig->use_reservation_on_event = false;
            $eventconfiguration->reservationconfig()->save($reservationconfig);
        }

        return new EventResource($event);

    }

    public function update(Request $request)
    {
        $jsonfromfrom = $request->json()->all();

        $eventtoupdate = Event::where('event_uid', '=', $jsonfromfrom['event_uid'])->first();
        $eventtoupdate->title = $jsonfromfrom['title'];
        $eventtoupdate->description = $jsonfromfrom['description'];
        $eventtoupdate->startdate = $jsonfromfrom['startdate'];
        $eventtoupdate->enddate = $jsonfromfrom['enddate'];
        $eventtoupdate->completed = false;

        $eventconfig = $eventtoupdate->eventconfiguration;
        $config = $jsonfromfrom['eventconfig'];
        $eventtoupdate->registration_opens = $config['registration_opens'];
        $eventconfig->registration_closes = $config['registration_closes'];
        $eventconfig->resarvation_on_event = false;
        $eventconfig->max_registrations = $config['max_registrations'];


        $startnumberfrom = $config['startnumberconfig'];
        $startnumberconfig = $eventconfig->startnumberconfig;
        $startnumberconfig->begins_at = $startnumberfrom['begins_at'];
        $startnumberconfig->ends_at = $startnumberfrom['ends_at'];
        $startnumberconfig->increments = $startnumberfrom['increment'];
        $eventconfig->startnumberconfig()->update();

        if (filter_var($eventconfig['reservation_on_event'], FILTER_VALIDATE_BOOLEAN) === true) {
            $reservationconfig = $config['reservationconfig'];
            $reservationconfig->use_reservation_until = null;
            $reservationconfig->use_reservation_on_event = false;
            $eventconfig->reservationconfig->update();
        }
        $eventtoupdate->eventconfiguration()->update();
        return new EventResource($eventtoupdate);

    }

    public function delete(Request $request): JsonResponse
    {
        $event = Event::where('event_uid', '=', $request['event_uid'])->first();
        if ($event) {
            $event->delete();
        }
        return response()->json(null, 204);
    }


    public function eventbyid(Request $request): JsonResponse
    {
        $event = Event::where('event_uid', '=', $request['event_uid'])->first();
        if ($event) {
            return response()->json($event, 200);
        }
        return response()->json(null, 204);
    }

    public function getLogin(Request $request)
    {
        $eventType = $request->query('event_type');
        $event = Event::where('event_uid', '=', $request['uid'])->first();

//        if (!$event) {
//            return response()->json(['message' => 'Event not found'], 404);
//        }

        // TODO: Implement login functionality based on event type
        return response()->json([
            'message' => 'Not implemented yet',
            'event_type' => $eventType,
            'event' => $event
        ], 501);
    }


}

