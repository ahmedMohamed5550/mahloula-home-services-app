<?php

namespace App\Http\Resources;

use App\Services\Timezone\TimezoneService;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;

class EmployeeProfileResource extends JsonResource
{
    protected $status;
    protected $save;

    public function __construct($resource, $status,$save)
    {
        parent::__construct($resource);
        $this->status = $status;
        $this->save = $save;
    }

    public function toArray(Request $request): array
    {
        $timeZoneService = new TimezoneService();

        return [
            "user" => [
                'id' => $this->user->id,
                'name' => $this->user->name,
                'email'=>$this->user->email,
                'image' => $this->user->image,
                'phone' => $this->user->phone,
                'userType' => $this->user->userType,
                'timezone' => $this->user->timezone,
            ],
            "employee_data" => [
                'id' => $this->id,
                'company_name'=>$this->company_name,
                'company_image'=>$this->company_image,
                'imageSSN' => $this->imageSSN,
                'livePhoto' => $this->livePhoto,
                'nationalId' => $this->nationalId,
                'description' => $this->description,
                'phone_number_2' => $this->phone_number_2,
                'mobile_number_1' => $this->mobile_number_1,
                'mobile_number_2' => $this->mobile_number_2,
                'fax_number' => $this->fax_number,
                'whatsapp_number' => $this->whatsapp_number,
                'facebook_link' => $this->facebook_link,
                'instagram_link'=>$this->instagram_link,
                'linked_in_link'=>$this->linked_in_link,
                'tiktok_link'=>$this->tiktok_link,
                'website' => $this->website,
                'total_rates' => $this->feedbacks->count(),
                'average_rating' => round($this->feedbacks->avg('rating'), 2),
                'likes' => $this->likes->count(),
                'total_saved' => $this->favourites->count(),
                'status' => $this->status,
                'saved' => $this->save,
                'checkByAdmin' => $this->checkByAdmin,
            ],
            'service' => [
                'id' => $this->service->id,
                'name' => $this->service->name,
            ],
            'section' => [
                'id' => $this->section->id,
                'name' => $this->section->name,
            ],
            'works' => $this->works->isNotEmpty() ? $this->works->map(function ($work) {
                return [
                    'id' => $work->id,
                    'image_url' => $work->image_url ?? null,
                    'video_url' => $work->video_url ?? null,
                ];
            }) : null,
            'location' => $this->user->locations->first() ? [
                'id' => $this->user->locations->first()->id,
                'city' => $this->user->locations->first()->city,
                'bitTitle' => $this->user->locations->first()->bitTitle,
                'street' => $this->user->locations->first()->street,
                'specialMarque' => $this->user->locations->first()->specialMarque,
                'zipCode' => $this->user->locations->first()->zipCode,
                'lat' => $this->user->locations->first()->lat,
                'long' => $this->user->locations->first()->long,
            ] : null,

            'feedbacks' => $this->feedbacks->map(function ($feedback) {
                $timeZoneService = new TimezoneService();

                return [
                    'id' => $feedback->id,
                    'rating' => $feedback->rating,
                    'name'=>$feedback->user->name,
                    'image'=>$feedback->user->image,
                    'comment' => $feedback->comment,
                    'created_at' => $feedback->created_at ? $timeZoneService->convertTimeFormat($feedback->created_at,'UTC',Auth::user()->timezone,'Y-m-d H:i:s')->format('Y-m-d H:i:s') : null,
                    'updated_at' => $feedback->updated_at ? $timeZoneService->convertTimeFormat($feedback->updated_at,'UTC',Auth::user()->timezone,'Y-m-d H:i:s')->format('Y-m-d H:i:s') : null,
                ];
            }),
        ];
    }
}
