<?php

namespace Kumogire\Workflow\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class WorkflowInstanceResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'workflow_id' => $this->workflow_id,
            'user_id' => $this->user_id,
            'status' => $this->status,
            'current_step_id' => $this->current_step_id,
            'started_at' => $this->started_at,
            'completed_at' => $this->completed_at,
            'metadata' => $this->metadata,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            
            // Relationships
            'workflow' => new WorkflowResource($this->whenLoaded('workflow')),
            'current_step' => new WorkflowStepResource($this->whenLoaded('currentStep')),
            'user' => $this->when($this->relationLoaded('user'), function () {
                return [
                    'id' => $this->user->id,
                    'name' => $this->user->name,
                    'email' => $this->user->email,
                ];
            }),
        ];
    }
}
