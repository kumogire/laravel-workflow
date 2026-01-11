<?php

namespace Kumogire\Workflow\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class WorkflowInstanceStepResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'workflow_instance_id' => $this->workflow_instance_id,
            'workflow_step_id' => $this->workflow_step_id,
            'status' => $this->status,
            'started_at' => $this->started_at,
            'completed_at' => $this->completed_at,
            'completed_by' => $this->completed_by,
            'data' => $this->data,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            
            // Relationships
            'workflow_step' => new WorkflowStepResource($this->whenLoaded('workflowStep')),
        ];
    }
}
