<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseHelper;
use App\Models\Article;
use App\Services\ArticleService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * Controller for managing articles.
 */
class ArticleController extends Controller
{
    /**
     * Display a listing of the articles.
     *
     * @OA\Get(
     *     path="/api/articles",
     *     summary="Get list of articles",
     *     description="Fetches list of articles with optional filters for keyword, category, and source. Returns paginated results.",
     *     security={{"Bearer":{}}},
     *     tags={"Articles"},
     *     @OA\Parameter(
     *         name="keyword",
     *         in="query",
     *         description="Keyword to search in title and content",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="category",
     *         in="query",
     *         description="Filter articles by category",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="source",
     *         in="query",
     *         description="Filter articles by source",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="List of articles",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Articles fetched successfully"),
     *             @OA\Property(property="data", type="array",
     *                 @OA\Items(ref="#/components/schemas/Article")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="Unauthenticated")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Server error",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="string", example="failed"),
     *             @OA\Property(property="message", type="string", example="An error occurred"),
     *             @OA\Property(property="data")
     *         )
     *     )
     * 
     * )
     * @OA\Schema(
     *     schema="Article",
     *     type="object",
     *     properties={
     *         @OA\Property(property="id", type="integer", description="The article ID"),
     *         @OA\Property(property="title", type="string", description="The article title"),
     *         @OA\Property(property="content", type="string", description="The main content of the article"),
     *         @OA\Property(property="category", type="string", description="Category of the article"),
     *         @OA\Property(property="source", type="string", description="Source of the article"),
     *         @OA\Property(property="created_at", type="string", format="date-time", description="Creation timestamp"),
     *         @OA\Property(property="updated_at", type="string", format="date-time", description="Update timestamp")
     *     }
     * )
     */
    public function index(Request $request)
    {
        try {
            $query = Article::query();

            if ($request->has('keyword')) {
                $query->where('title', 'like', '%' . $request->keyword . '%');
            }

            if ($request->has('category')) {
                $query->where('category', $request->category);
            }

            if ($request->has('source')) {
                $query->where('source', $request->source);
            }
            // Return the paginated query result, with 10 items per page
            return ResponseHelper::success($query->paginate(10), 'Articles fetched successfully', 200);
        } catch (Exception $e) {
            Log::error($e->getMessage());
            return ResponseHelper::error('Error occured while fetching article', 500);
        }
    }

    /**
     * Display a specific article.
     *
     * @OA\Get(
     *     path="/api/articles/{id}",
     *     summary="Get a specific article",
     *     description="Fetch a specific article by its ID.",
     *     tags={"Articles"},
     *     security={{"Bearer":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the article to fetch",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Article found",
     *         @OA\JsonContent(ref="#/components/schemas/Article")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Article not found"
     *     )
     * )
     */
    public function show($id)
    {
        $view_article = ArticleService::view($id);

        if (!$view_article[0]) {
            return ResponseHelper::error($view_article[1], $view_article[2]);
        }
        return ResponseHelper::success($view_article[1], 'Article fetched successfully', 200);
    }
}
