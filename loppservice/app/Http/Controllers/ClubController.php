<?php

namespace App\Http\Controllers;

use App\Models\Club;
use App\Repositories\Interfaces\ClubRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

/**
 * Club API Controller for integration endpoints
 *
 * Handles CRUD operations for clubs via REST API
 */
class ClubController extends Controller
{
    protected ClubRepositoryInterface $clubRepository;

    public function __construct(ClubRepositoryInterface $clubRepository)
    {
        $this->clubRepository = $clubRepository;
    }

    /**
     * Get all clubs
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        try {
            $clubs = $this->clubRepository->all();
            return response()->json($clubs);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to retrieve clubs',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Create a new club
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        try {
            // Validate the request
            $validated = $request->validate([
                'club_uid' => 'sometimes|string|uuid|unique:clubs,club_uid',
                'name' => 'required|string|max:255',
                'acp_code' => 'nullable|string|max:20',
                'description' => 'nullable|string|max:1000',
                'official_club' => 'boolean'
            ]);

            // Generate UUID if not provided
            if (!isset($validated['club_uid'])) {
                $validated['club_uid'] = (string) Str::uuid();
            }

            // Set default values
            $validated['official_club'] = $validated['official_club'] ?? false;

            // Create the club
            $club = $this->clubRepository->create($validated);

            return response()->json($club, 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to create club',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get a specific club by ID
     *
     * @param string $id Club UID
     * @return JsonResponse
     */
    public function show(string $id): JsonResponse
    {
        try {
            $club = $this->clubRepository->findByUuid($id);

            if (!$club) {
                return response()->json([
                    'message' => 'Club not found'
                ], 404);
            }

            return response()->json($club);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to retrieve club',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update a club
     *
     * @param Request $request
     * @param string $id Club UID
     * @return JsonResponse
     */
    public function update(Request $request, string $id): JsonResponse
    {
        try {
            // Find the club
            $club = $this->clubRepository->findByUuid($id);

            if (!$club) {
                return response()->json([
                    'message' => 'Club not found'
                ], 404);
            }

            // Validate the request
            $validated = $request->validate([
                'name' => 'sometimes|required|string|max:255',
                'acp_code' => 'nullable|string|max:20',
                'description' => 'nullable|string|max:1000',
                'official_club' => 'sometimes|boolean'
            ]);

            // Update the club using the UUID, not the auto-increment ID
            $club->fill($validated);
            $club->save();

            return response()->json($club->fresh());
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to update club',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete a club
     *
     * @param string $id Club UID
     * @return JsonResponse
     */
    public function destroy(string $id): JsonResponse
    {
        try {
            // Find the club
            $club = $this->clubRepository->findByUuid($id);

            if (!$club) {
                return response()->json([
                    'message' => 'Club not found'
                ], 404);
            }

            // Check if club is in use (has associated registrations)
            if ($this->isClubInUse($club)) {
                return response()->json([
                    'message' => 'Cannot delete club that has associated registrations'
                ], 422);
            }

            // Delete the club using UUID
            $deleted = $this->clubRepository->deleteByUuid($id);

            if (!$deleted) {
                return response()->json([
                    'message' => 'Failed to delete club'
                ], 500);
            }

            return response()->json([
                'message' => 'Club deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to delete club',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Check if club is in use by any registrations
     *
     * @param Club $club
     * @return bool
     */
    private function isClubInUse(Club $club): bool
    {
        // Check if there are any registrations associated with this club
        return \App\Models\Registration::where('club_uid', $club->club_uid)->exists();
    }
}
