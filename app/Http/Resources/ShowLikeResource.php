<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Services\Timezone\TimezoneService;
use Illuminate\Support\Facades\Auth;

class ShowLikeResource extends JsonResource
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
            'id'=>$this->id,
            'user' => $this->whenLoaded('user', function() {
                return [
                    'id' => $this->user->id,
                    'name' => $this->user->name,
                ];
            }),
            'created_at'=> $this->created_at ? $timeZoneService->convertTimeFormat($this->created_at,'UTC',Auth::user()->timezone,'Y-m-d H:i:s')->format('Y-m-d H:i:s') : null,
            'updated_at'=> $this->updated_at ? $timeZoneService->convertTimeFormat($this->updated_at,'UTC',Auth::user()->timezone,'Y-m-d H:i:s')->format('Y-m-d H:i:s') : null,
        ];
    }
}
