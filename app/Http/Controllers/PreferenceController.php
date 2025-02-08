<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseHelper;
use App\Http\Requests\PreferenceRequest;
use App\Models\Preference;
use App\Models\Article;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class PreferenceController extends Controller
{
    /**
     * Store or update user preferences.
     *
     * @OA\Post(
     *     path="/api/preferences",
     *     tags={"User Preferences"},
     *     summary="Set or update user preferences",
     *     description="Allows a user to set or update their preferred news sources, categories, and authors.",
     *     security={{"bearerAuth": {}}},
     *     operationId="setPreferences",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"preferred_sources", "preferred_categories"},
     *             @OA\Property(property="preferred_sources", type="array", @OA\Items(type="string"), example={"9to5google.com", "Blizzard.com"}),
     *             @OA\Property(property="preferred_categories", type="array", @OA\Items(type="string"), example={"general", "technology"}),
     *             @OA\Property(property="preferred_authors", type="array", @OA\Items(type="string"), example={"NYT", "NewsAPI"})
     *         )
     *     ),
     * @OA\Response(
     *     response=200,
     *     description="preference stored successfully",
     *     @OA\JsonContent(
     *         @OA\Property(property="status", type="string", example="success"),
     *         @OA\Property(property="message", type="string", example="preference stored successfully"),
     *         @OA\Property(
     *             property="data",
     *             type="object",
     *             @OA\Property(property="preferred_sources", type="array", @OA\Items(type="string")),
     *             @OA\Property(property="preferred_categories", type="array", @OA\Items(type="string")),
     *             @OA\Property(property="preferred_authors", type="array", @OA\Items(type="string"))
     *         )
     *     )
     * ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="failed"),
     *             @OA\Property(property="message", type="string", example="Validation error"),
     *             @OA\Property(property="data", nullable=true, example=null)
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Server error",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="failed"),
     *             @OA\Property(property="message", type="string", example="An error occurred"),
     *             @OA\Property(property="data", nullable=true, example=null)
     *         )
     *     )
     * )
     */
    public function store(PreferenceRequest $request)
    {
        try {
            // update or create preference record for the authenticated user
            $preference = Preference::updateOrCreate(
                ['user_id' => Auth::id()],
                [
                    'preferred_sources' => $request->preferred_sources ?? [],
                    'preferred_categories' => $request->preferred_categories ?? [],
                    'preferred_authors' => $request->preferred_authors ?? [],
                ]
            );
            return ResponseHelper::success($preference, "preference stored successfully");
        } catch (Exception $e) {
            Log::error($e->getMessage());
            return ResponseHelper::error();
        }
    }

    /**
     * Retrieve the authenticated user's preferences.
     * @OA\Get(
     *     path="/api/preferences",
     *     tags={"User Preferences"},
     *     summary="Retrieve user preferences",
     *     description="Fetches the user's saved preferences for news sources, categories, and authors.",
     *     security={{"bearerAuth": {}}},
     *     operationId="getPreferences",
     *     @OA\Response(
     *         response=200,
     *         description="Retrieve User preferences",
     *         @OA\JsonContent(
     *           @OA\Property(property="status", type="string", example="success"),
     *         @OA\Property(property="message", type="string", example="User preferences retrieved successfully"),
     *         @OA\Property(
     *             property="data",
     *             type="object",
     *             @OA\Property(property="preferred_sources", type="array", @OA\Items(type="string")),
     *             @OA\Property(property="preferred_categories", type="array", @OA\Items(type="string")),
     *             @OA\Property(property="preferred_authors", type="array", @OA\Items(type="string"))
     *         )
     *          
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Preferences not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="failed"),
     *             @OA\Property(property="message", type="string", example="Preferences not found"),
     *             @OA\Property(property="data", nullable=true, example=null)
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Server error",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="failed"),
     *             @OA\Property(property="message", type="string", example="An error occurred"),
     *             @OA\Property(property="data", nullable=true, example=null)
     *         )
     *     )
     * )
     */

    public function show()
    {
        try {
            $preference = Auth::user()->preference;

            return ResponseHelper::success($preference, "preference successfully retrieved", 200);
        } catch (Exception $e) {
            Log::error($e->getMessage());
            return ResponseHelper::error();
        }
    }


    /**
     * Fetch a personalized news feed based on user preferences.
     *
     * @OA\Get(
     *     path="/api/personalized-feed",
     *     tags={"User Preferences"},
     *     summary="Fetch personalized news feed",
     *     description="Generates a personalized news feed based on the user's preferences for sources, categories, and authors.",
     *     security={{"bearerAuth": {}}},
     *     operationId="getPersonalizedFeed",
     *     @OA\Response(
     *         response=200,
     *         description="Fetch personalized news feed",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Personalized news feed retrieved successfully"),
     *             @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/Article")),
     *             @OA\Property(property="links", type="object"),
     *             @OA\Property(property="meta", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Preferences not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="failed"),
     *             @OA\Property(property="message", type="string", example="No preferences found for the user"),
     *             @OA\Property(property="data", nullable=true, example=null)
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Server error",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="failed"),
     *             @OA\Property(property="message", type="string", example="An error occurred"),
     *             @OA\Property(property="data", nullable=true, example=null)
     *         )
     *     )
     * )
     * 
     */
    public function personalizedFeed()
    {
        try {
            $userPreference = Auth::user()->preference;

            if (!$userPreference) {
                return ResponseHelper::error('No preferences found for the user', 404);
            }

            // Query the articles based on user preferences
            $articles = Article::query()
                ->when($userPreference->preferred_sources, function ($query, $sources) {
                    $query->whereIn('source', (array)$sources);
                })
                ->when($userPreference->preferred_categories, function ($query, $categories) {
                    $query->whereIn('category', (array)$categories);
                })
                ->when($userPreference->preferred_authors, function ($query, $authors) {
                    $query->whereIn('author', (array)$authors);
                })
                ->paginate(10);
            if(count($articles)>0)
                return ResponseHelper::success($articles, 'personalized feeds successfully fetched',200);
            else
                return ResponseHelper::success('No Articles found for the preferences', 200);
        } catch (Exception $e) {
            Log::error($e->getMessage());
            return ResponseHelper::error();
        }
    }
}
