<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use App\Models\Employee;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    use ApiResponseTrait;

    /**
     * @OA\Get(
     *     path="/api/contact/show/{user_id}",
     *     tags={"Contact"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="user_id",
     *         in="path",
     *         required=true,
     *         description="user id where type employee or company",
     *         @OA\Schema(
     *             type="integer",
     *         ),
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="Success",
     *     ),
     *
     * )
     */
    public function show($userId)
    {
        $employee = Employee::where('user_id', $userId)->exists();
        if (!$employee) {
            return $this->apiResponse('Service provider not found', 404);
        }

        $contact = Contact::where('user_id', $userId)
            ->latest('created_at')
            ->first();

        if (!$contact) {
            return $this->apiResponse('User does not have any contact', 200);
        }

        return $this->apiResponse('Contact retrieved successfully', 200, $contact);
    }

}
