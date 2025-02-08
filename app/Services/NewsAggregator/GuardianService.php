<?php

namespace App\Services\NewsAggregator;

use GuzzleHttp\Client;
use App\Models\Article;
use Carbon\Carbon;

class GuardianService implements NewsSourceInterface
{
    public $client;
    protected $apiKey;

    public function __construct()
    {
        $this->client = new Client();
        $this->apiKey = env('GUARDIAN_API_KEY');  // Make sure to add this key to your .env file
    }

    public function fetchArticles()
    {
        try {
            $response = $this->client->get('https://content.guardianapis.com/search', [
                'query' => [
                    'api-key' => $this->apiKey,
                    'section' => 'technology',
                    'show-fields' => 'all'
                ]
            ]);

            $data = json_decode($response->getBody()->getContents(), true);

            foreach ($data['response']['results'] as $articleData) {
                $publishedAt = Carbon::parse($articleData['webPublicationDate'])->format('Y-m-d H:i:s');

                Article::updateOrCreate([
                    'title' => $articleData['webTitle'],
                ], [
                    'body' => $articleData['fields']['bodyText'] ?? 'No description',
                    'author' => $articleData['fields']['byline'] ?? 'Unknown',
                    'source' => 'The Guardian',
                    'category' => $articleData['sectionName'] ?? 'general',
                    'published_at' => $publishedAt,
                ]);
            }

            \Log::info('Successfully fetched ' . count($data['response']['results']) . ' articles from Guardian.');
        } catch (\Exception $e) {
            \Log::error('Failed to fetch articles from The Guardian: ' . $e->getMessage());
        }
    }
}
