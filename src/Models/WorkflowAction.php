<?php

namespace Kumogire\Workflow\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WorkflowAction extends Model
{
    protected $fillable = [
        'workflow_step_id',
        'type',
        'trigger',
        'configuration',
    ];

    protected $casts = [
        'configuration' => 'array',
    ];

    /**
     * Get the workflow step this action belongs to
     */
    public function workflowStep(): BelongsTo
    {
        return $this->belongsTo(WorkflowStep::class);
    }

    /**
     * Check if action triggers on step start
     */
    public function triggersOnStart(): bool
    {
        return $this->trigger === 'on_step_start';
    }

    /**
     * Check if action triggers on step complete
     */
    public function triggersOnComplete(): bool
    {
        return $this->trigger === 'on_step_complete';
    }
}
