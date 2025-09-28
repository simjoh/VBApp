<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class StatsController extends Controller
{
    /**
     * Get statistics for a specific event
     *
     * @param string $eventUid
     * @return JsonResponse
     */
    public function getEventStats(string $eventUid): JsonResponse
    {
        try {
            // First verify the event exists
            $event = Event::where('event_uid', '=', $eventUid)->first();
            if (!$event) {
                return response()->json(['message' => 'Event not found'], 404);
            }

            // Get event configuration for max registrations
            $eventConfig = $event->eventConfiguration;
            $maxRegistrations = $eventConfig ? $eventConfig->max_registrations : 0;

            // For MSR events, course_uid in registrations is actually the event_uid
            // For other events, we would need to check if courses table exists
            $courses = collect([$eventUid]);

            // Get registration statistics
            $registrationStats = DB::table('registrations')
                ->whereIn('course_uid', $courses)
                ->selectRaw('
                    COUNT(*) as total_registrations,
                    SUM(CASE WHEN reservation = 1 THEN 1 ELSE 0 END) as total_reservations,
                    SUM(CASE WHEN reservation = 0 THEN 1 ELSE 0 END) as confirmed_registrations
                ')
                ->first();

            // Get optional products statistics
            $optionalStats = DB::table('optionals')
                ->join('registrations', 'optionals.registration_uid', '=', 'registrations.registration_uid')
                ->join('products', 'optionals.productID', '=', 'products.productID')
                ->whereIn('registrations.course_uid', $courses)
                ->selectRaw('
                    optionals.productID,
                    products.productname,
                    COUNT(*) as count
                ')
                ->groupBy('optionals.productID', 'products.productname')
                ->orderBy('count', 'desc')
                ->get();

            // Calculate percentages for optional products
            $totalRegistrations = $registrationStats->total_registrations;
            $optionalProducts = $optionalStats->map(function ($item) use ($totalRegistrations) {
                return [
                    'product_id' => $item->productID,
                    'product_name' => $item->productname,
                    'count' => $item->count,
                    'percentage' => $totalRegistrations > 0 ? round(($item->count / $totalRegistrations) * 100, 1) : 0
                ];
            });

            // Get registration trends (last 7 and 30 days)
            $sevenDaysAgo = now()->subDays(7)->format('Y-m-d H:i:s');
            $thirtyDaysAgo = now()->subDays(30)->format('Y-m-d H:i:s');

            $trends = DB::table('registrations')
                ->whereIn('course_uid', $courses)
                ->selectRaw('
                    SUM(CASE WHEN created_at >= ? THEN 1 ELSE 0 END) as last_7_days,
                    SUM(CASE WHEN created_at >= ? THEN 1 ELSE 0 END) as last_30_days
                ', [$sevenDaysAgo, $thirtyDaysAgo])
                ->first();

            // Calculate registration percentage
            $registrationPercentage = $maxRegistrations > 0
                ? round(($totalRegistrations / $maxRegistrations) * 100, 1)
                : 0;

            return response()->json([
                'event_uid' => $eventUid,
                'event_title' => $event->title,
                'total_registrations' => $totalRegistrations,
                'confirmed_registrations' => $registrationStats->confirmed_registrations,
                'total_reservations' => $registrationStats->total_reservations,
                'max_registrations' => $maxRegistrations,
                'registration_percentage' => $registrationPercentage,
                'optional_products' => $optionalProducts,
                'registration_trends' => [
                    'last_7_days' => $trends->last_7_days ?? 0,
                    'last_30_days' => $trends->last_30_days ?? 0
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Error getting event statistics: ' . $e->getMessage());
            return response()->json(['message' => 'Error retrieving statistics'], 500);
        }
    }

    /**
     * Get optional products available for an event
     *
     * @param string $eventUid
     * @return JsonResponse
     */
    public function getEventOptionalProducts(string $eventUid): JsonResponse
    {
        try {
            // First verify the event exists
            $event = Event::where('event_uid', '=', $eventUid)->first();
            if (!$event) {
                return response()->json(['message' => 'Event not found'], 404);
            }

            // Get event configuration
            $eventConfig = $event->eventConfiguration;
            if (!$eventConfig) {
                return response()->json([
                    'event_uid' => $eventUid,
                    'event_title' => $event->title,
                    'optional_products' => []
                ]);
            }

            // Get available optional products for this event
            $optionalProducts = DB::table('eventconfiguration_product')
                ->join('products', 'eventconfiguration_product.product_id', '=', 'products.productID')
                ->where('eventconfiguration_product.eventconfiguration_id', $eventConfig->id)
                ->select('products.productID', 'products.productname', 'products.categoryID')
                ->orderBy('products.productname')
                ->get();

            return response()->json([
                'event_uid' => $eventUid,
                'event_title' => $event->title,
                'optional_products' => $optionalProducts->map(function ($item) {
                    return [
                        'product_id' => $item->productID,
                        'product_name' => $item->productname,
                        'category_id' => $item->categoryID
                    ];
                })
            ]);

        } catch (\Exception $e) {
            Log::error('Error getting event optional products: ' . $e->getMessage());
            return response()->json(['message' => 'Error retrieving optional products'], 500);
        }
    }

    /**
     * Get registration details for an event (if needed for detailed analysis)
     *
     * @param string $eventUid
     * @return JsonResponse
     */
    public function getEventRegistrations(string $eventUid): JsonResponse
    {
        try {
            // First verify the event exists
            $event = Event::where('event_uid', '=', $eventUid)->first();
            if (!$event) {
                return response()->json(['message' => 'Event not found'], 404);
            }

            // For MSR events, course_uid in registrations is actually the event_uid
            // For other events, we would need to check if courses table exists
            $courses = collect([$eventUid]);

            // Get registrations with optional products
            $registrations = DB::table('registrations')
                ->join('person', 'registrations.person_uid', '=', 'person.person_uid')
                ->leftJoin('contactinformation', 'person.person_uid', '=', 'contactinformation.person_person_uid')
                ->leftJoin('optionals', 'registrations.registration_uid', '=', 'optionals.registration_uid')
                ->leftJoin('products', 'optionals.productID', '=', 'products.productID')
                ->whereIn('registrations.course_uid', $courses)
                ->select([
                    'registrations.registration_uid',
                    'registrations.reservation',
                    'registrations.created_at',
                    'person.firstname',
                    'person.surname',
                    'contactinformation.email',
                    'optionals.productID',
                    'products.productname'
                ])
                ->orderBy('registrations.created_at', 'desc')
                ->get();

            // Group registrations and their optionals
            $groupedRegistrations = [];
            foreach ($registrations as $registration) {
                $uid = $registration->registration_uid;

                if (!isset($groupedRegistrations[$uid])) {
                    $groupedRegistrations[$uid] = [
                        'registration_uid' => $uid,
                        'reservation' => (bool) $registration->reservation,
                        'created_at' => $registration->created_at,
                        'person' => [
                            'firstname' => $registration->firstname,
                            'surname' => $registration->surname,
                            'email' => $registration->email
                        ],
                        'optional_products' => []
                    ];
                }

                if ($registration->productID) {
                    $groupedRegistrations[$uid]['optional_products'][] = [
                        'product_id' => $registration->productID,
                        'product_name' => $registration->productname
                    ];
                }
            }

            return response()->json([
                'event_uid' => $eventUid,
                'event_title' => $event->title,
                'registrations' => array_values($groupedRegistrations)
            ]);

        } catch (\Exception $e) {
            Log::error('Error getting event registrations: ' . $e->getMessage());
            return response()->json(['message' => 'Error retrieving registrations'], 500);
        }
    }

    /**
     * Get non-participant optionals for MSR events.
     * Can filter by event_uid or date interval.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function getNonParticipantOptionals(Request $request): JsonResponse
    {
        try {
            $eventUid = $request->input('event_uid');
            $startDate = $request->input('start_date');
            $endDate = $request->input('end_date');

            // Validate input
            if (!$eventUid && (!$startDate || !$endDate)) {
                return response()->json([
                    'message' => 'Either event_uid or both start_date and end_date must be provided'
                ], 400);
            }

            $query = DB::table('non_participant_optionals')
                ->join('products', 'non_participant_optionals.productID', '=', 'products.productID')
                ->leftJoin('events', 'non_participant_optionals.course_uid', '=', 'events.event_uid')
                ->select([
                    'non_participant_optionals.optional_uid',
                    'non_participant_optionals.course_uid',
                    'non_participant_optionals.firstname',
                    'non_participant_optionals.surname',
                    'non_participant_optionals.email',
                    'non_participant_optionals.productID',
                    'non_participant_optionals.quantity',
                    'non_participant_optionals.additional_information',
                    'non_participant_optionals.created_at',
                    'non_participant_optionals.updated_at',
                    'products.productname',
                    'products.description',
                    'products.price',
                    'events.title as event_title',
                    'events.startdate as event_startdate'
                ]);

            if ($eventUid) {
                // Filter by event_uid - get all non-participant optionals for this specific event
                $query->where('non_participant_optionals.course_uid', $eventUid);
            } else {
                // Filter by date interval
                $query->whereBetween('non_participant_optionals.created_at', [$startDate, $endDate]);
            }

            $optionals = $query->orderBy('non_participant_optionals.created_at', 'desc')->get();

            // Group by product for statistics
            $productStats = $optionals->groupBy('productID')->map(function ($group) {
                $first = $group->first();
                return [
                    'product_id' => $first->productID,
                    'product_name' => $first->productname,
                    'description' => $first->description,
                    'price' => $first->price,
                    'total_quantity' => $group->sum('quantity'),
                    'total_registrations' => $group->count(),
                    'registrations' => $group->map(function ($item) {
                        return [
                            'optional_uid' => $item->optional_uid,
                            'firstname' => $item->firstname,
                            'surname' => $item->surname,
                            'email' => $item->email,
                            'quantity' => $item->quantity,
                            'additional_information' => $item->additional_information,
                            'created_at' => $item->created_at,
                            'event_title' => $item->event_title,
                            'event_startdate' => $item->event_startdate
                        ];
                    })->values()
                ];
            })->values();

            return response()->json([
                'filter_type' => $eventUid ? 'event' : 'date_interval',
                'event_uid' => $eventUid,
                'start_date' => $startDate,
                'end_date' => $endDate,
                'total_registrations' => $optionals->count(),
                'total_quantity' => $optionals->sum('quantity'),
                'products' => $productStats
            ]);

        } catch (\Exception $e) {
            Log::error('Error getting non-participant optionals: ' . $e->getMessage());
            return response()->json(['message' => 'Error retrieving non-participant optionals'], 500);
        }
    }
}
