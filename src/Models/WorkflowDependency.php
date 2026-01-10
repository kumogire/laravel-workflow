<?php

namespace Kumogire\Workflow\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WorkflowDependency extends Model
{
    protected $fillable = [
        'workflow_id',
        'depends_on_workflow_id',
    ];

    /**
     * Get the workflow that has the dependency
     */
    public function workflow(): BelongsTo
    {
        return $this->belongsTo(Workflow::class);
    }

    /**
     * Get the workflow that is depended upon
     */
    public function dependsOnWorkflow(): BelongsTo
    {
        return $this->belongsTo(Workflow::class, 'depends_on_workflow_id');
    }
}
