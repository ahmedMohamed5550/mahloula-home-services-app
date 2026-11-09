<?php

namespace App\Http\Controllers;

use App\Models\Sponser;
use Illuminate\Http\Request;
use App\Traits\ApiResponseTrait;

class SponserController extends Controller
{
    use ApiResponseTrait;

    /**
     * @OA\Get(
     *     path="/api/sponsers/index",
     *     summary="Show all sponsers",
     *     tags={"sponsers"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Success",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="sponsors", type="array", @OA\Items(type="object"))
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Unauthorized")
     *         )
     *     )
     * )
     */

    public function index()
    {
        $sponser = Sponser::all();
        if(count($sponser) > 0)
        {
            return $this->apiResponse('show all sponsers done' , 200 , $sponser);
        }
        return $this->apiResponse('no sponsers found to show it' , 200);
    }
}
