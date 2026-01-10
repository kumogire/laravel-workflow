<?php

namespace Kumogire\Workflow\Events;

use Kumogire\Workflow\Models\WorkflowInstance;
use Kumogire\Workflow\Models\WorkflowInstanceStep;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class StepCompleted
{
    use Dispatchable, SerializesModels;

    public WorkflowInstance $instance;
    public WorkflowInstanceStep $instanceStep;

    public function __construct(WorkflowInstance $instance, WorkflowInstanceStep $instanceStep)
    {
        $this->instance = $instance;
        $this->instanceStep = $instanceStep;
    }
}
