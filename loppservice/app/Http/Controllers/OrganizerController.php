<?php

namespace App\Http\Controllers;

use App\Http\Resources\OrganizerCollection;
use App\Http\Resources\OrganizerResource;
use App\Models\Organizer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

/**
 * Organizer API Controller
 *
 * This controller handles CRUD operations for organizers.
 *
 * SVG Logo Handling:
 * -----------------
 * The API supports base64 encoded SVG content for the logo_svg field.
 *
 * When sending data:
 * 1. Convert your SVG to base64 (e.g., btoa(svgString) in JavaScript)
 * 2. Send the base64 string in the logo_svg field
 *
 * When receiving data:
 * 1. The logo_svg field will contain a base64 encoded string
 * 2. Decode it to get the original SVG (e.g., atob(data.logo_svg) in JavaScript)
 *
 * Example:
 * ```
 * // JavaScript - Encoding before sending
 * const svgString = '<svg>...</svg>';
 * const base64Svg = btoa(svgString);
 *
 * // JavaScript - Decoding after receiving
 * const svgString = atob(response.data.logo_svg);
 * ```
 */
class OrganizerController extends Controller
{
    /**
     * Display a listing of the organizers.
     *
     * @param Request $request
     * @return OrganizerCollection
     */
    public function index(Request $request): OrganizerCollection
    {
        // Get pagination parameters with defaults
        $perPage = $request->input('per_page', 15);
        $page = $request->input('page', 1);

        // Get filter parameters
        $active = $request->input('active');

        // Build query
        $query = Organizer::query();

        // Apply filters if provided
        if ($active !== null) {
            $query->where('active', filter_var($active, FILTER_VALIDATE_BOOLEAN));
        }

        // Apply sorting
        $sortBy = $request->input('sort_by', 'organization_name');
        $sortDir = $request->input('sort_dir', 'asc');
        $allowedSortFields = ['id', 'organization_name', 'created_at', 'updated_at'];

        if (in_array($sortBy, $allowedSortFields)) {
            $query->orderBy($sortBy, strtolower($sortDir) === 'desc' ? 'desc' : 'asc');
        }

        // Get paginated results
        $organizers = $query->paginate($perPage, ['*'], 'page', $page);

        return new OrganizerCollection($organizers);
    }

    /**
     * Store a newly created organizer in storage.
     *
     * @param Request $request
     * @return JsonResponse|OrganizerResource
     */
    public function store(Request $request)
    {
        try {
            $validated = $this->validateOrganizerData($request);

            $organizer = new Organizer();
            $this->setOrganizerAttributes($organizer, $validated);
            $organizer->save();

            return (new OrganizerResource($organizer))
                ->response()
                ->setStatusCode(201);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to create organizer',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified organizer.
     *
     * @param int $id
     * @return JsonResponse|OrganizerResource
     */
    public function show(int $id)
    {
        $organizer = Organizer::find($id);

        if (!$organizer) {
            return response()->json(['message' => 'Organizer not found'], 404);
        }

        return new OrganizerResource($organizer);
    }

    /**
     * Update the specified organizer in storage.
     *
     * @param Request $request
     * @param int $id
     * @return JsonResponse|OrganizerResource
     */
    public function update(Request $request, int $id)
    {
        try {
            $organizer = Organizer::find($id);

            if (!$organizer) {
                return response()->json(['message' => 'Organizer not found'], 404);
            }

            $validated = $this->validateOrganizerData($request, false);
            $this->setOrganizerAttributes($organizer, $validated);
            $organizer->save();

            return new OrganizerResource($organizer);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to update organizer',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified organizer from storage.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function destroy(int $id): JsonResponse
    {
        $organizer = Organizer::find($id);

        if (!$organizer) {
            return response()->json(['message' => 'Organizer not found'], 404);
        }

        // Check if organizer has associated events
        if ($organizer->events()->count() > 0) {
            return response()->json([
                'message' => 'Cannot delete organizer with associated events',
                'events_count' => $organizer->events()->count()
            ], 409); // Conflict
        }

        $organizer->delete();
        return response()->json(null, 204); // No content
    }

    /**
     * Get events for a specific organizer.
     *
     * @param Request $request
     * @param int $id
     * @return JsonResponse|AnonymousResourceCollection
     */
    public function events(Request $request, int $id)
    {
        $organizer = Organizer::find($id);

        if (!$organizer) {
            return response()->json(['message' => 'Organizer not found'], 404);
        }

        // Get pagination parameters with defaults
        $perPage = $request->input('per_page', 15);
        $page = $request->input('page', 1);

        // Get events with pagination
        $events = $organizer->events()->paginate($perPage, ['*'], 'page', $page);

        return OrganizerResource::collection($events);
    }

    /**
     * Validate organizer data from request.
     *
     * @param Request $request
     * @param bool $isRequired Whether fields are required (for create) or optional (for update)
     * @return array
     * @throws ValidationException
     */
    private function validateOrganizerData(Request $request, bool $isRequired = true): array
    {
        $rules = [
            'organization_name' => $isRequired ? 'required|string|max:255' : 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'website' => 'nullable|url|max:255',
            'logo_svg' => [
                'nullable',
                'string',
                function ($attribute, $value, $fail) {
                    // First check if it's base64 encoded
                    if (preg_match('/^[a-zA-Z0-9\/\r\n+]*={0,2}$/', $value)) {
                        $decodedValue = base64_decode($value, true);
                        if ($decodedValue === false) {
                            $fail('The '.$attribute.' must be a valid base64 encoded string.');
                            return;
                        }

                        // Check if decoded value is a valid SVG
                        if (!preg_match('/<\?xml.*?>.*?<svg|<svg/i', $decodedValue) || !str_ends_with(trim($decodedValue), '</svg>')) {
                            $fail('The '.$attribute.' must contain a valid SVG image.');
                        }
                    } else {
                        // If not base64, check if it's a valid SVG directly
                        if (!preg_match('/<\?xml.*?>.*?<svg|<svg/i', $value) || !str_ends_with(trim($value), '</svg>')) {
                            $fail('The '.$attribute.' must contain a valid SVG image.');
                        }
                    }
                },
            ],
            'contact_person_name' => 'nullable|string|max:255',
            'email' => $isRequired ? 'required|email|max:255' : 'sometimes|email|max:255',
            'active' => 'boolean'
        ];

        return $request->validate($rules);
    }

    /**
     * Set organizer attributes from validated data.
     *
     * @param Organizer $organizer
     * @param array $data
     * @return void
     */
    private function setOrganizerAttributes(Organizer $organizer, array $data): void
    {
        if (isset($data['organization_name'])) {
            $organizer->organization_name = $data['organization_name'];
        }

        if (isset($data['description'])) {
            $organizer->description = $data['description'];
        }

        if (isset($data['website'])) {
            $organizer->website = $data['website'];
        }

        if (isset($data['logo_svg'])) {
            // Check if the logo_svg is base64 encoded
            if (preg_match('/^[a-zA-Z0-9\/\r\n+]*={0,2}$/', $data['logo_svg'])) {
                // Attempt to decode base64
                $decodedSvg = base64_decode($data['logo_svg'], true);
                if ($decodedSvg !== false) {
                    // If successfully decoded, use the decoded value
                    $organizer->logo_svg = $decodedSvg;
                } else {
                    // If not valid base64, use as-is (fallback)
                    $organizer->logo_svg = $data['logo_svg'];
                }
            } else {
                // Not base64 encoded, use as-is
                $organizer->logo_svg = $data['logo_svg'];
            }
        }

        if (isset($data['contact_person_name'])) {
            $organizer->contact_person_name = $data['contact_person_name'];
        }

        if (isset($data['email'])) {
            $organizer->email = $data['email'];
        }

        if (isset($data['active'])) {
            $organizer->active = $data['active'];
        }
    }
}
