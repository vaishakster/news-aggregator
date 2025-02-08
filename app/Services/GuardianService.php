<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class GuardianService
{
    protected $baseUrl;
    protected $apiKey;

    public function __construct()
    {
        $this->baseUrl = 'https://content.guardianapis.com';
        $this->apiKey = config('services.guardian.key');
    }

    /**
     * Fetch articles from The Guardian.
     */
    public function fetchArticles($section = 'technology')
    {
        // Prepare the request parameters
        $params = [
            'api-key' => $this->apiKey,
            'show-fields' => 'headline,bodyText',
        ];

        // Add section only if defined and valid
        if ($section) {
            $params['section'] = $section;
        }

        // Make the API request
        $response = Http::get("{$this->baseUrl}/search", $params);

        if ($response->successful()) {
            // Parse and return articles
            return collect($response->json()['response']['results'] ?? [])->map(function ($article) use ($section) {
                return [
                    'title' => $article['webTitle'],
                    'description' => $article['fields']['bodyText'] ?? '',
                    'category' => $section,
                    'source' => 'The Guardian',
                ];
            })->toArray();
        }

        return [];
    }
}
