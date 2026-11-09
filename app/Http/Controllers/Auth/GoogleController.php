<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Resources\LoginResource;
use App\Services\Auth\AuthService;
use App\Traits\ApiResponseTrait;
use Exception;
use Laravel\Socialite\Facades\Socialite;

class GoogleController extends Controller
{
    use ApiResponseTrait;

    public function __construct(
        protected AuthService $authService
    ) {}

    /**
     * Redirect to Google for authentication.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    /**
     * Handle Google callback.
     *
     * @return \Illuminate\Http\Response
     */
    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->stateless()->user();

            $result = $this->authService->handleGoogleUser($googleUser);

            return $this->apiResponse(
                'User logged in successfully with Google',
                200,
                new LoginResource($result['user'], $result['token'])
            );
        } catch (Exception $e) {
            return $this->apiResponse('There was an error logging in with Google.', 500);
        }
    }
}
