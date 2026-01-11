<?php

namespace Kumogire\Workflow\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class WorkflowStepResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'workflow_id' => $this->workflow_id,
            'order' => $this->order,
            'title' => $this->title,
            'description' => $this->description,
            'type' => $this->type,
            'configuration' => $this->configuration,
            'condition_type' => $this->condition_type,
            'condition_config' => $this->condition_config,
            'skip_if_condition_false' => $this->skip_if_condition_false,
            'can_view_roles' => $this->can_view_roles,
            'can_complete_roles' => $this->can_complete_roles,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
