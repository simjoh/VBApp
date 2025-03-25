<?php

namespace App\Http\Controllers;

use App\Http\Resources\EventResource;
use App\Http\Resources\EventCollection;
use App\Http\Resources\RouteDetailResource;
use App\Models\Event;
use App\Models\EventConfiguration;
use App\Models\Organizer;
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
        // Calculate date 3 months ago from now
        $threeMonthsAgo = Carbon::now()->subMonths(3);

        // Load events with their organizer relationship
        $events = Event::with(['organizer', 'routeDetail'])
            ->whereIn('event_type', ['BRM', 'BP'])
            ->where('enddate', '>=', $threeMonthsAgo)
            ->get()
            ->sortBy("startdate");

        // Check if we're in development environment
        $isDevEnvironment = \in_array(\env('APP_ENV'), ['local', 'development']);

        // Debug: Log events count
        Log::info('Events count: ' . $events->count());

        // Debug: Check if organizer data is present
        foreach ($events as $event) {
            // Debug: Log event details
            Log::info('Event: ' . $event->title . ', Has RouteDetail: ' . ($event->routeDetail ? 'Yes' : 'No'));
            if ($event->routeDetail) {
                Log::info('RouteDetail for ' . $event->title . ': ' . json_encode([
                    'distance' => $event->routeDetail->distance,
                    'height_difference' => $event->routeDetail->height_difference,
                    'start_time' => $event->routeDetail->start_time,
                    'start_place' => $event->routeDetail->start_place
                ]));
            }

            // Create a dynamic property for the startlist URL
            $event->setAttribute('startlisturl', \env("APP_ENV") === 'production' ? \env("APP_URL") . '/public/startlist/event/' . $event->event_uid . '/showall' : \env("APP_URL") . '/startlist/event/' . $event->event_uid . '/showall');

            // We don't create default route details anymore, as they should not be mandatory
            // The view will handle null route details

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
        // Get pagination parameters with defaults
        $perPage = request()->input('per_page', 15);
        $page = request()->input('page', 1);

        // Get filter parameters
        $eventType = request()->input('event_type');
        $organizerId = request()->input('organizer_id');
        $completed = request()->input('completed');

        // Build query
        $query = Event::query();

        // Apply filters if provided
        if ($eventType !== null) {
            $query->where('event_type', $eventType);
        }

        if ($organizerId !== null) {
            $query->where('organizer_id', $organizerId);
        }

        if ($completed !== null) {
            $query->where('completed', filter_var($completed, FILTER_VALIDATE_BOOLEAN));
        }

        // Apply sorting
        $sortBy = request()->input('sort_by', 'startdate');
        $sortDir = request()->input('sort_dir', 'asc');
        $allowedSortFields = ['event_uid', 'title', 'startdate', 'enddate', 'created_at', 'updated_at'];

        if (in_array($sortBy, $allowedSortFields)) {
            $query->orderBy($sortBy, strtolower($sortDir) === 'desc' ? 'desc' : 'asc');
        }

        // Get paginated results
        $events = $query->paginate($perPage, ['*'], 'page', $page);

        return new EventCollection($events);
    }

    public function create(Request $request)
    {
        $data = $request->json()->all();

        // Validate required fields
        if (!isset($data['title']) || !isset($data['description']) || !isset($data['startdate']) || !isset($data['enddate'])) {
            return response()->json(['message' => 'Missing required fields'], 400);
        }

        // Validate organizer_id is present and valid
        if (!isset($data['organizer_id'])) {
            return response()->json(['message' => 'organizer_id is required'], 400);
        }

        // Check if organizer exists
        if (!Organizer::where('id', $data['organizer_id'])->exists()) {
            return response()->json(['message' => 'Invalid organizer_id. Organizer does not exist'], 400);
        }

        // Validate route_detail required fields
        if (isset($data['route_detail'])) {
            $routeDetailData = $data['route_detail'];
            if (!isset($routeDetailData['distance']) || !isset($routeDetailData['start_time'])) {
                return response()->json(['message' => 'Missing required route_detail fields (distance, start_time)'], 400);
            }
        }

        if (Event::where('title', $data['title'])->exists()) {
            return response()->json("alreadyexists", 500);
        }

        $event = new Event();
        $event->event_uid = Uuid::uuid4();
        $event->title = $data['title'];
        $event->description = $data['description'];
        $event->startdate = $data['startdate'];
        $event->enddate = $data['enddate'];
        $event->completed = false;

        // Set event_type if provided, default to env value or BRM
        $event->event_type = $data['event_type'] ?? env('EVENT_DEFAULT_TYPE', 'BRM');

        // Set organizer_id if provided
        if (isset($data['organizer_id'])) {
            $event->organizer_id = $data['organizer_id'];
        }

        // Set county_id if provided
        if (isset($data['county_id'])) {
            $event->county_id = $data['county_id'];
        }

        // Set event_group_uid if provided
        if (isset($data['event_group_uid'])) {
            $event->event_group_uid = $data['event_group_uid'];
        }

        $event->save();

        // Create event configuration
        $eventconfiguration = new EventConfiguration();

        if (isset($data['eventconfiguration'])) {
            $config = $data['eventconfiguration'];

            // Set registration_opens if provided
            if (isset($config['registration_opens'])) {
                $eventconfiguration->registration_opens = $config['registration_opens'];
            }

            // Set use_stripe_payment if provided
            if (isset($config['use_stripe_payment'])) {
                $eventconfiguration->use_stripe_payment = $config['use_stripe_payment'];
            } else {
                $eventconfiguration->use_stripe_payment = false;
            }

            // Set registration_closes to 23:59 the day before event if not provided
            if (isset($config['registration_closes'])) {
                $eventconfiguration->registration_closes = $config['registration_closes'];
            } else {
                // Always set to 23:59 the day before event starts
                $eventStartDate = Carbon::parse($data['startdate']);
                $dayBefore = $eventStartDate->copy()->subDay()->format('Y-m-d') . ' 23:59:00';
                $eventconfiguration->registration_closes = $dayBefore;
            }

            // Set resarvation_on_event if provided
            if (isset($config['resarvation_on_event'])) {
                $eventconfiguration->resarvation_on_event = $config['resarvation_on_event'];
            } else {
                $eventconfiguration->resarvation_on_event = false;
            }

            // Set max_registrations if provided
            if (isset($config['max_registrations'])) {
                $eventconfiguration->max_registrations = $config['max_registrations'];
            } else {
                $eventconfiguration->max_registrations = env('EVENT_DEFAULT_MAX_REGISTRATIONS', 300);
            }
        } else {
            // Set default values if eventconfiguration is not provided
            $eventStartDate = Carbon::parse($data['startdate']);
            $dayBefore = $eventStartDate->copy()->subDay()->format('Y-m-d') . ' 23:59:00';

            $eventconfiguration->registration_opens = $eventStartDate->copy()->subDays(30)->format('Y-m-d') . ' 00:00:00';
            $eventconfiguration->registration_closes = $dayBefore;
            $eventconfiguration->resarvation_on_event = false;
            $eventconfiguration->use_stripe_payment = false;
            $eventconfiguration->max_registrations = env('EVENT_DEFAULT_MAX_REGISTRATIONS', 300);
        }

        $event->eventconfiguration()->save($eventconfiguration);

        // Create start number configuration
        $startnumberconfig = new StartNumberConfig();

        if (isset($data['eventconfiguration']['startnumberconfig'])) {
            $startnumberfrom = $data['eventconfiguration']['startnumberconfig'];
            $startnumberconfig->begins_at = $startnumberfrom['begins_at'] ?? 1;
            $startnumberconfig->ends_at = $startnumberfrom['ends_at'] ?? 1000;
            $startnumberconfig->increments = $startnumberfrom['increments'] ?? 1;
        } else {
            // Default values
            $startnumberconfig->begins_at = 1;
            $startnumberconfig->ends_at = 1000;
            $startnumberconfig->increments = 1;
        }

        $eventconfiguration->startnumberconfig()->save($startnumberconfig);

        // Handle products if provided
        if (isset($data['eventconfiguration']['products']) && is_array($data['eventconfiguration']['products'])) {
            $products = $data['eventconfiguration']['products'];
            $addedProducts = [];
            $has1013 = false;
            $has1014 = false;

            foreach ($products as $productId) {
                if (Product::where('productID', $productId)->exists()) {
                    $product = Product::where('productID', '=', $productId)->firstOrFail();
                    $eventconfiguration->products()->save($product);
                    $addedProducts[] = $productId;

                    if ($productId == '1013') {
                        $has1013 = true;
                    }
                    if ($productId == '1014') {
                        $has1014 = true;
                    }
                }
            }

            // For BRM events, ensure at least 2 products
            if ($event->event_type === 'BRM' && count($addedProducts) < 2) {
                // Add default products if not already added
                if (!$has1013) {
                    $product1013 = Product::where('productID', '=', '1013')->first();
                    if ($product1013) {
                        $eventconfiguration->products()->save($product1013);
                        $addedProducts[] = '1013';
                    }
                }

                // If we still don't have 2 products, add 1014 if not already added
                if (count($addedProducts) < 2 && !$has1014) {
                    $product1014 = Product::where('productID', '=', '1014')->first();
                    if ($product1014) {
                        $eventconfiguration->products()->save($product1014);
                        $addedProducts[] = '1014';
                    }
                }
            }
        } else {
            // For BRM events, add default products if no products provided
            if ($event->event_type === 'BRM') {
                // Add default product 1013
                $product1013 = Product::where('productID', '=', '1013')->first();
                if ($product1013) {
                    $eventconfiguration->products()->save($product1013);
                }

                // Add default product 1014 (medal)
                $product1014 = Product::where('productID', '=', '1014')->first();
                if ($product1014) {
                    $eventconfiguration->products()->save($product1014);
                }
            }
        }

        // Create reservation config
        $reservationconfig = new Reservationconfig();

        if (isset($data['eventconfiguration']['reservationconfig'])) {
            $reservationData = $data['eventconfiguration']['reservationconfig'];
            $reservationconfig->use_reservation_until = $reservationData['use_reservation_until'] ?? null;
            $reservationconfig->use_reservation_on_event = $reservationData['use_reservation_on_event'] ?? false;
        } else {
            $reservationconfig->use_reservation_until = null;
            $reservationconfig->use_reservation_on_event = false;
        }

        $eventconfiguration->reservationconfig()->save($reservationconfig);

        // Handle route details if provided
        if (isset($data['route_detail'])) {
            $routeDetailData = $data['route_detail'];
            $routeDetail = new \App\Models\RouteDetail(['event_uid' => $event->event_uid]);

            // Set route detail properties
            $routeDetail->distance = $routeDetailData['distance'];
            $routeDetail->height_difference = $routeDetailData['height_difference'] ?? 0;
            $routeDetail->start_time = $routeDetailData['start_time'];
            $routeDetail->start_place = $routeDetailData['start_place'] ?? null;
            $routeDetail->name = $routeDetailData['name'] ?? null;
            $routeDetail->description = $routeDetailData['description'] ?? null;
            $routeDetail->track_link = $routeDetailData['track_link'] ?? null;

            // Save route detail
            $event->routeDetail()->save($routeDetail);
        }

        return (new EventResource($event))->response()->setStatusCode(201);
    }

    public function update(Request $request, string $eventUid)
    {
        $data = $request->json()->all();

        // Validate required fields
        if (!isset($data['title']) || !isset($data['description']) || !isset($data['startdate']) || !isset($data['enddate'])) {
            return response()->json(['message' => 'Missing required fields'], 400);
        }

        // Validate organizer_id is present and valid
        if (!isset($data['organizer_id'])) {
            return response()->json(['message' => 'organizer_id is required'], 400);
        }

        // Check if organizer exists
        if (!Organizer::where('id', $data['organizer_id'])->exists()) {
            return response()->json(['message' => 'Invalid organizer_id. Organizer does not exist'], 400);
        }

        $event = Event::where('event_uid', '=', $eventUid)->first();

        if (!$event) {
            return response()->json(['message' => 'Event not found'], 404);
        }

        $event->title = $data['title'];
        $event->description = $data['description'];
        $event->startdate = $data['startdate'];
        $event->enddate = $data['enddate'];

        // Update completed if provided
        if (isset($data['completed'])) {
            $event->completed = $data['completed'];
        }

        // Update event_type if provided
        if (isset($data['event_type'])) {
            $event->event_type = $data['event_type'];
        }

        // Update organizer_id if provided
        if (isset($data['organizer_id'])) {
            $event->organizer_id = $data['organizer_id'];
        }

        // Update county_id if provided
        if (isset($data['county_id'])) {
            $event->county_id = $data['county_id'];
        }

        // Update event_group_uid if provided
        if (isset($data['event_group_uid'])) {
            $event->event_group_uid = $data['event_group_uid'];
        }

        $event->save();

        // Update event configuration
        if (isset($data['eventconfiguration'])) {
            $config = $data['eventconfiguration'];
            $eventconfig = $event->eventconfiguration;

            if (!$eventconfig) {
                $eventconfig = new EventConfiguration();
                $event->eventconfiguration()->save($eventconfig);
            }

            // Update registration_opens if provided
            if (isset($config['registration_opens'])) {
                $eventconfig->registration_opens = $config['registration_opens'];
            }

            // Update use_stripe_payment if provided
            if (isset($config['use_stripe_payment'])) {
                $eventconfig->use_stripe_payment = $config['use_stripe_payment'];
            }

            // Update registration_closes if provided
            if (isset($config['registration_closes'])) {
                $eventconfig->registration_closes = $config['registration_closes'];
            } else {
                // Always set to 23:59 the day before event starts
                $eventStartDate = Carbon::parse($data['startdate']);
                $dayBefore = $eventStartDate->copy()->subDay()->format('Y-m-d') . ' 23:59:00';
                $eventconfig->registration_closes = $dayBefore;
            }

            // Update resarvation_on_event if provided
            if (isset($config['resarvation_on_event'])) {
                $eventconfig->resarvation_on_event = $config['resarvation_on_event'];
            }

            // Update max_registrations if provided
            if (isset($config['max_registrations'])) {
                $eventconfig->max_registrations = $config['max_registrations'];
            }

            $eventconfig->save();

            // Update start number configuration
            if (isset($config['startnumberconfig'])) {
                $startnumberfrom = $config['startnumberconfig'];
                $startnumberconfig = $eventconfig->startnumberconfig;

                if (!$startnumberconfig) {
                    $startnumberconfig = new StartNumberConfig();
                    $eventconfig->startnumberconfig()->save($startnumberconfig);
                }

                if (isset($startnumberfrom['begins_at'])) {
                    $startnumberconfig->begins_at = $startnumberfrom['begins_at'];
                }

                if (isset($startnumberfrom['ends_at'])) {
                    $startnumberconfig->ends_at = $startnumberfrom['ends_at'];
                }

                if (isset($startnumberfrom['increments'])) {
                    $startnumberconfig->increments = $startnumberfrom['increments'];
                }

                $startnumberconfig->save();
            }

            // Handle products if provided
            if (isset($config['products']) && is_array($config['products'])) {
                // First remove existing products
                $eventconfig->products()->detach();

                // Then add the new products
                $products = $config['products'];
                $addedProducts = [];
                $has1013 = false;
                $has1014 = false;

                foreach ($products as $productId) {
                    if (Product::where('productID', $productId)->exists()) {
                        $product = Product::where('productID', '=', $productId)->firstOrFail();
                        $eventconfig->products()->save($product);
                        $addedProducts[] = $productId;

                        if ($productId == '1013') {
                            $has1013 = true;
                        }
                        if ($productId == '1014') {
                            $has1014 = true;
                        }
                    }
                }

                // For BRM events, ensure at least 2 products
                if ($event->event_type === 'BRM' && count($addedProducts) < 2) {
                    // Add default products if not already added
                    if (!$has1013) {
                        $product1013 = Product::where('productID', '=', '1013')->first();
                        if ($product1013) {
                            $eventconfig->products()->save($product1013);
                            $addedProducts[] = '1013';
                        }
                    }

                    // If we still don't have 2 products, add 1014 if not already added
                    if (count($addedProducts) < 2 && !$has1014) {
                        $product1014 = Product::where('productID', '=', '1014')->first();
                        if ($product1014) {
                            $eventconfig->products()->save($product1014);
                            $addedProducts[] = '1014';
                        }
                    }
                }
            } else if ($event->event_type === 'BRM' && isset($config)) {
                // For BRM events, if config is provided but no products, add default products
                // First check if there are any existing products
                if ($eventconfig->products()->count() === 0) {
                    // Add default product 1013
                    $product1013 = Product::where('productID', '=', '1013')->first();
                    if ($product1013) {
                        $eventconfig->products()->save($product1013);
                    }

                    // Add default product 1014 (medal)
                    $product1014 = Product::where('productID', '=', '1014')->first();
                    if ($product1014) {
                        $eventconfig->products()->save($product1014);
                    }
                }
            }

            // Update reservation config
            if (isset($config['reservationconfig'])) {
                $reservationData = $config['reservationconfig'];

                if ($eventconfig->reservationconfig) {
                    $reservationconfig = $eventconfig->reservationconfig;
                } else {
                    $reservationconfig = new Reservationconfig();
                    $eventconfig->reservationconfig()->save($reservationconfig);
                }

                if (isset($reservationData['use_reservation_until'])) {
                    $reservationconfig->use_reservation_until = $reservationData['use_reservation_until'];
                }

                if (isset($reservationData['use_reservation_on_event'])) {
                    $reservationconfig->use_reservation_on_event = $reservationData['use_reservation_on_event'];
                }

                $reservationconfig->save();
            }
        }

        // Handle route details if provided
        if (isset($data['route_detail'])) {
            $routeDetailData = $data['route_detail'];

            // Find existing route detail or create new one
            $routeDetail = $event->routeDetail ?? new \App\Models\RouteDetail(['event_uid' => $event->event_uid]);

            // Set route detail properties
            if (isset($routeDetailData['distance'])) {
                $routeDetail->distance = $routeDetailData['distance'] ?? null;
            }

            if (isset($routeDetailData['height_difference'])) {
                $routeDetail->height_difference = $routeDetailData['height_difference'] ?? 0;
            }

            if (isset($routeDetailData['start_time'])) {
                $routeDetail->start_time = $routeDetailData['start_time'];
            }

            if (isset($routeDetailData['start_place'])) {
                $routeDetail->start_place = $routeDetailData['start_place'];
            }

            if (isset($routeDetailData['name'])) {
                $routeDetail->name = $routeDetailData['name'];
            }

            if (isset($routeDetailData['description'])) {
                $routeDetail->description = $routeDetailData['description'];
            }

            if (isset($routeDetailData['track_link'])) {
                $routeDetail->track_link = $routeDetailData['track_link'];
            }

            // Save route detail
            if ($event->routeDetail) {
                $routeDetail->save();
            } else {
                $event->routeDetail()->save($routeDetail);
            }
        }

        return new EventResource($event);
    }

    public function delete(Request $request): JsonResponse
    {
        $event = Event::where('event_uid', '=', $request['event_uid'])->first();
        if ($event) {
            $event->delete();
            return response()->json(['message' => 'Event deleted successfully'], 200);
        }
        return response()->json(['message' => 'Event not found'], 404);
    }


    public function eventbyid(Request $request): JsonResponse
    {
        $event = Event::where('event_uid', '=', $request['event_uid'])->first();
        if ($event) {
            return response()->json(new EventResource($event), 200);
        }
        return response()->json(['message' => 'Event not found'], 404);
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
            return response()->json(new RouteDetailResource($event->routeDetail), 200);
        }

        return response()->json(['message' => 'Route details not found'], 404);
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
            return response()->json(['message' => 'Event not found'], 404);
        }

        // Validate request data
        $request->validate([
            'distance' => 'required|numeric',
            'height_difference' => 'nullable|numeric',
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
        $routeDetail->height_difference = $request->input('height_difference', 0);
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

        return response()->json(new RouteDetailResource($routeDetail), 200);
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
        // Debug: Log the event UID being requested
        Log::info('Show method called for event UID: ' . $eventUid);

        $event = Event::with(['routeDetail', 'organizer'])->where('event_uid', '=', $eventUid)->first();

        if (!$event) {
            Log::error('Event not found: ' . $eventUid);
            \abort(404);
        }

        // Debug: Log if the event has a route detail
        Log::info('Event found: ' . $event->title . ', Has RouteDetail: ' . ($event->routeDetail ? 'Yes' : 'No'));

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

        // Create a dynamic property for the startlist URL
        $event->setAttribute('startlisturl', \env("APP_ENV") === 'production' ? \env("APP_URL") . '/public/startlist/event/' . $event->event_uid . '/showall' : \env("APP_URL") . '/startlist/event/' . $event->event_uid . '/showall');

        // Group the event by month and year
        $month = $months[$startDate->format('m')] . " " . $startDate->format('Y');
        $events = collect([$event]);
        $allevents = [$month => $events];

        // Note: We always show track links if they are present in the database
        // The migration only generates links in development environment

        return \view('event.show', [
            'allevents' => $allevents,
        ]);
    }

}

