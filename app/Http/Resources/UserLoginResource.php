<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserLoginResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {

        parent::wrap(null);

        $token = $this->createToken($this->email);
        
        return [
            'token' => $token->plainTextToken,
            'user' => [
                'name' => $this->name,
                'email' => $this->email
            ]
        ];
    }
}
