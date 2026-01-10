<?php

namespace Kumogire\Workflow\Contracts;

use Kumogire\Workflow\Models\WorkflowAction;
use Kumogire\Workflow\Models\WorkflowInstance;

interface ActionHandler
{
    public function handle(WorkflowAction $action, WorkflowInstance $instance): void;
}