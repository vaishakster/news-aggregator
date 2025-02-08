<?php

namespace App\Services\NewsAggregator;

use GuzzleHttp\Client;
use App\Models\Article;
use Carbon\Carbon;

class NewYorkTimesService implements NewsSourceInterface
{
    public $client;
    protected $apiKey;

    public function __construct()
    {
        $this->client = new Client();
        $this->apiKey = env('NYT_API_KEY');  // Make sure to add this key to your .env file
    }

    public function fetchArticles()
    {
        try {
            $response = $this->client->get('https://api.nytimes.com/svc/mostpopular/v2/emailed/7.json', [
                'query' => [
                    'api-key' => $this->apiKey,
                ]
            ]);

            $data = json_decode($response->getBody()->getContents(), true);

            foreach ($data['results'] as $articleData) {
                $publishedAt = Carbon::parse($articleData['published_date'])->format('Y-m-d H:i:s');

                Article::updateOrCreate([
                    'title' => $articleData['title'],
                ], [
                    'body' => $articleData['abstract'],
                    'author' => $articleData['byline'] ?? 'Unknown',
                    'source' => 'The New York Times',
                    'category' => $articleData['section'] ?? 'general',
                    'published_at' => $publishedAt,
                ]);
            }

            \Log::info('Successfully fetched ' . count($data['results']) . ' articles from the New York Times.');
        } catch (\Exception $e) {
            \Log::error('Failed to fetch articles from The New York Times: ' . $e->getMessage());
        }
    }
}
