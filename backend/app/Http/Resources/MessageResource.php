<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MessageResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'conversation_id' => $this->conversation_id,
            'sender_type' => $this->sender_type,
            'sender_id' => $this->sender_id,
            'message_type' => $this->message_type->value,
            'content' => $this->content,
            'moment' => $this->moment ? [
                'id' => $this->moment->id,
                'title' => $this->moment->title,
                'caption' => $this->moment->caption,
                'type' => $this->moment->moment_type->value,
                'visual_url' => $this->moment->visualAsset?->cdn_url ?? $this->moment->visualAsset?->file_path,
            ] : null,
            'is_read' => $this->is_read,
            'created_at' => $this->created_at->toIso8601String(),
        ];
    }
}
