<?php

namespace Tests\Feature;

use App\Models\Preference;
use App\Models\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseTransactions;


class PreferenceTest extends TestCase
{

    use DatabaseTransactions;

    public function test_user_can_set_preferences()
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->postJson('/api/preferences', [
                'preferred_sources' => ['NYT', 'NewsAPI'],
                'preferred_categories' => ['sports', 'technology'],
            ])
            ->assertStatus(200)
            ->assertJsonStructure([
                "status",
                "message",
                "data" => array()
            ]);
    }

    public function test_user_can_retrieve_preferences()
    {
        $user = User::factory()->create();
        Preference::create([
            'user_id' => $user->id,
            'preferred_sources' => json_encode(['source1']),
            'preferred_categories' => json_encode(['technology']),
        ]);

        $response = $this->actingAs($user)->getJson('/api/preferences');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            "status",
            "message",
            "data" => array()
        ]);
    }
}
