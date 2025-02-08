<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use App\Models\Article;
use App\Models\User;
use Exception;

class ArticleTest extends TestCase
{
    use DatabaseTransactions;

    public function test_can_fetch_articles_with_pagination()
    {

        $user = User::factory()->create();

        $this->actingAs($user);

        Article::factory()->count(15)->create();

        $response = $this->getJson('/api/articles');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'status',
            'message',
            'data' => array()
        ]);
    }

    public function test_can_filter_articles_by_keyword()
    {
        $user = User::factory()->create();

        $this->actingAs($user);

        Article::factory()->create(['title' => 'Unique Keyword Article']);
        Article::factory()->count(5)->create();

        $response = $this->getJson('/api/articles?keyword=Unique');
        \Illuminate\Support\Facades\Log::info(json_encode($response));
        $response->assertStatus(200);
        $response->assertJsonStructure(
            [
                "status",
                "message",
                "data" => [
                    "current_page",
                    "data" => array(),
                    "first_page_url",
                    "from",
                    "last_page",
                    "last_page_url",
                    "links" => array(),
                    "next_page_url",
                    "path",
                    "per_page",
                    "prev_page_url",
                    "to",
                    "total"
                ]
            ]
        );
    }
}
