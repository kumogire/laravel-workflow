<?php

namespace Kumogire\Workflow\Services;

use Kumogire\Workflow\Models\WorkflowInstance;
use Kumogire\Workflow\Models\WorkflowInstanceStep;
use Kumogire\Workflow\Exceptions\InvalidStateTransitionException;

class WorkflowStateMachine
{
    /**
     * Valid state transitions for workflow instances
     */
    protected array $validTransitions = [
        'pending' => ['in_progress', 'abandoned'],
        'in_progress' => ['paused', 'completed', 'abandoned'],
        'paused' => ['in_progress', 'abandoned'],
        'completed' => [],
        'abandoned' => [],
    ];

    /**
     * Valid state transitions for instance steps
     */
    protected array $validStepTransitions = [
        'not_started' => ['in_progress', 'skipped'],
        'in_progress' => ['completed', 'skipped'],
        'completed' => [],
        'skipped' => [],
    ];

    /**
     * Transition workflow instance to a new state
     */
    public function transitionInstance(WorkflowInstance $instance, string $newState): void
    {
        if (!$this->canTransitionInstance($instance->status, $newState)) {
            throw new InvalidStateTransitionException(
                "Cannot transition from {$instance->status} to {$newState}"
            );
        }

        $instance->status = $newState;

        // Set timestamps based on state
        switch ($newState) {
            case 'in_progress':
                if (!$instance->started_at) {
                    $instance->started_at = now();
                }
                break;
            case 'completed':
                $instance->completed_at = now();
                break;
        }

        $instance->save();
    }

    /**
     * Transition instance step to a new state
     */
    public function transitionStep(WorkflowInstanceStep $instanceStep, string $newState, $user = null): void
    {
        if (!$this->canTransitionStep($instanceStep->status, $newState)) {
            throw new InvalidStateTransitionException(
                "Cannot transition step from {$instanceStep->status} to {$newState}"
            );
        }

        $instanceStep->status = $newState;

        // Set timestamps based on state
        switch ($newState) {
            case 'in_progress':
                if (!$instanceStep->started_at) {
                    $instanceStep->started_at = now();
                }
                break;
            case 'completed':
                $instanceStep->completed_at = now();
                if ($user) {
                    $instanceStep->completed_by = $user->id;
                }
                break;
        }

        $instanceStep->save();
    }

    /**
     * Check if instance can transition to new state
     */
    public function canTransitionInstance(string $currentState, string $newState): bool
    {
        return isset($this->validTransitions[$currentState]) &&
               in_array($newState, $this->validTransitions[$currentState]);
    }

    /**
     * Check if step can transition to new state
     */
    public function canTransitionStep(string $currentState, string $newState): bool
    {
        return isset($this->validStepTransitions[$currentState]) &&
               in_array($newState, $this->validStepTransitions[$currentState]);
    }

    /**
     * Start a workflow instance
     */
    public function start(WorkflowInstance $instance): void
    {
        $this->transitionInstance($instance, 'in_progress');
    }

    /**
     * Pause a workflow instance
     */
    public function pause(WorkflowInstance $instance): void
    {
        $this->transitionInstance($instance, 'paused');
    }

    /**
     * Resume a paused workflow instance
     */
    public function resume(WorkflowInstance $instance): void
    {
        $this->transitionInstance($instance, 'in_progress');
    }

    /**
     * Complete a workflow instance
     */
    public function complete(WorkflowInstance $instance): void
    {
        $this->transitionInstance($instance, 'completed');
    }

    /**
     * Abandon a workflow instance
     */
    public function abandon(WorkflowInstance $instance): void
    {
        $this->transitionInstance($instance, 'abandoned');
    }
}
