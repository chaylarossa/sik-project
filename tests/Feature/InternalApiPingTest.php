<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class InternalApiPingTest extends TestCase
{
    use RefreshDatabase;

    public function test_internal_ping_requires_authentication(): void
    {
        $response = $this->getJson('/api/internal/ping');

        $response->assertUnauthorized();
    }

    public function test_internal_ping_returns_success_for_authenticated_user(): void
    {
        $user = User::factory()->create();

        Sanctum::actingAs($user);

        $response = $this->getJson('/api/internal/ping');

        $response
            ->assertOk()
            ->assertJson(['message' => 'pong']);
    }
}
