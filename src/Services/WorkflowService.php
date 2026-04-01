<?php

namespace Kumogire\Workflow\Services;

use Kumogire\Workflow\Models\Workflow;
use Kumogire\Workflow\Models\WorkflowInstance;
use Kumogire\Workflow\Models\WorkflowInstanceStep;
use Kumogire\Workflow\Models\WorkflowStep;
use Kumogire\Workflow\Events\WorkflowStarted;
use Kumogire\Workflow\Events\StepStarted;
use Kumogire\Workflow\Events\StepCompleted;
use Kumogire\Workflow\Events\WorkflowCompleted;
use Kumogire\Workflow\Events\WorkflowPaused;
use Kumogire\Workflow\Events\WorkflowAbandoned;
use Kumogire\Workflow\Exceptions\WorkflowException;
use Kumogire\Workflow\Exceptions\PermissionDeniedException;
use Illuminate\Support\Facades\DB;

class WorkflowService
{
    protected WorkflowStateMachine $stateMachine;

    public function __construct(WorkflowStateMachine $stateMachine)
    {
        $this->stateMachine = $stateMachine;
    }

    /**
     * Start a new workflow instance for a user
     */
    public function startWorkflow(Workflow $workflow, $user, array $metadata = []): WorkflowInstance
    {
        // Check if workflow is active
        if (!$workflow->is_active) {
            throw new WorkflowException("Workflow {$workflow->name} is not active");
        }

        // Check dependencies (soft requirement)
        $this->checkDependencies($workflow, $user);

        // Get first step
        $firstStep = $workflow->firstStep();
        if (!$firstStep) {
            throw new WorkflowException("Workflow has no steps");
        }

        return DB::transaction(function () use ($workflow, $user, $firstStep, $metadata) {
            // Create workflow instance
            $instance = WorkflowInstance::create([
                'workflow_id' => $workflow->id,
                'user_id' => $user->id,
                'current_step_id' => $firstStep->id,
                'status' => 'in_progress',
                'started_at' => now(),
                'metadata' => $metadata,
            ]);

            // Create instance step for first step
            $instanceStep = WorkflowInstanceStep::create([
                'workflow_instance_id' => $instance->id,
                'workflow_step_id' => $firstStep->id,
                'status' => 'in_progress',
                'started_at' => now(),
            ]);

            // Fire events
            event(new WorkflowStarted($instance));
            event(new StepStarted($instance, $instanceStep));

            return $instance->fresh();
        });
    }

    /**
     * Complete the current step and advance to next
     */
    public function completeStep(WorkflowInstance $instance, $user, array $data = []): WorkflowInstance
    {
        // Check if instance is in progress
        if (!$instance->isInProgress()) {
            throw new WorkflowException("Workflow instance is not in progress");
        }

        $currentStep = $instance->currentStep;
        if (!$currentStep) {
            throw new WorkflowException("No current step found");
        }

        // Check permissions
        if (!$currentStep->canComplete($user)) {
            throw new PermissionDeniedException("User does not have permission to complete this step");
        }

        return DB::transaction(function () use ($instance, $currentStep, $user, $data) {
            // Get or create instance step
            $instanceStep = $instance->getOrCreateInstanceStep($currentStep);

            // Save step data
            $instanceStep->data = $data;
            $instanceStep->save();

            // Mark step as completed
            $this->stateMachine->transitionStep($instanceStep, 'completed', $user);

            // Fire step completed event
            event(new StepCompleted($instance, $instanceStep));

            // Find next step
            $nextStep = $this->findNextStep($currentStep, $instance, $data);

            if ($nextStep) {
                // Move to next step
                $this->advanceToStep($instance, $nextStep);
            } else {
                // No more steps, complete workflow
                $this->completeWorkflow($instance);
            }

            return $instance->fresh();
        });
    }

    /**
     * Find the next step considering conditional logic
     */
    protected function findNextStep(WorkflowStep $currentStep, WorkflowInstance $instance, array $data): ?WorkflowStep
    {
        $nextStep = $currentStep->nextStep();

        // No more steps
        if (!$nextStep) {
            return null;
        }

        // Check if step should be skipped based on conditions
        if ($this->shouldSkipStep($nextStep, $instance, $data)) {
            // Mark as skipped
            $instanceStep = $instance->getOrCreateInstanceStep($nextStep);
            $this->stateMachine->transitionStep($instanceStep, 'skipped');

            // Recursively find next step
            return $this->findNextStep($nextStep, $instance, $data);
        }

        return $nextStep;
    }

    /**
     * Check if a step should be skipped based on conditions
     */
    protected function shouldSkipStep(WorkflowStep $step, WorkflowInstance $instance, array $data): bool
    {
        // Always execute if condition type is 'always'
        if ($step->condition_type === 'always') {
            return false;
        }

        // If skip_if_condition_false is false, never skip
        if (!$step->skip_if_condition_false) {
            return false;
        }

        // Evaluate condition
        return !$this->evaluateCondition($step, $instance, $data);
    }

    /**
     * Evaluate step condition
     */
    protected function evaluateCondition(WorkflowStep $step, WorkflowInstance $instance, array $data): bool
    {
        $config = $step->condition_config ?? [];

        switch ($step->condition_type) {
            case 'always':
                return true;

            case 'if_data_equals':
                $field = $config['field'] ?? null;
                $value = $config['value'] ?? null;
                return isset($data[$field]) && $data[$field] == $value;

            case 'if_data_contains':
                $field = $config['field'] ?? null;
                $value = $config['value'] ?? null;
                return isset($data[$field]) && 
                       is_array($data[$field]) && 
                       in_array($value, $data[$field]);

            case 'if_role':
                $requiredRole = $config['role'] ?? null;
                $roleField = config('workflow.role_field', 'role');
                $userRole = $instance->user->{$roleField};
                
                if (is_array($userRole)) {
                    return in_array($requiredRole, $userRole);
                }
                return $userRole === $requiredRole;

            default:
                return true;
        }
    }

    /**
     * Advance instance to a specific step
     */
    protected function advanceToStep(WorkflowInstance $instance, WorkflowStep $step): void
    {
        // Update current step
        $instance->current_step_id = $step->id;
        $instance->save();

        // Create or update instance step
        $instanceStep = $instance->getOrCreateInstanceStep($step);
        $this->stateMachine->transitionStep($instanceStep, 'in_progress');

        // Fire step started event
        event(new StepStarted($instance, $instanceStep));
    }

    /**
     * Complete the workflow
     */
    protected function completeWorkflow(WorkflowInstance $instance): void
    {
        $this->stateMachine->complete($instance);
        event(new WorkflowCompleted($instance));
    }

    /**
     * Pause a workflow instance
     */
    public function pauseWorkflow(WorkflowInstance $instance): void
    {
        $this->stateMachine->pause($instance);
        event(new WorkflowPaused($instance));
    }

    /**
     * Resume a paused workflow instance
     */
    public function resumeWorkflow(WorkflowInstance $instance): void
    {
        $this->stateMachine->resume($instance);
    }

    /**
     * Abandon a workflow instance
     */
    public function abandonWorkflow(WorkflowInstance $instance): void
    {
        $this->stateMachine->abandon($instance);
        event(new WorkflowAbandoned($instance));
    }

    /**
     * Check workflow dependencies (soft requirement)
     */
    protected function checkDependencies(Workflow $workflow, $user): void
    {
        $dependencies = $workflow->dependencies;

        foreach ($dependencies as $dependency) {
            $prerequisiteWorkflow = $dependency->dependsOnWorkflow;
            
            // Check if user has completed the prerequisite workflow
            $completed = WorkflowInstance::where('workflow_id', $prerequisiteWorkflow->id)
                ->where('user_id', $user->id)
                ->where('status', 'completed')
                ->exists();

            // Soft requirement - just log or notify, don't block
            if (!$completed) {
                \Log::info("User {$user->id} starting workflow {$workflow->id} without completing prerequisite {$prerequisiteWorkflow->id}");
            }
        }
    }

    /**
     * Get current step details for display
     */
    public function getCurrentStepDetails(WorkflowInstance $instance, $user): array
    {
        $currentStep = $instance->currentStep;
        
        if (!$currentStep) {
            return [
                'status' => $instance->status,
                'completed' => $instance->isCompleted(),
            ];
        }

        $instanceStep = $instance->getInstanceStep($currentStep);

        return [
            'instance_id' => $instance->id,
            'status' => $instance->status,
            'current_step' => [
                'id' => $currentStep->id,
                'title' => $currentStep->title,
                'description' => $currentStep->description,
                'type' => $currentStep->type,
                'configuration' => $currentStep->configuration,
                'can_view' => $currentStep->canView($user),
                'can_complete' => $currentStep->canComplete($user),
            ],
            'instance_step' => $instanceStep ? [
                'status' => $instanceStep->status,
                'started_at' => $instanceStep->started_at,
                'data' => $instanceStep->data,
            ] : null,
            'workflow' => [
                'id' => $instance->workflow->id,
                'name' => $instance->workflow->name,
                'type' => $instance->workflow->type,
            ],
        ];
    }
}
