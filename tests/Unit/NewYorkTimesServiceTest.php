<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Services\NewsAggregator\NewYorkTimesService;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;

class NewYorkTimesServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_fetch_articles_success()
    {
        // Mock the Guzzle client
        $mockClient = Mockery::mock(Client::class);
        $mockResponse = new Response(200, [], json_encode([
            'results' => [
                [
                    'title' => 'NY Times Article Title',
                    'abstract' => 'Sample abstract',
                    'byline' => 'NY Times Author',
                    'published_date' => '2024-10-08T00:12:27Z',
                    'section' => 'Technology',
                ]
            ]
        ]));

        // Mock the response for the get method
        $mockClient->shouldReceive('get')->andReturn($mockResponse);

        // Inject the mock client into the NewYorkTimesService
        $nytService = new NewYorkTimesService();
        $nytService->client = $mockClient;

        // Execute the fetchArticles method
        $nytService->fetchArticles();

        // Check if the article was created in the database
        $this->assertDatabaseHas('articles', [
            'title' => 'NY Times Article Title',
            'author' => 'NY Times Author',
            'category' => 'Technology',
        ]);
    }
}
