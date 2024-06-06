<?php

namespace App\Http\Resources;

use Faker\Provider\Lorem;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Carbon;

class ChatRoomResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'is_private' => $this->is_private,
            'last_message' => Lorem::sentence(),
            'unread_count' => random_int(0, 10),
            'updated_at' => Carbon::now()->subDays(rand(0, 90))->format('Y-m-d H:i:s'),
            'role'=>$this->pivot->role??null
        ];
    }
}
