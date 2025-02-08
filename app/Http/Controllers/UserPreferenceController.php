<?php

namespace App\Http\Controllers;

use App\Models\UserPreference;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Article;
use Illuminate\Support\Facades\Cache;

class UserPreferenceController extends Controller
{

    /**
     * @OA\Post(
     *      path="/api/preferences",
     *      operationId="setPreferences",
     *      tags={"User Preferences"},
     *      summary="Set user preferences",
     *      description="Set the user's preferred sources, categories, and authors",
     *      security={{ "sanctum": {} }},
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              @OA\Property(property="preferred_sources", type="string", example="BBC,The Guardian"),
     *              @OA\Property(property="preferred_categories", type="string", example="Technology, Sports"),
     *              @OA\Property(property="preferred_authors", type="string", example="John Doe, Jane Doe")
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Preferences updated successfully"
     *      ),
     *      @OA\Response(
     *          response=422,
     *          description="Validation error"
     *      )
     * )
     */
    public function setPreferences(Request $request)
    {
        $request->validate([
            'preferred_sources' => 'nullable|string',
            'preferred_categories' => 'nullable|string',
            'preferred_authors' => 'nullable|string',
        ]);

        $user = Auth::user();
        $preference = $user->preference()->firstOrCreate([]);

        $preference->update([
            'preferred_sources' => $request->preferred_sources,
            'preferred_categories' => $request->preferred_categories,
            'preferred_authors' => $request->preferred_authors,
        ]);

        $this->clearUserFeedCache($user);
        return response()->json(['message' => 'Preferences updated successfully!']);
    }

    protected function clearUserFeedCache($user)
    {
        for ($page = 1; $page <= 10; $page++) {
            Cache::forget("user_feed_{$user->id}_page_{$page}");
        }
    }

    /**
     * @OA\Get(
     *      path="/api/preferences",
     *      operationId="getPreferences",
     *      tags={"User Preferences"},
     *      summary="Get user preferences",
     *      description="Get the user's preferences",
     *      security={{ "sanctum": {} }},
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation"
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Preferences not found"
     *      )
     * )
     */
    public function getPreferences()
    {
        $user = Auth::user();
        $preference = $user->preference;

        if (! $preference) {
            return response()->json(['message' => 'No preferences set'], 404);
        }

        return response()->json($preference);
    }

    /**
     * @OA\Get(
     *      path="/api/feed",
     *      operationId="getPersonalizedFeed",
     *      tags={"User Preferences"},
     *      summary="Get personalized feed",
     *      description="Get a personalized feed of articles based on the user's preferences",
     *      security={{ "sanctum": {} }},
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation"
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Preferences not set"
     *      )
     * )
     */
    public function getPersonalizedFeed()
    {
        $user = Auth::user();
        $page = request()->get('page', 1);
        $cacheKey = 'user_feed_' . $user->id . "_page_{$page}";
    
        $feed = Cache::remember($cacheKey, now()->addMinutes(10), function () use ($user) {
            $preference = $user->preference;
    
            if (! $preference) {
                return response()->json(['message' => 'No preferences set'], 404);
            }
    
            $query = Article::query();
    
            if ($preference->preferred_sources) {
                $query->where('source', $preference->preferred_sources);
            }
    
            if ($preference->preferred_categories) {
                $query->orWhere('category', $preference->preferred_categories);
            }
    
            if ($preference->preferred_authors) {
                $query->orWhere('author', $preference->preferred_authors);
            }
    
            return $query->paginate(10);
        });
    
        return response()->json($feed);
    }
}
