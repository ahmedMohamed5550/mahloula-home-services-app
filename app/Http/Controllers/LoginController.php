<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Resources\LoginResource;
use App\Models\RefreshToken;
use App\Services\Auth\AuthService;
use App\Services\Auth\TokenService;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;

class LoginController extends Controller
{
    use ApiResponseTrait;

    public function __construct(
        protected AuthService $authService,
        protected TokenService $tokenService
    ) {}

    /**
     * @OA\Post(
     *     path="/api/login",
     *     summary="Authenticate user and generate token",
     *     description="Authenticates the user using their email or phone and password, and generates access and refresh tokens.",
     *     tags={"userAuth"},
     *     @OA\Parameter(
     *         name="email_or_phone",
     *         in="query",
     *         description="User's email or phone to login",
     *         required=true,
     *         @OA\Schema(
     *             type="string",
     *             example="ahmed@gmail.com"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="password",
     *         in="query",
     *         description="User's password",
     *         required=true,
     *         @OA\Schema(
     *             type="string",
     *             example="Am123456"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Login successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="User login successfully"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="user", type="object",
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="name", type="string", example="Ahmed Abd El-Baky"),
     *                     @OA\Property(property="email", type="string", example="ahmed@gmail.com"),
     *                     @OA\Property(property="phone", type="string", example="+201005763000")
     *                 ),
     *                 @OA\Property(property="token", type="string", example="accessTokenHere"),
     *                 @OA\Property(property="refresh_token", type="string", example="refreshTokenHere")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Invalid credentials",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="Unauthorized. Incorrect email or password."),
     *             @OA\Property(property="errors", type="object", nullable=true)
     *         )
     *     )
     * )
     */



    public function login(LoginRequest $request)
    {
        $loginData = $request->validated();

        $result = $this->authService->attemptLogin($loginData['email_or_phone'], $loginData['password']);

        if (!$result) {
            return $this->apiResponse('Unauthorized. Incorrect email or password.', 401);
        }

        return $this->apiResponse(
            'User login successfully',
            200,
            new LoginResource($result['user'], $result['token'], $result['refresh_token'])
        );
    }

    protected function generateRefreshToken($user)
    {
        return $this->tokenService->generateRefreshToken($user);
    }

    /**
     * @OA\Post(
     *     path="/api/refresh-token",
     *     summary="Refresh authentication token",
     *     description="Generates a new token for the authenticated user when the current token is expired or invalid.",
     *     tags={"userAuth"},
     *     @OA\RequestBody(
     *         required=true,
     *         content={
     *             @OA\MediaType(
     *                 mediaType="application/x-www-form-urlencoded",
     *                 @OA\Schema(
     *                     type="object",
     *                     @OA\Property(property="refresh_token", type="string", description="The refresh token")
     *                 )
     *             )
     *         }
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Token refreshed successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Token refreshed successfully"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="token", type="string", example="newAccessTokenHere"),
     *                 @OA\Property(property="refresh_token", type="string", example="newRefreshTokenHere"),
     *                 @OA\Property(property="user", type="object",
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="name", type="string", example="John Doe"),
     *                     @OA\Property(property="email", type="string", example="johndoe@example.com")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Token refresh failed",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="Invalid token. Please login again."),
     *             @OA\Property(property="errors", type="object", nullable=true)
     *         )
     *     )
     * )
     */

    public function refreshToken(Request $request)
    {
        $refreshToken = $request->input('refresh_token');

        if (!$refreshToken) {
            return $this->apiResponse('Refresh token is required.', 400);
        }

        try {
            $result = $this->tokenService->rotateRefreshToken($refreshToken);

            return $this->apiResponse(
                'Token refreshed successfully',
                200,
                new LoginResource($result['user'], $result['token'], $result['refresh_token'])
            );
        } catch (\Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {
            return $this->apiResponse('Invalid refresh token.', 401);
        } catch (\Tymon\JWTAuth\Exceptions\JWTException $e) {
            return $this->apiResponse('Failed to refresh token.', 500);
        }
    }

}
