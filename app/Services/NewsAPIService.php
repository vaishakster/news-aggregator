<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class NewsAPIService
{
    protected $baseUrl;
    protected $apiKey;

    public function __construct()
    {
        $this->baseUrl = 'https://newsapi.org/v2';
        $this->apiKey = config('services.newsapi.key');
    }

    /**
     * Fetch articles from NewsAPI.
     */
    public function fetchArticles($category = 'technology')
    {
        $response = Http::get("{$this->baseUrl}/top-headlines", [
            'category' => $category,
            'apiKey' => $this->apiKey,
            'country' => 'us'
        ]);

        if ($response->successful()) {
            return $response->json()['articles'];
        }

        return [];
    }
}
