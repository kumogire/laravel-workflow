<?php

namespace Kumogire\Workflow\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class WorkflowInstance extends Model
{
    protected $fillable = [
        'workflow_id',
        'user_id',
        'current_step_id',
        'status',
        'started_at',
        'completed_at',
        'metadata',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
        'metadata' => 'array',
    ];

    /**
     * Get the workflow this instance belongs to
     */
    public function workflow(): BelongsTo
    {
        return $this->belongsTo(Workflow::class);
    }

    /**
     * Get the user this instance belongs to
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(config('workflow.user_model', 'App\Models\User'));
    }

    /**
     * Get the current step
     */
    public function currentStep(): BelongsTo
    {
        return $this->belongsTo(WorkflowStep::class, 'current_step_id');
    }

    /**
     * Get all instance steps
     */
    public function instanceSteps(): HasMany
    {
        return $this->hasMany(WorkflowInstanceStep::class);
    }

    /**
     * Scope to filter by status
     */
    public function scopeStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope to get pending instances
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope to get in-progress instances
     */
    public function scopeInProgress($query)
    {
        return $query->where('status', 'in_progress');
    }

    /**
     * Scope to get completed instances
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    /**
     * Check if instance is pending
     */
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    /**
     * Check if instance is in progress
     */
    public function isInProgress(): bool
    {
        return $this->status === 'in_progress';
    }

    /**
     * Check if instance is completed
     */
    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    /**
     * Check if instance is paused
     */
    public function isPaused(): bool
    {
        return $this->status === 'paused';
    }

    /**
     * Check if instance is abandoned
     */
    public function isAbandoned(): bool
    {
        return $this->status === 'abandoned';
    }

    /**
     * Get the instance step for a specific workflow step
     */
    public function getInstanceStep(WorkflowStep $step): ?WorkflowInstanceStep
    {
        return $this->instanceSteps()
            ->where('workflow_step_id', $step->id)
            ->first();
    }

    /**
     * Get or create instance step for a workflow step
     */
    public function getOrCreateInstanceStep(WorkflowStep $step): WorkflowInstanceStep
    {
        return $this->instanceSteps()->firstOrCreate(
            ['workflow_step_id' => $step->id],
            ['status' => 'not_started']
        );
    }
}
