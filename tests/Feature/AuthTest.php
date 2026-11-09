<?php

namespace Tests\Feature;

use App\Models\RefreshToken;
use App\Models\User;
use App\Notifications\CustomResetPasswordNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Storage;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\User as SocialiteUser;
use Mockery;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_register_with_valid_data()
    {
        Storage::fake('public');

        $payload = [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'Password123!',
            'phone' => '01012345678',
            'userType' => 'user',
            'timezone' => 'UTC',
        ];

        $response = $this->postJson('/api/register', $payload);

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'User registered successfully',
                'status' => 200,
                'data' => [
                    'name' => 'Test User',
                    'email' => 'test@example.com',
                    'phone' => '01012345678',
                    'userType' => 'user',
                ],
            ]);

        $this->assertDatabaseHas('users', [
            'email' => 'test@example.com',
            'phone' => '01012345678',
        ]);

        $user = User::where('email', 'test@example.com')->first();
        $this->assertTrue(Hash::check('Password123!', $user->password));
    }

    public function test_registration_validation_fails_for_duplicate_email()
    {
        User::factory()->create(['email' => 'existing@example.com', 'phone' => '01000000001']);

        $payload = [
            'name' => 'Another User',
            'email' => 'existing@example.com',
            'password' => 'Password123!',
            'phone' => '01000000002',
            'userType' => 'user',
            'timezone' => 'UTC',
        ];

        $response = $this->postJson('/api/register', $payload);
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }

    public function test_user_can_login_with_email()
    {
        $user = User::factory()->create([
            'email' => 'loginuser@example.com',
            'phone' => '01099998888',
            'password' => Hash::make('Secret123!'),
        ]);

        $response = $this->postJson('/api/login', [
            'email_or_phone' => 'loginuser@example.com',
            'password' => 'Secret123!',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'message',
                'status',
                'data' => [
                    'user' => ['id', 'name', 'email', 'phone', 'userType'],
                    'authorisation' => ['token', 'refresh_token', 'type', 'expires_in', 'refresh_expires_in'],
                ],
            ])
            ->assertJson([
                'message' => 'User login successfully',
                'status' => 200,
            ]);

        $this->assertDatabaseHas('refresh_tokens', [
            'user_id' => $user->id,
        ]);
    }

    public function test_user_can_login_with_phone()
    {
        $user = User::factory()->create([
            'email' => 'phoneuser@example.com',
            'phone' => '01055554444',
            'password' => Hash::make('Secret123!'),
        ]);

        $response = $this->postJson('/api/login', [
            'email_or_phone' => '01055554444',
            'password' => 'Secret123!',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'User login successfully',
                'status' => 200,
            ]);
    }

    public function test_login_fails_with_invalid_credentials()
    {
        User::factory()->create([
            'email' => 'wrongpass@example.com',
            'password' => Hash::make('CorrectPassword'),
        ]);

        $response = $this->postJson('/api/login', [
            'email_or_phone' => 'wrongpass@example.com',
            'password' => 'WrongPassword',
        ]);

        $response->assertStatus(401)
            ->assertJson([
                'message' => 'Unauthorized. Incorrect email or password.',
                'status' => 401,
            ]);
    }

    public function test_refresh_token_rotates_and_returns_new_tokens()
    {
        $user = User::factory()->create();
        $refreshToken = JWTAuth::customClaims([
            'refresh' => true,
            'exp' => now()->addDays(7)->timestamp,
        ])->fromUser($user);

        RefreshToken::create([
            'user_id' => $user->id,
            'token' => $refreshToken,
            'expires_at' => now()->addDays(7),
        ]);

        $response = $this->postJson('/api/refresh-token', [
            'refresh_token' => $refreshToken,
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'message',
                'status',
                'data' => [
                    'user',
                    'authorisation' => ['token', 'refresh_token'],
                ],
            ])
            ->assertJson([
                'message' => 'Token refreshed successfully',
                'status' => 200,
            ]);
    }

    public function test_refresh_token_fails_when_token_is_missing()
    {
        $response = $this->postJson('/api/refresh-token', []);

        $response->assertStatus(400)
            ->assertJson([
                'message' => 'Refresh token is required.',
                'status' => 400,
            ]);
    }

    public function test_logout_invalidates_token_and_deletes_refresh_token()
    {
        $user = User::factory()->create();
        $token = JWTAuth::fromUser($user);

        RefreshToken::create([
            'user_id' => $user->id,
            'token' => 'dummy_token',
            'expires_at' => now()->addDays(7),
        ]);

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/logout');

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Successfully logged out',
                'status' => 200,
            ]);

        $this->assertDatabaseMissing('refresh_tokens', [
            'user_id' => $user->id,
        ]);
    }

    public function test_forgot_password_sends_reset_notification()
    {
        Notification::fake();

        $user = User::factory()->create(['email' => 'resetme@example.com']);

        $response = $this->postJson('/api/forgot-password', [
            'email' => 'resetme@example.com',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Send Reset Link to Mail Successfully',
                'status' => 200,
            ]);

        Notification::assertSentTo($user, CustomResetPasswordNotification::class);
    }

    public function test_forgot_password_fails_for_nonexistent_email()
    {
        $response = $this->postJson('/api/forgot-password', [
            'email' => 'doesnotexist@example.com',
        ]);

        $response->assertStatus(422)
            ->assertJson([
                'message' => 'Validation error',
                'status' => 422,
            ]);
    }

    public function test_reset_password_updates_user_password()
    {
        $user = User::factory()->create(['email' => 'updatepass@example.com']);
        $token = Password::createToken($user);

        $response = $this->postJson('/api/reset-password', [
            'email' => 'updatepass@example.com',
            'token' => $token,
            'password' => 'NewSecurePassword123!',
            'password_confirmation' => 'NewSecurePassword123!',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Reset Password Successfully',
                'status' => 200,
            ]);

        $user->refresh();
        $this->assertTrue(Hash::check('NewSecurePassword123!', $user->password));
    }

    public function test_reset_password_token_endpoint_returns_token()
    {
        $response = $this->getJson('/api/reset-password/sample-test-token');

        $response->assertStatus(200)
            ->assertJson([
                'token' => 'sample-test-token',
            ]);
    }

    public function test_google_redirect_returns_redirect_response()
    {
        $provider = Mockery::mock('Laravel\Socialite\Contracts\Provider');
        $provider->shouldReceive('redirect')
            ->once()
            ->andReturn(redirect('https://accounts.google.com/o/oauth2/v2/auth'));

        Socialite::shouldReceive('driver')
            ->with('google')
            ->once()
            ->andReturn($provider);

        $response = $this->get('/api/auth/google');
        $response->assertStatus(302);
        $this->assertStringContainsString('accounts.google.com', $response->getTargetUrl());
    }

    public function test_google_callback_creates_and_logs_in_user()
    {
        $socialiteUser = new SocialiteUser;
        $socialiteUser->id = 'google-unique-id-12345';
        $socialiteUser->name = 'Google User';
        $socialiteUser->email = 'googleuser@example.com';
        $socialiteUser->avatar = 'https://example.com/avatar.jpg';

        $provider = Mockery::mock('Laravel\Socialite\Contracts\Provider');
        $provider->shouldReceive('stateless')->once()->andReturnSelf();
        $provider->shouldReceive('user')->once()->andReturn($socialiteUser);

        Socialite::shouldReceive('driver')->with('google')->once()->andReturn($provider);

        $response = $this->getJson('/api/auth/google/callback');

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'User logged in successfully with Google',
                'status' => 200,
                'data' => [
                    'user' => [
                        'name' => 'Google User',
                        'email' => 'googleuser@example.com',
                    ],
                ],
            ]);

        $this->assertDatabaseHas('users', [
            'email' => 'googleuser@example.com',
            'provider' => 'google',
            'provider_id' => 'google-unique-id-12345',
        ]);
    }

    public function test_admin_web_login_authenticates_admin()
    {
        $admin = User::factory()->create([
            'email' => 'adminuser@example.com',
            'password' => Hash::make('AdminPass123'),
            'userType' => 'admin',
        ]);

        $response = $this->post('/admin/login', [
            'email' => 'adminuser@example.com',
            'password' => 'AdminPass123',
        ]);

        $response->assertRedirect(route('admin.dashboard'));
        $this->assertAuthenticatedAs($admin, 'web');
    }

    public function test_admin_web_login_rejects_non_admin_user()
    {
        $user = User::factory()->create([
            'email' => 'regularuser@example.com',
            'password' => Hash::make('RegularPass123'),
            'userType' => 'user',
        ]);

        $response = $this->post('/admin/login', [
            'email' => 'regularuser@example.com',
            'password' => 'RegularPass123',
        ]);

        $response->assertRedirect();
        $this->assertGuest('web');
    }

    public function test_admin_web_logout_logs_out_session()
    {
        $admin = User::factory()->create(['userType' => 'admin']);

        $response = $this->actingAs($admin, 'web')->get('/admin/logout');

        $response->assertRedirect('/');
        $this->assertGuest('web');
    }
}
