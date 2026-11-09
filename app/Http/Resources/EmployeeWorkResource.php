<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Services\Timezone\TimezoneService;
use Illuminate\Support\Facades\Auth;

class EmployeeWorkResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $timeZoneService = new TimezoneService();

        return [
            'id' => $this->id,
            'employee_id' => $this->employee_id,
            'image_url' => $this->image_url,
            'video_url' => $this->video_url,
            'created_at' => $this->created_at ? $timeZoneService->convertTimeFormat($this->created_at,'UTC',Auth::user()->timezone,'Y-m-d H:i:s')->format('Y-m-d H:i:s') : null,
            'updated_at' => $this->updated_at ? $timeZoneService->convertTimeFormat($this->updated_at,'UTC',Auth::user()->timezone,'Y-m-d H:i:s')->format('Y-m-d H:i:s') : null,
        ];
    }
}
