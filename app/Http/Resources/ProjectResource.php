<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Services\Timezone\TimezoneService;
use Illuminate\Support\Facades\Auth;

class ProjectResource extends JsonResource
{
    public function toArray(Request $request)
    {
        $timeZoneService = new TimezoneService();

        return [
            'id'             => $this->id,
            'desc'           => $this->desc,
            'city'           => $this->city,
            'created_at'     => $this->created_at ? $timeZoneService->convertTimeFormat($this->created_at,'UTC',Auth::user()->timezone,'Y-m-d H:i:s')->format('Y-m-d H:i:s') : null,
            'updated_at'     => $this->updated_at ? $timeZoneService->convertTimeFormat($this->updated_at,'UTC',Auth::user()->timezone,'Y-m-d H:i:s')->format('Y-m-d H:i:s') : null,
            'views'          => $this->views ?? 0,
            'is_edited'     => $this->is_edited ?? 0,
            'is_liked'      => $this->is_liked ?? false,
            'img'           => $this->img,
            'video'         => $this->video,
            'likes'          => $this->likes()->count(),
            'total_comments' => $this->comments()->count(),
            'user'    => $this->whenLoaded('user'),
            'service' => $this->whenLoaded('service'),
            'section' => $this->whenLoaded('section'),
        ];
    }
}
