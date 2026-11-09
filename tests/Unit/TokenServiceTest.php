<?php

namespace Tests\Unit;

use App\Models\RefreshToken;
use App\Models\User;
use App\Services\Auth\TokenService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Facades\JWTAuth;

class TokenServiceTest extends TestCase
{
    use RefreshDatabase;

    protected TokenService $tokenService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->tokenService = new TokenService();
    }

    public function test_generate_refresh_token_stores_in_database()
    {
        $user = User::factory()->create();

        $token = $this->tokenService->generateRefreshToken($user);

        $this->assertNotEmpty($token);
        $this->assertDatabaseHas('refresh_tokens', [
            'user_id' => $user->id,
            'token' => $token,
        ]);
    }

    public function test_rotate_refresh_token_creates_new_tokens()
    {
        $user = User::factory()->create();
        $initialRefreshToken = $this->tokenService->generateRefreshToken($user);

        $result = $this->tokenService->rotateRefreshToken($initialRefreshToken);

        $this->assertArrayHasKey('user', $result);
        $this->assertArrayHasKey('token', $result);
        $this->assertArrayHasKey('refresh_token', $result);
        $this->assertEquals($user->id, $result['user']->id);
        $this->assertNotEmpty($result['token']);
        $this->assertNotEmpty($result['refresh_token']);

        $this->assertDatabaseHas('refresh_tokens', [
            'user_id' => $user->id,
            'token' => $result['refresh_token'],
        ]);
    }

    public function test_rotate_refresh_token_throws_for_invalid_claim()
    {
        $user = User::factory()->create();
        // Generate standard token without 'refresh' => true claim
        $standardToken = JWTAuth::fromUser($user);

        $this->expectException(TokenInvalidException::class);
        $this->tokenService->rotateRefreshToken($standardToken);
    }

    public function test_invalidate_user_tokens_deletes_records()
    {
        $user = User::factory()->create();
        $this->tokenService->generateRefreshToken($user);

        $this->assertDatabaseHas('refresh_tokens', ['user_id' => $user->id]);

        $this->tokenService->invalidateUserTokens($user);

        $this->assertDatabaseMissing('refresh_tokens', ['user_id' => $user->id]);
    }
}
