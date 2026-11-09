<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Resources\LoginResource;
use App\Models\User;
use App\Traits\ApiResponseTrait;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

class GoogleController extends Controller
{
    use ApiResponseTrait;
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
     * @return \Illuminate\Http\RedirectResponse
     */
    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->stateless()->user();

            // Check if the user already exists in the database
            $user = User::where('provider_id', $googleUser->id)->where('provider', 'google')->first();

            // If the user does not exist, create a new user
            if (!$user) {
                $user = User::create([
                    'name' => $googleUser->name,
                    'email' => $googleUser->email,
                    'provider' => 'google',
                    'provider_id' => $googleUser->id,
                    'image' => $googleUser->avatar,
                ]);
            }

            $token = Auth::login($user);
            return $this->apiResponse('User logged in successfully with Google',200,new LoginResource($user, $token));
        }
        catch (\Exception $e) {
            return $this->apiResponse('There was an error logging in with Google.', 500);
        }
    }

}

