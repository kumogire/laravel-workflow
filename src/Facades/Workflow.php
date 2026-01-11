<?php

namespace Kumogire\Workflow\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static \Kumogire\Workflow\Models\WorkflowInstance startWorkflow(\Kumogire\Workflow\Models\Workflow $workflow, $user, array $metadata = [])
 * @method static \Kumogire\Workflow\Models\WorkflowInstance completeStep(\Kumogire\Workflow\Models\WorkflowInstance $instance, $user, array $data = [])
 * @method static void pauseWorkflow(\Kumogire\Workflow\Models\WorkflowInstance $instance)
 * @method static void resumeWorkflow(\Kumogire\Workflow\Models\WorkflowInstance $instance)
 * @method static void abandonWorkflow(\Kumogire\Workflow\Models\WorkflowInstance $instance)
 * @method static array getCurrentStepDetails(\Kumogire\Workflow\Models\WorkflowInstance $instance, $user)
 *
 * @see \Kumogire\Workflow\Services\WorkflowService
 */
class Workflow extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \Kumogire\Workflow\Services\WorkflowService::class;
    }
}
