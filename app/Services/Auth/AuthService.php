<?php

namespace App\Services\Auth;

use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;

class AuthService
{
    public function __construct(
        protected TokenService $tokenService
    ) {}

    /**
     * Register a new user with optional avatar upload.
     *
     * @param array $data
     * @param UploadedFile|null $image
     * @return User
     */
    public function register(array $data, ?UploadedFile $image = null): User
    {
        return DB::transaction(function () use ($data, $image) {
            $imagePath = null;

            if ($image) {
                $filename = $image->getClientOriginalName();
                $image->move(public_path('users_folder'), $filename);
                $imagePath = asset('users_folder/' . $filename);
            }

            $data['image'] = $imagePath;
            $data['password'] = Hash::make($data['password']);

            return User::create($data);
        });
    }

    /**
     * Attempt login with either email or phone and a password.
     *
     * @param string $emailOrPhone
     * @param string $password
     * @return array|null Returns ['user', 'token', 'refresh_token'] on success, null on failure.
     */
    public function attemptLogin(string $emailOrPhone, string $password): ?array
    {
        $fieldType = filter_var($emailOrPhone, FILTER_VALIDATE_EMAIL) ? 'email' : 'phone';

        $token = Auth::attempt([$fieldType => $emailOrPhone, 'password' => $password]);

        if (!$token) {
            return null;
        }

        /** @var User $user */
        $user = Auth::user();
        $refreshToken = $this->tokenService->generateRefreshToken($user);

        return [
            'user' => $user,
            'token' => $token,
            'refresh_token' => $refreshToken,
        ];
    }

    /**
     * Send password reset link to user's email.
     *
     * @param string $email
     * @return string Status code returned by Password broker.
     */
    public function sendResetLink(string $email): string
    {
        return Password::sendResetLink(['email' => $email]);
    }

    /**
     * Reset user password using the broker.
     *
     * @param array $credentials Contains email, token, password, password_confirmation
     * @return string Status code returned by Password broker.
     */
    public function resetPassword(array $credentials): string
    {
        return Password::reset(
            $credentials,
            function ($user, $password) {
                $user->forceFill([
                    'password' => Hash::make($password),
                ])->save();
            }
        );
    }

    /**
     * Authenticate or register an OAuth Google user.
     *
     * @param object $googleUser
     * @return array
     */
    public function handleGoogleUser(object $googleUser): array
    {
        $user = User::where('provider_id', $googleUser->id)
            ->where('provider', 'google')
            ->first();

        if (!$user) {
            $user = User::create([
                'name' => $googleUser->name,
                'email' => $googleUser->email,
                'provider' => 'google',
                'provider_id' => $googleUser->id,
                'image' => $googleUser->avatar,
                'password' => Hash::make(Str::random(24)),
            ]);
        }

        $token = Auth::login($user);

        return [
            'user' => $user,
            'token' => $token,
        ];
    }
}
