<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MomentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'type' => $this->moment_type->value,
            'title' => $this->title,
            'narrative' => $this->narrative,
            'caption' => $this->caption,
            'status' => $this->status->value,
            'visual' => $this->visualAsset ? [
                'id' => $this->visualAsset->id,
                'url' => $this->visualAsset->cdn_url ?? $this->visualAsset->file_path,
                'dimensions' => $this->visualAsset->dimensions,
                'status' => $this->visualAsset->approval_status->value,
                'is_master_reference' => $this->visualAsset->master_reference_version !== null,
            ] : null,
            'activity' => $this->activityInstance ? [
                'id' => $this->activityInstance->id,
                'name' => $this->activityInstance->name,
                'category' => $this->activityInstance->category->value,
                'location' => $this->activityInstance->location?->name,
            ] : null,
            'created_at' => $this->created_at->toIso8601String(),
        ];
    }
}
