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
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Ramsey\Uuid\Nonstandard\Uuid;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;


class EventController extends Controller
{

    public function index(Request $request)
    {
        // Load events with their organizer relationship
        $events = Event::with(['organizer', 'routeDetail'])
            ->where('event_type', 'BRM')
            ->get()
            ->sortBy("startdate");

        // Check if we're in development environment
        $isDevEnvironment = \in_array(\env('APP_ENV'), ['local', 'development']);

        // Debug: Check if organizer data is present
        foreach ($events as $event) {
            // Create a dynamic property for the startlist URL
            $event->setAttribute('startlisturl', \env("APP_URL") . '/startlist/event/' . $event->event_uid . '/showall');

            // Format dates in Swedish
            $startDate = Carbon::parse($event->startdate);
            $months = Config::get('app.swedish_month');

            // Format start date (e.g., "10 Maj")
            $event->setAttribute('formatted_start_date', $startDate->format('j') . ' ' . $months[$startDate->format('m')]);

            // Format registration closing date if available
            if ($event->eventconfiguration && $event->eventconfiguration->registration_closes) {
                $closingDate = Carbon::parse($event->eventconfiguration->registration_closes);
                $event->setAttribute('formatted_closing_date', $closingDate->format('j') . ' ' . $months[$closingDate->format('m')]);
            } else {
                $event->setAttribute('formatted_closing_date', '10 Maj'); // Default value
            }
        }

        $events = $events->groupBy(function ($val) {
            $date = Carbon::parse($val->startdate);
            $months = Config::get('app.swedish_month');
            return $months[$date->format('m')] . " " . $date->format('Y');
        });
        return \view('event.show')->with(['allevents' => $events]);
    }

    public function all()
    {
        $events = Event::all();
        if ($events) {
            return \response()->json($events, 200);
        }
        return \response()->json($events, 204);

    }

    public function create(Request $request)
    {
        $jsonfromfrom = $request->json()->all();

        if (Event::where('title', $jsonfromfrom['title'])->exists()) {
            return \response()->json("alreadyexists", 500);
        }

        $event = new Event();
        $event->event_uid = Uuid::uuid4();
        $event->title = $jsonfromfrom['title'];
        $event->description = $jsonfromfrom['description'];
        $event->startdate = $jsonfromfrom['startdate'];
        $event->enddate = $jsonfromfrom['enddate'];
        $event->completed = false;

        // Set event_type if provided, default to env value or BRM
        $event->event_type = $jsonfromfrom['event_type'] ?? env('EVENT_DEFAULT_TYPE', 'BRM');

        // Set organizer_id if provided
        if (isset($jsonfromfrom['organizer_id'])) {
            $event->organizer_id = $jsonfromfrom['organizer_id'];
        }

        // Set county_id if provided
        if (isset($jsonfromfrom['county_id'])) {
            $event->county_id = $jsonfromfrom['county_id'];
        }

        // Set event_group_uid if provided
        if (isset($jsonfromfrom['event_group_uid'])) {
            $event->event_group_uid = $jsonfromfrom['event_group_uid'];
        }

        $event->save();

        $eventconfiguration = new EventConfiguration();
        $config = $jsonfromfrom['eventconfig'];
        $eventconfiguration->registration_opens = $config['registration_opens'];

        // Set registration_closes to 23:59 the day before event if not provided
        if (isset($config['registration_closes'])) {
            $eventconfiguration->registration_closes = $config['registration_closes'];
        } else {
            // Always set to 23:59 the day before event starts
            $eventStartDate = Carbon::parse($jsonfromfrom['startdate']);
            $dayBefore = $eventStartDate->copy()->subDay()->format('Y-m-d') . ' 23:59:00';
            $eventconfiguration->registration_closes = $dayBefore;
        }

        $eventconfiguration->resarvation_on_event = false;

        // Set max_registrations to env default if not provided
        if (isset($config['max_registrations'])) {
            $eventconfiguration->max_registrations = $config['max_registrations'];
        } else {
            $eventconfiguration->max_registrations = env('EVENT_DEFAULT_MAX_REGISTRATIONS', 300);
        }

        $event->eventconfiguration()->save($eventconfiguration);

        $startnumberfrom = $config['startnumberconfig'];
        $startnumberconfig = new StartNumberConfig();
        $startnumberconfig->begins_at = $startnumberfrom['begins_at'];
        $startnumberconfig->ends_at = $startnumberfrom['ends_at'];
        $startnumberconfig->increments = $startnumberfrom['increment'];
        $eventconfiguration->startnumberconfig()->save($startnumberconfig);

        // Handle products if provided
        if (isset($jsonfromfrom['products']) && is_array($jsonfromfrom['products'])) {
            $products = $jsonfromfrom['products'];

            foreach ($products as $productId) {
                if (Product::where('productID', $productId)->exists()) {
                    $eventconfiguration->products()->save((Product::where('productID', '=', $productId)->firstOrFail()));
                }
            }
        }

        // Create reservation config
        $reservationconfig = new Reservationconfig();
        $reservationconfig->use_reservation_until = null;

        // Default to false, but use true if specified in the request
        $useReservation = isset($config['reservation_on_event']) &&
                          \filter_var($config['reservation_on_event'], \FILTER_VALIDATE_BOOLEAN) === true;
        $reservationconfig->use_reservation_on_event = $useReservation;

        $eventconfiguration->reservationconfig()->save($reservationconfig);

        // Handle route details if provided
        if (isset($jsonfromfrom['route_detail'])) {
            $routeDetailData = $jsonfromfrom['route_detail'];
            $routeDetail = new \App\Models\RouteDetail(['event_uid' => $event->event_uid]);

            // Set route detail properties
            $routeDetail->distance = $routeDetailData['distance'] ?? null;
            $routeDetail->height_difference = $routeDetailData['height_difference'] ?? null;
            $routeDetail->start_time = $routeDetailData['start_time'] ?? null;
            $routeDetail->start_place = $routeDetailData['start_place'] ?? null;
            $routeDetail->name = $routeDetailData['name'] ?? null;
            $routeDetail->description = $routeDetailData['description'] ?? null;
            $routeDetail->track_link = $routeDetailData['track_link'] ?? null;

            // Save route detail
            $event->routeDetail()->save($routeDetail);
        }

        return (new EventResource($event))->response()->setStatusCode(201);
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

        // Update event_type if provided
        if (isset($jsonfromfrom['event_type'])) {
            $eventtoupdate->event_type = $jsonfromfrom['event_type'];
        }

        // Update organizer_id if provided
        if (isset($jsonfromfrom['organizer_id'])) {
            $eventtoupdate->organizer_id = $jsonfromfrom['organizer_id'];
        }

        // Update county_id if provided
        if (isset($jsonfromfrom['county_id'])) {
            $eventtoupdate->county_id = $jsonfromfrom['county_id'];
        }

        // Update event_group_uid if provided
        if (isset($jsonfromfrom['event_group_uid'])) {
            $eventtoupdate->event_group_uid = $jsonfromfrom['event_group_uid'];
        }

        $eventtoupdate->save();

        $eventconfig = $eventtoupdate->eventconfiguration;
        $config = $jsonfromfrom['eventconfig'];
        $eventconfig->registration_opens = $config['registration_opens'];

        // Set registration_closes to 23:59 the day before event if not provided
        if (isset($config['registration_closes'])) {
            $eventconfig->registration_closes = $config['registration_closes'];
        } else {
            // Always set to 23:59 the day before event starts
            $eventStartDate = Carbon::parse($jsonfromfrom['startdate']);
            $dayBefore = $eventStartDate->copy()->subDay()->format('Y-m-d') . ' 23:59:00';
            $eventconfig->registration_closes = $dayBefore;
        }

        $eventconfig->resarvation_on_event = false;

        // Set max_registrations to env default if not provided
        if (isset($config['max_registrations'])) {
            $eventconfig->max_registrations = $config['max_registrations'];
        } else {
            $eventconfig->max_registrations = env('EVENT_DEFAULT_MAX_REGISTRATIONS', 300);
        }

        $startnumberfrom = $config['startnumberconfig'];
        $startnumberconfig = $eventconfig->startnumberconfig;
        $startnumberconfig->begins_at = $startnumberfrom['begins_at'];
        $startnumberconfig->ends_at = $startnumberfrom['ends_at'];
        $startnumberconfig->increments = $startnumberfrom['increment'];
        $startnumberconfig->save();

        // Handle products if provided
        if (isset($jsonfromfrom['products']) && is_array($jsonfromfrom['products'])) {
            // First remove existing products
            $eventconfig->products()->detach();

            // Then add the new products
            $products = $jsonfromfrom['products'];
            foreach ($products as $productId) {
                if (Product::where('productID', $productId)->exists()) {
                    $eventconfig->products()->save((Product::where('productID', '=', $productId)->firstOrFail()));
                }
            }
        }

        // Update or create reservation config
        if ($eventconfig->reservationconfig) {
            $reservationconfig = $eventconfig->reservationconfig;
        } else {
            $reservationconfig = new Reservationconfig();
        }

        // Default to false, but use true if specified in the request
        $reservationconfig->use_reservation_until = null;
        $useReservation = isset($config['reservation_on_event']) &&
                          \filter_var($config['reservation_on_event'], \FILTER_VALIDATE_BOOLEAN) === true;
        $reservationconfig->use_reservation_on_event = $useReservation;

        $eventconfig->reservationconfig()->save($reservationconfig);

        $eventconfig->save();
        $eventtoupdate->save();

        // Handle route details if provided
        if (isset($jsonfromfrom['route_detail'])) {
            $routeDetailData = $jsonfromfrom['route_detail'];

            // Find existing route detail or create new one
            $routeDetail = $eventtoupdate->routeDetail ?? new \App\Models\RouteDetail(['event_uid' => $eventtoupdate->event_uid]);

            // Set route detail properties
            $routeDetail->distance = $routeDetailData['distance'] ?? $routeDetail->distance;
            $routeDetail->height_difference = $routeDetailData['height_difference'] ?? $routeDetail->height_difference;
            $routeDetail->start_time = $routeDetailData['start_time'] ?? $routeDetail->start_time;
            $routeDetail->start_place = $routeDetailData['start_place'] ?? $routeDetail->start_place;
            $routeDetail->name = $routeDetailData['name'] ?? $routeDetail->name;
            $routeDetail->description = $routeDetailData['description'] ?? $routeDetail->description;
            $routeDetail->track_link = $routeDetailData['track_link'] ?? $routeDetail->track_link;

            // Save route detail
            if ($eventtoupdate->routeDetail) {
                $routeDetail->save();
            } else {
                $eventtoupdate->routeDetail()->save($routeDetail);
            }
        }

        return (new EventResource($eventtoupdate))->response()->setStatusCode(200);
    }

    public function delete(Request $request): JsonResponse
    {
        $event = Event::where('event_uid', '=', $request['event_uid'])->first();
        if ($event) {
            $event->delete();
        }
        return \response()->json(null, 204);
    }


    public function eventbyid(Request $request): JsonResponse
    {
        $event = Event::where('event_uid', '=', $request['event_uid'])->first();
        if ($event) {
            return \response()->json($event, 200);
        }
        return \response()->json(null, 204);
    }

    /**
     * Get route details for a specific event.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function getRouteDetails(Request $request): JsonResponse
    {
        $event = Event::with('routeDetail')
            ->where('event_uid', '=', $request['event_uid'])
            ->first();

        if ($event && $event->routeDetail) {
            // Note: We always show track links if they are present in the database
            // The migration only generates links in development environment
            return \response()->json($event->routeDetail, 200);
        }

        return \response()->json(['message' => 'Route details not found'], 404);
    }

    /**
     * Create or update route details for an event.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function updateRouteDetails(Request $request): JsonResponse
    {
        $eventUid = $request['event_uid'];
        $event = Event::where('event_uid', '=', $eventUid)->first();

        if (!$event) {
            return \response()->json(['message' => 'Event not found'], 404);
        }

        // Validate request data
        $request->validate([
            'distance' => 'required|numeric',
            'height_difference' => 'required|numeric',
            'start_time' => 'required|string',
            'start_place' => 'nullable|string',
            'name' => 'nullable|string',
            'description' => 'nullable|string',
            'track_link' => 'nullable|url',
        ]);

        // Find existing route detail or create new one
        $routeDetail = $event->routeDetail ?? new \App\Models\RouteDetail(['event_uid' => $eventUid]);

        // Update route detail properties
        $routeDetail->distance = $request->input('distance');
        $routeDetail->height_difference = $request->input('height_difference');
        $routeDetail->start_time = $request->input('start_time');
        $routeDetail->start_place = $request->input('start_place');
        $routeDetail->name = $request->input('name');
        $routeDetail->description = $request->input('description');
        $routeDetail->track_link = $request->input('track_link');

        // Save route detail
        if ($event->routeDetail) {
            $routeDetail->save();
        } else {
            $event->routeDetail()->save($routeDetail);
        }

        return \response()->json($routeDetail, 200);
    }

    public function getLogin(Request $request)
    {
        $eventType = $request->query('event_type');
        $event = Event::where('event_uid', '=', $request['uid'])->first();

//        if (!$event) {
//            return response()->json(['message' => 'Event not found'], 404);
//        }

        // TODO: Implement login functionality based on event type
        return \response()->json([
            'message' => 'Not implemented yet',
            'event_type' => $eventType,
            'event' => $event
        ], 501);
    }

    public function show(string $eventUid): View
    {
        $event = Event::where('event_uid', '=', $eventUid)->first();

        if (!$event) {
            \abort(404);
        }

        // Note: We always show track links if they are present in the database
        // The migration only generates links in development environment

        return \view('event.show', [
            'event' => $event,
        ]);
    }

}

