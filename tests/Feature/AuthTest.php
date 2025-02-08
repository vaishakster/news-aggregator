<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use App\Models\User;

class AuthTest extends TestCase
{
    use DatabaseTransactions;

    public function test_user_can_register()
    {
        $response = $this->postJson('/api/register', [
            'name' => 'Tolulope Akinnuoye',
            'email' => 'akinnuoyetolulope@gmail.com',
            'password' => 'password@123',
            'password_confirmation' => 'password@123',
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure(
            [
                'status',
                'message',
                'data' => [
                    'name',
                    'email',
                    'updated_at',
                    'created_at',
                    'id',
                ],
            ]
        );
    }

    public function test_user_can_login()
    {
        $user = User::factory()->create([
            'password' => bcrypt($password = 'password@1223'),
        ]);

        $response = $this->postJson('/api/login', [
            'email' => $user->email,
            'password' => $password,
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            "status",
            "message",
            "data" => [
                "token"
            ]
        ]);
    }
}
