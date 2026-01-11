<?php

namespace Kumogire\Workflow\Listeners;

use Kumogire\Workflow\Events\StepStarted;
use Kumogire\Workflow\Events\StepCompleted;
use Kumogire\Workflow\Actions\ActionHandlerFactory;
use Kumogire\Workflow\Models\WorkflowAction;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class ExecuteWorkflowActions implements ShouldQueue
{
    use InteractsWithQueue;

    protected ActionHandlerFactory $actionFactory;

    public function __construct(ActionHandlerFactory $actionFactory)
    {
        $this->actionFactory = $actionFactory;
    }

    /**
     * Get the name of the listener's queue connection
     */
    public function viaConnection()
    {
        return config('workflow.queue_connection', 'default');
    }

    /**
     * Determine whether the listener should be queued
     */
    public function shouldQueue($event)
    {
        return config('workflow.queue_actions', true);
    }

    /**
     * Handle step started events
     */
    public function handleStepStarted(StepStarted $event): void
    {
        $instance = $event->instance;
        $instanceStep = $event->instanceStep;
        $workflowStep = $instanceStep->workflowStep;

        // Get all actions that trigger on step start
        $actions = WorkflowAction::where('workflow_step_id', $workflowStep->id)
            ->where('trigger', 'on_step_start')
            ->get();

        $this->executeActions($actions, $instance);
    }

    /**
     * Handle step completed events
     */
    public function handleStepCompleted(StepCompleted $event): void
    {
        $instance = $event->instance;
        $instanceStep = $event->instanceStep;
        $workflowStep = $instanceStep->workflowStep;

        // Get all actions that trigger on step complete
        $actions = WorkflowAction::where('workflow_step_id', $workflowStep->id)
            ->where('trigger', 'on_step_complete')
            ->get();

        $this->executeActions($actions, $instance);
    }

    /**
     * Execute all actions
     */
    protected function executeActions($actions, $instance): void
    {
        foreach ($actions as $action) {
            try {
                $handler = $this->actionFactory->make($action->type);
                $handler->handle($action, $instance);
            } catch (\Exception $e) {
                \Log::error("Failed to execute workflow action", [
                    'action_id' => $action->id,
                    'type' => $action->type,
                    'instance_id' => $instance->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }
    }

    /**
     * Handle failed job
     */
    public function failed($event, $exception)
    {
        \Log::error("Workflow action listener failed", [
            'event' => get_class($event),
            'error' => $exception->getMessage(),
        ]);
    }
}
