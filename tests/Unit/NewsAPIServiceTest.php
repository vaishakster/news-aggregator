<?php

namespace Tests\Unit;

use App\Services\NewsAggregator\NewsAPIService;
use Tests\TestCase;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Foundation\Testing\RefreshDatabase;

class NewsAPIServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_fetch_articles_success()
    {
        // Mock the Guzzle client
        $mockClient = \Mockery::mock(Client::class);
        $mockResponse = new Response(200, [], json_encode([
            'articles' => [
                [
                    'title' => 'Sample Title',
                    'description' => 'Sample description',
                    'author' => 'John Doe',
                    'publishedAt' => '2024-10-08T00:12:27Z',
                ],
            ]
        ]));

        $mockClient->shouldReceive('get')->andReturn($mockResponse);

        // Inject the mock client into the service
        $newsAPIService = new NewsAPIService();
        $newsAPIService->client = $mockClient;

        // Run the method and assert the results
        $newsAPIService->fetchArticles();

        // Check if the article was created in the database
        $this->assertDatabaseHas('articles', [
            'title' => 'Sample Title',
        ]);
    }
}
