<?php

namespace App\Http\Controllers;

use App\Http\Resources\EmployeeWorkResource;
use App\Models\EmployeeWork;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Throwable;
use Illuminate\Validation\Rule;

class EmployeeWorkController extends Controller
{
    use ApiResponseTrait;
    /**
     * @OA\Post(
     *     path="/api/work/create",
     *     summary="Add works to employee",
     *     tags={"Works"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=false,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 @OA\Property(
     *                     property="employee_id",
     *                     type="integer",
     *                     description="User ID where type is employee"
     *                 ),
     *                 @OA\Property(
     *                     property="images[0][image]",
     *                     type="string",
     *                     format="binary",
     *                     description="Image work 1",
     *                     nullable=true,
     *                 ),
     *                 @OA\Property(
     *                     property="images[1][image]",
     *                     type="string",
     *                     format="binary",
     *                     description="Image work 2",
     *                     nullable=true,
     *                 ),
     *                 @OA\Property(
     *                     property="images[2][image]",
     *                     type="string",
     *                     format="binary",
     *                     description="Image work 3",
     *                     nullable=true,
     *                 ),
     *                 @OA\Property(
     *                     property="images[3][image]",
     *                     type="string",
     *                     format="binary",
     *                     description="Image work 4",
     *                     nullable=true,
     *                 ),
     *                 @OA\Property(
     *                     property="images[4][image]",
     *                     type="string",
     *                     format="binary",
     *                     description="Image work 5",
     *                     nullable=true,
     *                 ),
     *                 @OA\Property(
     *                     property="videos[0][video]",
     *                     type="string",
     *                     format="binary",
     *                     description="Video work 1",
     *                     nullable=true,
     *                 ),
     *                 @OA\Property(
     *                     property="videos[1][video]",
     *                     type="string",
     *                     format="binary",
     *                     description="Video work 2",
     *                     nullable=true,
     *                 ),
     *                 @OA\Property(
     *                     property="videos[2][video]",
     *                     type="string",
     *                     format="binary",
     *                     description="Video work 3",
     *                     nullable=true,
     *                 ),
     *                 @OA\Property(
     *                     property="videos[3][video]",
     *                     type="string",
     *                     format="binary",
     *                     description="Video work 4",
     *                     nullable=true,
     *                 ),
     *                 @OA\Property(
     *                     property="videos[4][video]",
     *                     type="string",
     *                     format="binary",
     *                     description="Video work 5",
     *                     nullable=true,
     *                 ),
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Employee works added successfully",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="status",
     *                 type="boolean",
     *                 example=true
     *             ),
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="successfully"
     *             ),
     *             @OA\Property(
     *                 property="employee",
     *                 type="object"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="status",
     *                 type="boolean",
     *                 example=false
     *             ),
     *             @OA\Property(
     *                 property="message",
     *                 type="object"
     *             )
     *         )
     *     )
     * )
     */

    public function store(Request $request)
    {
        $validatedData = $this->validateRequest($request);

        if ($validatedData->fails()) {
            return response()->json(['status' => false, 'message' => $validatedData->errors()], 401);
        }

        DB::beginTransaction();
        $employee = EmployeeWork::where('employee_id',$request->employee_id)->first();
        if($employee){
            return $this->apiResponse('This Employee already add last works', 400);
        }

        try{
            $createdWorks = [];

            $images = $request->has('images') ? $request->images : [];
            $videos = $request->has('videos') ? $request->videos : [];

            // Ensure both images and videos arrays have exactly 5 entries, filling with null values if necessary
            $images = array_pad($images, 5, ['image' => null]);
            $videos = array_pad($videos, 5, ['video' => null]);

            for ($i = 0; $i < 5; $i++) {
                $workImageUrl = null;
                $workVideoUrl = null;

                if (isset($images[$i]['image']) && $images[$i]['image']) {
                    $workImage = $images[$i]['image'];
                    $workImagePath = $workImage->move(public_path('employee_works/images'), $workImage->getClientOriginalName());
                    $workImageUrl = asset('employee_works/images/' . $workImage->getClientOriginalName());
                }

                if (isset($videos[$i]['video']) && $videos[$i]['video']) {
                    $workVideo = $videos[$i]['video'];
                    $workVideoPath = $workVideo->move(public_path('employee_works/videos'), $workVideo->getClientOriginalName());
                    $workVideoUrl = asset('employee_works/videos/' . $workVideo->getClientOriginalName());
                }

                $data = EmployeeWork::create([
                    'employee_id' => $request->employee_id,
                    'image_url' => $workImageUrl,
                    'video_url' => $workVideoUrl,
                ]);

                $createdWorks[] = $data;
            }

            DB::commit();
            return $this->apiResponse('Employee images and videos added successfully', 200, EmployeeWorkResource::collection($createdWorks));
        }
        catch (\Exception $e) {
            DB::rollBack();
            return $this->apiResponse('something error', 500, $e->getMessage());
        }
    }

    /**
     * @OA\Post(
     *     path="/api/work/edit",
     *     summary="Update works for an employee",
     *     tags={"Works"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=false,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 @OA\Property(
     *                     property="employee_id",
     *                     type="integer",
     *                     description="User ID where type is employee"
     *                 ),
     *                 @OA\Property(
     *                     property="images[0][image]",
     *                     type="string",
     *                     format="binary",
     *                     description="Image work 1",
     *                     nullable=true,
     *                 ),
     *                 @OA\Property(
     *                     property="images[1][image]",
     *                     type="string",
     *                     format="binary",
     *                     description="Image work 2",
     *                     nullable=true,
     *                 ),
     *                 @OA\Property(
     *                     property="images[2][image]",
     *                     type="string",
     *                     format="binary",
     *                     description="Image work 3",
     *                     nullable=true,
     *                 ),
     *                 @OA\Property(
     *                     property="images[3][image]",
     *                     type="string",
     *                     format="binary",
     *                     description="Image work 4",
     *                     nullable=true,
     *                 ),
     *                 @OA\Property(
     *                     property="images[4][image]",
     *                     type="string",
     *                     format="binary",
     *                     description="Image work 5",
     *                     nullable=true,
     *                 ),
     *                 @OA\Property(
     *                     property="videos[0][video]",
     *                     type="string",
     *                     format="binary",
     *                     description="Video work 1",
     *                     nullable=true,
     *                 ),
     *                 @OA\Property(
     *                     property="videos[1][video]",
     *                     type="string",
     *                     format="binary",
     *                     description="Video work 2",
     *                     nullable=true,
     *                 ),
     *                 @OA\Property(
     *                     property="videos[2][video]",
     *                     type="string",
     *                     format="binary",
     *                     description="Video work 3",
     *                     nullable=true,
     *                 ),
     *                 @OA\Property(
     *                     property="videos[3][video]",
     *                     type="string",
     *                     format="binary",
     *                     description="Video work 4",
     *                     nullable=true,
     *                 ),
     *                 @OA\Property(
     *                     property="videos[4][video]",
     *                     type="string",
     *                     format="binary",
     *                     description="Video work 5",
     *                     nullable=true,
     *                 ),
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Employee works updated successfully",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="status",
     *                 type="boolean",
     *                 example=true
     *             ),
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="Update successful"
     *             ),
     *             @OA\Property(
     *                 property="employee",
     *                 type="object"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="status",
     *                 type="boolean",
     *                 example=false
     *             ),
     *             @OA\Property(
     *                 property="message",
     *                 type="object"
     *             )
     *         )
     *     )
     * )
     */
    public function update(Request $request)
    {
        $validatedData = $this->validateRequest($request);

        if ($validatedData->fails()) {
            return response()->json(['status' => false, 'message' => $validatedData->errors()], 401);
        }

        DB::beginTransaction();

        try {
            $images = $request->images ?? [];
            $videos = $request->videos ?? [];
            $this->processEmployeeWorks($request->employee_id, $images, $videos);

            DB::commit();

            $updatedWorks = EmployeeWork::where('employee_id', $request->employee_id)->get();
            return $this->apiResponse('Employee works updated successfully', 200, EmployeeWorkResource::collection($updatedWorks));
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->apiResponse('something error', 500, $e->getMessage());
        }
    }

    private function validateRequest($request)
    {
        return Validator::make($request->all(), [
            'employee_id' => [
                'required',
                Rule::exists('users', 'id')->where(function ($query) {
                    $query->whereIn('userType', ['employee', 'company']);
                }),
            ],
            'images' => 'nullable|array|max:5',
            'images.*.image' => 'nullable|image|mimes:jpeg,png,jpg,gif',
            'videos' => 'nullable|array|max:5',
            'videos.*.video' => 'nullable|mimes:mp4,mov,avi|max:10000',
        ]);
    }

    private function processEmployeeWorks($employeeId, $images, $videos)
    {
        $existingWorks = EmployeeWork::where('employee_id', $employeeId)->get();

        for ($i = 0; $i < 5; $i++) {
            $workImageUrl = $this->handleImageUpload($images[$i]['image'] ?? null, $existingWorks[$i]->image_url ?? null);
            $workVideoUrl = $this->handleVideoUpload($videos[$i]['video'] ?? null, $existingWorks[$i]->video_url ?? null);

            if (isset($existingWorks[$i])) {
                $existingWorks[$i]->update([
                    'image_url' => $workImageUrl,
                    'video_url' => $workVideoUrl,
                ]);
            } else {
                EmployeeWork::create([
                    'employee_id' => $employeeId,
                    'image_url' => $workImageUrl,
                    'video_url' => $workVideoUrl,
                ]);
            }
        }
    }

    private function handleImageUpload($newImage, $oldImageUrl)
    {
        if ($newImage) {
            $this->deleteOldFile($oldImageUrl);
            $imagePath = 'employee_works/images/' . $newImage->getClientOriginalName();
            $newImage->move(public_path('employee_works/images'), $newImage->getClientOriginalName());
            return asset($imagePath);
        }
        return $oldImageUrl;
    }

    private function handleVideoUpload($newVideo, $oldVideoUrl)
    {
        if ($newVideo) {
            $this->deleteOldFile($oldVideoUrl);
            $videoPath = 'employee_works/videos/' . $newVideo->getClientOriginalName();
            $newVideo->move(public_path('employee_works/videos'), $newVideo->getClientOriginalName());
            return asset($videoPath);
        }
        return $oldVideoUrl;
    }

    private function deleteOldFile($fileUrl)
    {
        if ($fileUrl) {
            // Extract the relative path from the URL
            $relativePath = parse_url($fileUrl, PHP_URL_PATH);
            $fullPath = public_path($relativePath);

            // Check if file exists before attempting to delete
            if (file_exists($fullPath)) {
                unlink($fullPath);
            }
        }
    }

}
