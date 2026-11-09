<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Services\Timezone\TimezoneService;
use Illuminate\Support\Facades\Auth;

class FavouriteResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $timeZoneService = new TimezoneService();

        return [
            'employee' => $this->whenLoaded('employee', function() {
                return [
                    'id' => $this->employee->user_id,
                    'name' => $this->employee->user->name,
                    'image' => $this->employee->user->image,
                    'timezone' => $this->employee->user->timezone,
                    'company_image'=>$this->employee->company_image ?? null,
                    'company_name'=>$this->employee->company_name ?? null,
                ];
            }),
            'created_at' => $this->created_at ? $timeZoneService->convertTimeFormat($this->created_at,'UTC',Auth::user()->timezone,'Y-m-d H:i:s')->format('Y-m-d H:i:s') : null,
            'updated_at' => $this->updated_at ? $timeZoneService->convertTimeFormat($this->updated_at,'UTC',Auth::user()->timezone,'Y-m-d H:i:s')->format('Y-m-d H:i:s') : null,
        ];
    }
}
