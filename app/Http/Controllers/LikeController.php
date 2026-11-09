<?php

namespace App\Http\Controllers;

use App\Models\Like;
use Illuminate\Http\Request;
use App\Traits\ApiResponseTrait;
use App\Http\Requests\LikeRequest;
use App\Http\Resources\LikeResource;
use App\Http\Resources\ShowLikeResource;

class LikeController extends Controller
{
    use ApiResponseTrait;

    /**
     * @OA\Post(
     *     path="/api/like/create",
     *     summary="Add or delete a like for an employee",
     *     tags={"like"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 @OA\Property(
     *                     property="user_id",
     *                     type="integer",
     *                     description="User ID where type is user"
     *                 ),
     *                 @OA\Property(
     *                     property="employee_id",
     *                     type="integer",
     *                     description="User ID where type is employee"
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response="201",
     *         description="Like added or deleted successfully",
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 type="object",
     *                 @OA\Property(
     *                     property="success",
     *                     type="boolean",
     *                     description="Indicates if the operation was successful",
     *                     example=true
     *                 ),
     *                 @OA\Property(
     *                     property="message",
     *                     type="string",
     *                     description="Response message",
     *                     example="Like added successfully"
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response="401",
     *         description="Validation errors",
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 type="object",
     *                 @OA\Property(
     *                     property="success",
     *                     type="boolean",
     *                     description="Indicates if the operation was successful",
     *                     example=false
     *                 ),
     *                 @OA\Property(
     *                     property="errors",
     *                     type="object",
     *                     description="Validation errors details",
     *                     @OA\AdditionalProperties(
     *                         type="array",
     *                         @OA\Items(type="string")
     *                     ),
     *                     example={
     *                         "user_id": {"The user_id field is required."},
     *                         "employee_id": {"The employee_id must be an integer."}
     *                     }
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response="500",
     *         description="Internal server error",
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 type="object",
     *                 @OA\Property(
     *                     property="success",
     *                     type="boolean",
     *                     description="Indicates if the operation was successful",
     *                     example=false
     *                 ),
     *                 @OA\Property(
     *                     property="message",
     *                     type="string",
     *                     description="Error message",
     *                     example="An unexpected error occurred."
     *                 )
     *             )
     *         )
     *     )
     * )
     */


    public function store(LikeRequest $request)
    {
        $validatedData = $request->validated();
        $user_id = $request->user_id;
        $employee_id = $request->employee_id;
        $like = Like::where('user_id' , $user_id)->where('employee_id' , $employee_id)->get();

        if(count($like) > 0)
        {
            Like::where('user_id' , $user_id)->where('employee_id' , $employee_id)->delete();
            return $this->apiResponse('like deleted successfully', 200, $like);
        }

        $like = Like::create($validatedData);
        $like->load('user','employee');

        if ($like) {
            return $this->apiResponse('Like Created successfully', 200, new LikeResource($like));
        } else {
            return $this->apiResponse('Something went wrong', 500);
        }
    }

     /**
     * @OA\Get(
     *     path="/api/like/show/{id}",
     *     summary="Show Likes for employee",
     *     description="Show like for employee by user id where type employee",
     *     tags={"like"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the user where user type employee to show likes",
     *         required=true,
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Show employee likes successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="boolean"),
     *             @OA\Property(property="message", type="string"),
     *             @OA\Property(property="data", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="No Likes For Employee",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="boolean"),
     *             @OA\Property(property="message", type="string")
     *         )
     *     )
     * )
     */

     public function showLikes($id)
     {
        $likes = Like::with('user')->where('employee_id' , $id)->get();

        if(count($likes) > 0)
        {
            return $this->apiResponse('All Likes' , 200 , ShowLikeResource::collection($likes));
        }
        return $this->apiResponse('no likes for employee' , 404);

     }


}
