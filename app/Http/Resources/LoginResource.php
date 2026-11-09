<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Tymon\JWTAuth\Facades\JWTAuth;

class LoginResource extends JsonResource
{
    protected $token;
    protected $refreshToken;

    public function __construct($resource, $token,$refreshToken)
    {
        parent::__construct($resource);
        $this->token = $token;
        $this->refreshToken = $refreshToken;
    }

    public function toArray($request)
    {
        $accessTokenTTL = config('jwt.ttl');
        $refreshTokenTTL = config('jwt.refresh_ttl');

        return [
            'user' => [
                'id' => $this->resource->id,
                'name' => $this->resource->name,
                'email' => $this->resource->email,
                'phone' => $this->resource->phone,
                'userType' => $this->resource->userType,
                'timezone' => $this->resource->timezone,
                'image' => $this->resource->image,
                'provider' => $this->resource->provider,
                'provider_id' => $this->resource->provider_id,
            ],
            'authorisation' => [
                'token' => $this->token,
                'refresh_token' => $this->refreshToken,
                'type' => 'bearer',
                'expires_in' => $accessTokenTTL,
                'refresh_expires_in' => $refreshTokenTTL,
            ],
        ];
    }
}


