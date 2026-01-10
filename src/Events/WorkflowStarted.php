<?php

namespace Kumogire\Workflow\Events;

use Kumogire\Workflow\Models\WorkflowInstance;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class WorkflowStarted
{
    use Dispatchable, SerializesModels;

    public WorkflowInstance $instance;

    public function __construct(WorkflowInstance $instance)
    {
        $this->instance = $instance;
    }
}
