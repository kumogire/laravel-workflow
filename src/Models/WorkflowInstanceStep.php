<?php

namespace Kumogire\Workflow\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WorkflowInstanceStep extends Model
{
    protected $fillable = [
        'workflow_instance_id',
        'workflow_step_id',
        'status',
        'started_at',
        'completed_at',
        'completed_by',
        'data',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
        'data' => 'array',
    ];

    /**
     * Get the workflow instance
     */
    public function workflowInstance(): BelongsTo
    {
        return $this->belongsTo(WorkflowInstance::class);
    }

    /**
     * Get the workflow step
     */
    public function workflowStep(): BelongsTo
    {
        return $this->belongsTo(WorkflowStep::class);
    }

    /**
     * Get the user who completed this step
     */
    public function completedBy(): BelongsTo
    {
        return $this->belongsTo(config('workflow.user_model', 'App\Models\User'), 'completed_by');
    }

    /**
     * Check if step is not started
     */
    public function isNotStarted(): bool
    {
        return $this->status === 'not_started';
    }

    /**
     * Check if step is in progress
     */
    public function isInProgress(): bool
    {
        return $this->status === 'in_progress';
    }

    /**
     * Check if step is completed
     */
    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    /**
     * Check if step is skipped
     */
    public function isSkipped(): bool
    {
        return $this->status === 'skipped';
    }
}
