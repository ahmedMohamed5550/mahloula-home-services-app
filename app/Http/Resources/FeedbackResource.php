<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Services\Timezone\TimezoneService;
use Illuminate\Support\Facades\Auth;

class FeedbackResource extends JsonResource
{
    public function toArray($request)
    {
        $timeZoneService = new TimezoneService();
        return [
            'id' => $this->id,
            'comment' => $this->comment,
            'rating' => $this->rating,
            'user' => $this->whenLoaded('user', function() {
                return [
                    'id' => $this->user->id,
                    'name' => $this->user->name,
                    'timezone' => $this->user->timezone ?? null,
                    'image'=>$this->user->image ?? null,
                ];
            }),
            'employee' => $this->whenLoaded('employee', function() {
                return [
                    'id' => $this->employee->id,
                    'name' => $this->employee->name,
                ];
            }),
            'created_at' => $this->created_at ? $timeZoneService->convertTimeFormat($this->created_at,'UTC',Auth::user()->timezone,'Y-m-d H:i:s')->format('Y-m-d H:i:s') : null,
            'updated_at' => $this->updated_at ? $timeZoneService->convertTimeFormat($this->updated_at,'UTC',Auth::user()->timezone,'Y-m-d H:i:s')->format('Y-m-d H:i:s') : null,
        ];
    }
}

