<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class NYTService
{
    protected $baseUrl;
    protected $apiKey;

    public function __construct()
    {
        $this->baseUrl = 'https://api.nytimes.com/svc/topstories/v2';
        $this->apiKey = config('services.nyt.key');
    }

    /**
     * Fetch articles from the New York Times.
     */
    public function fetchArticles($section = 'technology')
    {
        $response = Http::get("{$this->baseUrl}/{$section}.json", [
            'api-key' => $this->apiKey,
        ]);

        if ($response->successful()) {
            // Parse and return articles
            return collect($response->json()['results'] ?? [])->map(function ($article) {
                return [
                    'title' => $article['title'],
                    'description' => $article['abstract'],
                    'category' => $article['section'] ?? 'general',
                    'source' => 'New York Times',
                ];
            })->toArray();
        }

        return [];
    }
}
