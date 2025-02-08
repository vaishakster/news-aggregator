<?php

namespace App\Services;

use App\Models\Article;
use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ArticleService
{
    public static function view($id)
    {
        try {
            $article = Article::findOrFail($id);
            if (!$article) {
                return [false, "Article not found", 404];
            }
            return [true, $article, 200];
        } catch (Exception $e) {
            Log::error($e->getMessage());
            return [false, 'Error occurred trying to view article', 500];
        }
    }
}
