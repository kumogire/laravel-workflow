<?php

namespace Kumogire\Workflow\Tests\Unit\Services;

use Kumogire\Workflow\Tests\TestCase;
use Kumogire\Workflow\Models\Workflow;
use Kumogire\Workflow\Models\WorkflowStep;
use Kumogire\Workflow\Services\WorkflowService;
use Kumogire\Workflow\Services\WorkflowStateMachine;
use Kumogire\Workflow\Exceptions\WorkflowException;
use Kumogire\Workflow\Exceptions\PermissionDeniedException;
use Illuminate\Support\Facades\Event;

class WorkflowServiceTest extends TestCase
{
    protected WorkflowService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new WorkflowService(new WorkflowStateMachine());
    }

    /** @test */
    public function it_can_start_a_workflow()
    {
        Event::fake();

        $workflow = Workflow::create([
            'name' => 'Test Workflow',
            'type' => 'test',
            'is_active' => true,
        ]);

        $step = WorkflowStep::create([
            'workflow_id' => $workflow->id,
            'order' => 1,
            'title' => 'First Step',
            'type' => 'task',
        ]);

        $user = $this->createUser();

        $instance = $this->service->startWorkflow($workflow, $user);

        $this->assertDatabaseHas('workflow_instances', [
            'workflow_id' => $workflow->id,
            'user_id' => $user->id,
            'status' => 'in_progress',
        ]);

        $this->assertEquals($step->id, $instance->current_step_id);

        Event::assertDispatched(\Kumogire\Workflow\Events\WorkflowStarted::class);
        Event::assertDispatched(\Kumogire\Workflow\Events\StepStarted::class);
    }

    /** @test */
    public function it_throws_exception_when_workflow_is_inactive()
    {
        $this->expectException(WorkflowException::class);

        $workflow = Workflow::create([
            'name' => 'Inactive Workflow',
            'type' => 'test',
            'is_active' => false,
        ]);

        $user = $this->createUser();

        $this->service->startWorkflow($workflow, $user);
    }

    /** @test */
    public function it_throws_exception_when_workflow_has_no_steps()
    {
        $this->expectException(WorkflowException::class);

        $workflow = Workflow::create([
            'name' => 'Empty Workflow',
            'type' => 'test',
            'is_active' => true,
        ]);

        $user = $this->createUser();

        $this->service->startWorkflow($workflow, $user);
    }

    /** @test */
    public function it_can_complete_a_step_and_advance()
    {
        Event::fake();

        $workflow = Workflow::create([
            'name' => 'Test',
            'type' => 'test',
            'is_active' => true,
        ]);

        $step1 = WorkflowStep::create([
            'workflow_id' => $workflow->id,
            'order' => 1,
            'title' => 'Step 1',
            'type' => 'task',
        ]);

        $step2 = WorkflowStep::create([
            'workflow_id' => $workflow->id,
            'order' => 2,
            'title' => 'Step 2',
            'type' => 'task',
        ]);

        $user = $this->createUser();
        $instance = $this->service->startWorkflow($workflow, $user);

        $instance = $this->service->completeStep($instance, $user, ['answer' => 'yes']);

        $this->assertEquals($step2->id, $instance->current_step_id);

        Event::assertDispatched(\Kumogire\Workflow\Events\StepCompleted::class);
    }

    /** @test */
    public function it_completes_workflow_when_last_step_is_done()
    {
        Event::fake();

        $workflow = Workflow::create([
            'name' => 'Test',
            'type' => 'test',
            'is_active' => true,
        ]);

        $step = WorkflowStep::create([
            'workflow_id' => $workflow->id,
            'order' => 1,
            'title' => 'Only Step',
            'type' => 'task',
        ]);

        $user = $this->createUser();
        $instance = $this->service->startWorkflow($workflow, $user);

        $instance = $this->service->completeStep($instance, $user);

        $this->assertEquals('completed', $instance->status);
        $this->assertNotNull($instance->completed_at);

        Event::assertDispatched(\Kumogire\Workflow\Events\WorkflowCompleted::class);
    }

    /** @test */
    public function it_throws_exception_when_user_lacks_permission_to_complete()
    {
        $this->expectException(PermissionDeniedException::class);

        $workflow = Workflow::create([
            'name' => 'Test',
            'type' => 'test',
            'is_active' => true,
        ]);

        $step = WorkflowStep::create([
            'workflow_id' => $workflow->id,
            'order' => 1,
            'title' => 'Manager Only',
            'type' => 'task',
            'can_complete_roles' => ['manager'],
        ]);

        $user = $this->createUser(['role' => 'employee']);
        $instance = $this->service->startWorkflow($workflow, $user);

        $this->service->completeStep($instance, $user);
    }

    /** @test */
    public function it_skips_steps_based_on_conditions()
    {
        $workflow = Workflow::create([
            'name' => 'Test',
            'type' => 'test',
            'is_active' => true,
        ]);

        $step1 = WorkflowStep::create([
            'workflow_id' => $workflow->id,
            'order' => 1,
            'title' => 'Step 1',
            'type' => 'task',
        ]);

        $step2 = WorkflowStep::create([
            'workflow_id' => $workflow->id,
            'order' => 2,
            'title' => 'Conditional Step',
            'type' => 'task',
            'condition_type' => 'if_data_equals',
            'condition_config' => [
                'field' => 'department',
                'value' => 'engineering',
            ],
            'skip_if_condition_false' => true,
        ]);

        $step3 = WorkflowStep::create([
            'workflow_id' => $workflow->id,
            'order' => 3,
            'title' => 'Step 3',
            'type' => 'task',
        ]);

        $user = $this->createUser();
        $instance = $this->service->startWorkflow($workflow, $user);

        // Complete step 1 without matching condition
        $instance = $this->service->completeStep($instance, $user, ['department' => 'sales']);

        // Should skip step 2 and go to step 3
        $this->assertEquals($step3->id, $instance->current_step_id);

        // Verify step 2 was marked as skipped
        $instanceStep2 = $instance->instanceSteps()
            ->where('workflow_step_id', $step2->id)
            ->first();

        $this->assertEquals('skipped', $instanceStep2->status);
    }

    /** @test */
    public function it_can_pause_and_resume_workflow()
    {
        Event::fake();

        $workflow = Workflow::create(['name' => 'Test', 'type' => 'test', 'is_active' => true]);
        $step = WorkflowStep::create([
            'workflow_id' => $workflow->id,
            'order' => 1,
            'title' => 'Step 1',
            'type' => 'task',
        ]);

        $user = $this->createUser();
        $instance = $this->service->startWorkflow($workflow, $user);

        $this->service->pauseWorkflow($instance);
        $this->assertEquals('paused', $instance->fresh()->status);
        Event::assertDispatched(\Kumogire\Workflow\Events\WorkflowPaused::class);

        $this->service->resumeWorkflow($instance);
        $this->assertEquals('in_progress', $instance->fresh()->status);
    }
}