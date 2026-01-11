<?php

namespace Kumogire\Workflow\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class WorkflowResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'type' => $this->type,
            'is_active' => $this->is_active,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'steps' => WorkflowStepResource::collection($this->whenLoaded('steps')),
            'steps_count' => $this->when(isset($this->steps_count), $this->steps_count),
        ];
    }
}
