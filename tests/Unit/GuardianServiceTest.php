<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Services\NewsAggregator\GuardianService;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;

class GuardianServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_fetch_articles_success()
    {
        // Mock the Guzzle client
        $mockClient = Mockery::mock(Client::class);
        $mockResponse = new Response(200, [], json_encode([
            'response' => [
                'results' => [
                    [
                        'webTitle' => 'Guardian Article Title',
                        'fields' => [
                            'bodyText' => 'Sample body',
                            'byline' => 'Guardian Author',
                        ],
                        'webPublicationDate' => '2024-10-08T00:12:27Z',
                        'sectionName' => 'Technology',
                    ]
                ]
            ]
        ]));

        // Mock the response for the get method
        $mockClient->shouldReceive('get')->andReturn($mockResponse);

        // Inject the mock client into the GuardianService
        $guardianService = new GuardianService();
        $guardianService->client = $mockClient;

        // Execute the fetchArticles method
        $guardianService->fetchArticles();

        // Check if the article was created in the database
        $this->assertDatabaseHas('articles', [
            'title' => 'Guardian Article Title',
            'author' => 'Guardian Author',
            'category' => 'Technology',
        ]);
    }
}
