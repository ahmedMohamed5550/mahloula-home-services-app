<?php

namespace Tests\Unit;

use App\Models\User;
use App\Services\Auth\AuthService;
use App\Services\Auth\TokenService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthServiceTest extends TestCase
{
    use RefreshDatabase;

    protected AuthService $authService;
    protected TokenService $tokenService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->tokenService = new TokenService();
        $this->authService = new AuthService($this->tokenService);
    }

    public function test_register_creates_user_with_hashed_password()
    {
        $data = [
            'name' => 'Unit User',
            'email' => 'unituser@example.com',
            'password' => 'PlainSecret123',
            'phone' => '01122334455',
            'userType' => 'user',
            'timezone' => 'UTC',
        ];

        $user = $this->authService->register($data);

        $this->assertInstanceOf(User::class, $user);
        $this->assertEquals('unituser@example.com', $user->email);
        $this->assertTrue(Hash::check('PlainSecret123', $user->password));
        $this->assertDatabaseHas('users', ['email' => 'unituser@example.com']);
    }

    public function test_attempt_login_returns_token_and_user_on_success()
    {
        $user = User::factory()->create([
            'email' => 'loginunit@example.com',
            'password' => Hash::make('CorrectPassword123'),
        ]);

        $result = $this->authService->attemptLogin('loginunit@example.com', 'CorrectPassword123');

        $this->assertNotNull($result);
        $this->assertEquals($user->id, $result['user']->id);
        $this->assertNotEmpty($result['token']);
        $this->assertNotEmpty($result['refresh_token']);
    }

    public function test_attempt_login_returns_null_on_failure()
    {
        User::factory()->create([
            'email' => 'wrongpass@example.com',
            'password' => Hash::make('CorrectPassword123'),
        ]);

        $result = $this->authService->attemptLogin('wrongpass@example.com', 'WrongPassword123');

        $this->assertNull($result);
    }

    public function test_handle_google_user_creates_new_user()
    {
        $googleUser = (object) [
            'id' => 'google-test-id-999',
            'name' => 'Google Newbie',
            'email' => 'googlenewbie@example.com',
            'avatar' => 'https://example.com/pic.jpg',
        ];

        $result = $this->authService->handleGoogleUser($googleUser);

        $this->assertNotNull($result);
        $this->assertEquals('googlenewbie@example.com', $result['user']->email);
        $this->assertEquals('google-test-id-999', $result['user']->provider_id);
        $this->assertNotEmpty($result['token']);
        $this->assertDatabaseHas('users', ['provider_id' => 'google-test-id-999']);
    }

    public function test_handle_google_user_reuses_existing_user()
    {
        $existing = User::factory()->create([
            'email' => 'existinggoogle@example.com',
            'provider' => 'google',
            'provider_id' => 'google-existing-111',
        ]);

        $googleUser = (object) [
            'id' => 'google-existing-111',
            'name' => 'Existing Google User',
            'email' => 'existinggoogle@example.com',
            'avatar' => 'https://example.com/pic.jpg',
        ];

        $result = $this->authService->handleGoogleUser($googleUser);

        $this->assertEquals($existing->id, $result['user']->id);
        $this->assertNotEmpty($result['token']);
        $this->assertEquals(1, User::where('provider_id', 'google-existing-111')->count());
    }
}
