<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EventResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'start_time' => $this->start_time,
            'end_time' => $this->end_time,
            'location' => $this->location,
            'max_participants' => $this->max_participants,
            'status' => $this->status,
            'creator' => new UserResource($this->whenLoaded('creator')),
            'participants_count' => $this->whenLoaded('participants', function () {
                return $this->participants->count();
            }),
            'user_status' => $this->when(auth()->check(), function () {
                $participant = $this->participants
                    ->where('id', auth()->id())
                    ->first();
                return $participant ? $participant->pivot->status : null;
            }),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}