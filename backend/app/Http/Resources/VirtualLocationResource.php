<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class VirtualLocationResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'code' => $this->code,
            'name' => $this->name,
            'category' => $this->category,
            'description' => $this->description,
            'environment_asset_path' => $this->environment_asset_path,
            'allowed_activities' => $this->allowed_activities,
            'visual_theme' => $this->visual_theme,
            'is_available' => $this->is_available,
        ];
    }
}
