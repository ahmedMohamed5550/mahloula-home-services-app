<?php

namespace App\Services\Auth;

use App\Models\RefreshToken;
use App\Models\User;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Facades\JWTAuth;

class TokenService
{
    /**
     * Generate and store a persistent refresh token for the user.
     *
     * @param User $user
     * @return string
     */
    public function generateRefreshToken(User $user): string
    {
        $refreshToken = JWTAuth::customClaims([
            'refresh' => true,
            'exp' => now()->addDays(7)->timestamp,
        ])->fromUser($user);

        RefreshToken::updateOrCreate(
            ['user_id' => $user->id],
            [
                'token' => $refreshToken,
                'expires_at' => now()->addDays(7),
            ]
        );

        return $refreshToken;
    }

    /**
     * Rotate an existing refresh token: validate claim, authenticate user, and issue new tokens.
     *
     * @param string $refreshToken
     * @return array
     * @throws TokenInvalidException
     */
    public function rotateRefreshToken(string $refreshToken): array
    {
        $payload = JWTAuth::setToken($refreshToken)->getPayload();

        if (!$payload->get('refresh', false)) {
            throw new TokenInvalidException('Invalid refresh token.');
        }

        /** @var User $user */
        $user = JWTAuth::authenticate($refreshToken);

        $newAccessToken = JWTAuth::fromUser($user);
        $newRefreshToken = $this->generateRefreshToken($user);

        return [
            'user' => $user,
            'token' => $newAccessToken,
            'refresh_token' => $newRefreshToken,
        ];
    }

    /**
     * Invalidate and delete all refresh tokens for a user.
     *
     * @param User $user
     * @return void
     */
    public function invalidateUserTokens(User $user): void
    {
        RefreshToken::where('user_id', $user->id)->delete();
    }
}
