<?php

namespace App\Http\Resources;

use App\Services\Timezone\TimezoneService;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;

class CommentResource extends JsonResource
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
            'comment_id'=>$this->id,
            'content'=>$this->content,
            'is_edited' => $this->is_edited ?? 0,
            'created_at' => $this->created_at ? $timeZoneService->convertTimeFormat($this->created_at,'UTC',Auth::user()->timezone,'Y-m-d H:i:s')->format('Y-m-d H:i:s') : null,
            'updated_at' => $this->updated_at ? $timeZoneService->convertTimeFormat($this->updated_at,'UTC',Auth::user()->timezone,'Y-m-d H:i:s')->format('Y-m-d H:i:s') : null,
            'user' => $this->whenLoaded('user', function() {
                return [
                    'id' => $this->user->id ?? null,
                    'name' => $this->user->name ?? null,
                    'image' => $this->user->image ?? null,
                    'company_image' => optional($this->user->employee)->company_image ?? null,
                    'company_name' => optional($this->user->employee)->company_name ?? null,
                ];
            }),
        ];
    }
}
