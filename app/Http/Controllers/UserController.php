<?php

namespace App\Http\Controllers;

use Throwable;
use App\Models\User;
use Illuminate\Http\Request;
use App\Traits\ApiResponseTrait;
use App\Http\Requests\ForgotPasswordRequest;
use App\Http\Requests\ResetPasswordRequest;
use App\Http\Requests\UserRequest;
use App\Http\Resources\UserResource;
use App\Services\Auth\AuthService;
use App\Services\Auth\TokenService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\EditUserProfileRequest;
use App\Http\Resources\UpdateUserProfileResource;
use App\Mail\UserRegister;
use App\Models\RefreshToken;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class UserController extends Controller
{
    use ApiResponseTrait;

    public function __construct(
        protected AuthService $authService,
        protected TokenService $tokenService
    ) {}

    /**
     * @OA\Post(
     *     path="/api/register",
     *     summary="Register a new user",
     *     tags={"userAuth"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 @OA\Property(property="name", type="string", description="User's name", example="Ahmed Mohamed"),
     *                 @OA\Property(property="email", type="string", description="User's email", example="Ahmed@gmail.com"),
     *                 @OA\Property(property="password", type="string", description="User's password", example="Am123456#"),
     *                 @OA\Property(property="phone", type="string", description="User's phone", example="12345678912"),
     *                 @OA\Property(property="timezone", type="string", description="User's timezone", example="UTC"),
     *                 @OA\Property(property="image", type="string", format="binary", description="User's image"),
     *                 @OA\Property(
     *                     property="userType",
     *                     type="string",
     *                     description="User type: ['user','employee','company','admin']",
     *                     example="user"
     *                 )
     *             )
     *         ),
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(property="name", type="string", description="User's name", example="Ahmed Mohamed"),
     *                 @OA\Property(property="email", type="string", description="User's email", example="Ahmed@gmail.com"),
     *                 @OA\Property(property="password", type="string", description="User's password", example="Am123456#"),
     *                 @OA\Property(property="phone", type="string", description="User's phone", example="12345678912"),
     *                 @OA\Property(property="timezone", type="string", description="User's timezone", example="UTC"),
     *                 @OA\Property(property="image", type="string", format="binary", description="User's image"),
     *                 @OA\Property(
     *                     property="userType",
     *                     type="string",
     *                     description="User type: ['user','employee','company','admin']",
     *                     example="user"
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response="201",
     *         description="User registered successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="User registered successfully"),
     *             @OA\Property(property="data", type="object", example={
     *                 "id": 1,
     *                 "name": "Ahmed Mohamed",
     *                 "email": "Ahmed@gmail.com",
     *                 "userType": "user"
     *             })
     *         )
     *     ),
     *     @OA\Response(
     *         response="422",
     *         description="Validation errors",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Validation failed. Please check the input fields.")
     *         )
     *     ),
     *     @OA\Response(
     *         response="400",
     *         description="Bad request - Invalid parameters",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Invalid input data.")
     *         )
     *     ),
     *     @OA\Response(
     *         response="500",
     *         description="Internal server error",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Something went wrong, please try again later.")
     *         )
     *     ),
     *     @OA\Response(
     *         response="404",
     *         description="Resource not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="The requested resource could not be found.")
     *         )
     *     )
     * )
     */


    public function register(UserRequest $request)
    {
        try {
            $user = $this->authService->register(
                $request->validated(),
                $request->file('image')
            );

            return $this->apiResponse('User registered successfully', 200, new UserResource($user));
        } catch (Throwable $e) {
            return $this->apiResponse('Something went wrong', 500, $e->getMessage());
        }
    }


    /**
     * @OA\Post(
     *     path="/api/user/editUserProfile/{user_id}",
     *     summary="Edit user profile",
     *     tags={"userAuth"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="user_id",
     *         in="path",
     *         required=true,
     *         description="ID of the user",
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 @OA\Property(
     *                     property="name",
     *                     type="string",
     *                     description="Name"
     *                 ),
     *                 @OA\Property(
     *                     property="timezone",
     *                     type="string",
     *                     description="User's timezone",
     *                     example="UTC"
     *                 ),
     *                 @OA\Property(
     *                     property="image",
     *                     type="string",
     *                     format="binary",
     *                     description="Image file"
     *                 ),
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response="201",
     *         description="Update profile successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Profile updated successfully"),
     *             @OA\Property(property="data", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response="400",
     *         description="Bad request",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="Invalid input data"),
     *             @OA\Property(property="errors", type="object", example={"name": {"The name field is required."}})
     *         )
     *     ),
     *     @OA\Response(
     *         response="401",
     *         description="Unauthorized",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="Unauthorized action")
     *         )
     *     ),
     *     @OA\Response(
     *         response="422",
     *         description="Validation errors",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="Validation error"),
     *             @OA\Property(property="errors", type="object")
     *         )
     *     )
     * )
     */

     public function editUserProfile(EditUserProfileRequest $request, $id)
     {
         try {
            $user = User::findOrFail($id);
            $imageUrl = $user->image;

            if ($request->hasFile('image')) {
                if ($imageUrl) {
                    $oldImagePath = public_path('users_folder/' . basename($imageUrl));

                    if (file_exists($oldImagePath)) {
                        unlink($oldImagePath);
                    }
                }

                $newImage = $request->file('image')->move(public_path('users_folder'), $request->file('image')->getClientOriginalName());
                $imageUrl = asset('users_folder/' . $request->file('image')->getClientOriginalName());
             }

            $user->update([
                'name' => $request->name,
                'timezone' => $request->timezone,
                'image' => $imageUrl,
            ]);

            return $this->apiResponse('User profile updated successfully', 200, new UpdateUserProfileResource($user));

        } catch (Throwable $e) {
            return $this->apiResponse('Something went wrong', 500,$e->getMessage());
        }
     }




    /**
     * @OA\Get(
     *     path="/api/user/{user_id}",
     *     summary="Show User profile",
     *     description="Show User profile",
     *     tags={"userAuth"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="user_id",
     *         in="path",
     *         description="ID of the user",
     *         required=true,
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Show User profile successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="boolean"),
     *             @OA\Property(property="message", type="string"),
     *             @OA\Property(property="data", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="User not found",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="boolean"),
     *             @OA\Property(property="message", type="string")
     *         )
     *     )
     * )
     */
    public function userProfile($user_id)
    {
        $user = User::findOrFail($user_id);
        if(!$user)
        {
            return $this->apiResponse('User Not found',404);
        }

        return $this->apiResponse('show user profile' , 200 , new UserResource($user));

    }

    /**
     * @OA\Get(
     *     path="/api/logout",
     *     summary="User logout",
     *     tags={"userAuth"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response="200",
     *         description="Successfully logged out",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Successfully logged out")
     *         )
     *     ),
     *     @OA\Response(
     *         response="401",
     *         description="Unauthorized",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="Unauthorized")
     *         )
     *     )
     * )
     */

    public function logout()
    {
         try {
            $user = Auth::user();
            if ($user) {
                $this->tokenService->invalidateUserTokens($user);
            }

            Auth::logout();
            return $this->apiResponse('Successfully logged out', 200);
         } catch (Throwable $e) {
            return $this->apiResponse('Something went wrong', 500, $e->getMessage());
        }
    }

    /**
     * @OA\Post(
     *     path="/api/forgot-password",
     *     summary="Request a password reset link",
     *     tags={"userAuth"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 required={"email"},
     *                 @OA\Property(
     *                     property="email",
     *                     type="string",
     *                     description="The email of the user requesting the reset link"
     *                 ),
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Reset link sent successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Send Reset Link to Mail Successfully")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Failed to send reset link",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="Failed Send Mail")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(
     *                 property="errors",
     *                 type="object",
     *                 additionalProperties=@OA\Property(type="array", @OA\Items(type="string"))
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="Something went wrong")
     *         )
     *     )
     * )
     */


    public function sendResetLinkEmail(ForgotPasswordRequest $request)
    {
         try {
            $status = $this->authService->sendResetLink($request->validated('email'));

            if ($status === Password::RESET_LINK_SENT) {
                return $this->apiResponse('Send Reset Link to Mail Successfully', 200, __($status));
            }

            return $this->apiResponse('Failed Send Mail', 400, __($status));
        } catch (Throwable $e) {
            return $this->apiResponse('Something went wrong', 500, $e->getMessage());
        }
    }


    /**
    * @OA\Post(
    *     path="/api/reset-password",
    *     summary="reset password",
    *     tags={"userAuth"},
    *     @OA\RequestBody(
    *         required=true,
    *         @OA\MediaType(
    *             mediaType="multipart/form-data",
    *             @OA\Schema(
    *                 required={"email", "token", "password", "password_confirmation"},
    *                 @OA\Property(
    *                     property="email",
    *                     type="string",
    *                     format="email",
    *                     description="The email of the user requesting the reset link or resetting the password"
    *                 ),
    *                 @OA\Property(
    *                     property="token",
    *                     type="string",
    *                     description="The token provided in the password reset email"
    *                 ),
    *                 @OA\Property(
    *                     property="password",
    *                     type="string",
    *                     format="password",
    *                     description="The new password for the user"
    *                 ),
    *                 @OA\Property(
    *                     property="password_confirmation",
    *                     type="string",
    *                     format="password",
    *                     description="The confirmation of the new password"
    *                 )
    *             )
    *         )
    *     ),
    *     @OA\Response(
    *         response=200,
    *         description="password reseted Successfully",
    *         @OA\JsonContent(
    *             type="object",
    *             @OA\Property(property="status", type="string", example="success"),
    *             @OA\Property(property="message", type="string", example="password reseted Successfully")
    *         )
    *     ),
    *     @OA\Response(
    *         response=400,
    *         description="Failed to reset password",
    *         @OA\JsonContent(
    *             type="object",
    *             @OA\Property(property="status", type="string", example="error"),
    *             @OA\Property(property="message", type="string", example="Failed Send Mail")
    *         )
    *     ),
    *     @OA\Response(
    *         response=422,
    *         description="Validation error",
    *         @OA\JsonContent(
    *             type="object",
    *             @OA\Property(property="status", type="string", example="error"),
    *             @OA\Property(
    *                 property="errors",
    *                 type="object",
    *                 additionalProperties=@OA\Property(type="array", @OA\Items(type="string"))
    *             )
    *         )
    *     ),
    *     @OA\Response(
    *         response=500,
    *         description="Internal server error",
    *         @OA\JsonContent(
    *             type="object",
    *             @OA\Property(property="status", type="string", example="error"),
    *             @OA\Property(property="message", type="string", example="Something went wrong")
    *         )
    *     )
    * )
    */

    public function resetPassword(ResetPasswordRequest $request)
    {
        try {
            $status = $this->authService->resetPassword(
                $request->only('email', 'password', 'password_confirmation', 'token')
            );

            if ($status === Password::PASSWORD_RESET) {
                return $this->apiResponse('Reset Password Successfully', 200, $status);
            }

            return $this->apiResponse('Failed to Reset Password', 400, __($status));
        } catch (Throwable $e) {
            return $this->apiResponse('Something went wrong', 500, $e->getMessage());
        }
    }


}
