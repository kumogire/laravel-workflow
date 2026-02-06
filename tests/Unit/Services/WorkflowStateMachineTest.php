<?php

namespace Kumogire\Workflow\Tests\Unit\Services;

use Kumogire\Workflow\Tests\TestCase;
use Kumogire\Workflow\Models\Workflow;
use Kumogire\Workflow\Models\WorkflowInstance;
use Kumogire\Workflow\Models\WorkflowStep;
use Kumogire\Workflow\Models\WorkflowInstanceStep;
use Kumogire\Workflow\Services\WorkflowStateMachine;
use Kumogire\Workflow\Exceptions\InvalidStateTransitionException;

class WorkflowStateMachineTest extends TestCase
{
    protected WorkflowStateMachine $stateMachine;

    protected function setUp(): void
    {
        parent::setUp();
        $this->stateMachine = new WorkflowStateMachine();
    }

    /** @test */
    public function it_can_transition_instance_from_pending_to_in_progress()
    {
        $workflow = Workflow::create(['name' => 'Test', 'type' => 'test']);
        $user = $this->createUser();

        $instance = WorkflowInstance::create([
            'workflow_id' => $workflow->id,
            'user_id' => $user->id,
            'status' => 'pending',
        ]);

        $this->stateMachine->transitionInstance($instance, 'in_progress');

        $this->assertEquals('in_progress', $instance->fresh()->status);
        $this->assertNotNull($instance->fresh()->started_at);
    }

    /** @test */
    public function it_can_transition_instance_from_in_progress_to_completed()
    {
        $workflow = Workflow::create(['name' => 'Test', 'type' => 'test']);
        $user = $this->createUser();

        $instance = WorkflowInstance::create([
            'workflow_id' => $workflow->id,
            'user_id' => $user->id,
            'status' => 'in_progress',
        ]);

        $this->stateMachine->transitionInstance($instance, 'completed');

        $this->assertEquals('completed', $instance->fresh()->status);
        $this->assertNotNull($instance->fresh()->completed_at);
    }

    /** @test */
    public function it_throws_exception_for_invalid_transition()
    {
        $this->expectException(InvalidStateTransitionException::class);

        $workflow = Workflow::create(['name' => 'Test', 'type' => 'test']);
        $user = $this->createUser();

        $instance = WorkflowInstance::create([
            'workflow_id' => $workflow->id,
            'user_id' => $user->id,
            'status' => 'completed',
        ]);

        $this->stateMachine->transitionInstance($instance, 'in_progress');
    }

    /** @test */
    public function it_can_check_if_transition_is_valid()
    {
        $this->assertTrue($this->stateMachine->canTransitionInstance('pending', 'in_progress'));
        $this->assertTrue($this->stateMachine->canTransitionInstance('in_progress', 'completed'));
        $this->assertFalse($this->stateMachine->canTransitionInstance('completed', 'pending'));
    }

    /** @test */
    public function it_can_transition_step_to_completed()
    {
        $workflow = Workflow::create(['name' => 'Test', 'type' => 'test']);
        $step = WorkflowStep::create([
            'workflow_id' => $workflow->id,
            'order' => 1,
            'title' => 'Step 1',
            'type' => 'task',
        ]);
        $user = $this->createUser();
        $instance = WorkflowInstance::create([
            'workflow_id' => $workflow->id,
            'user_id' => $user->id,
            'status' => 'in_progress',
        ]);

        $instanceStep = WorkflowInstanceStep::create([
            'workflow_instance_id' => $instance->id,
            'workflow_step_id' => $step->id,
            'status' => 'in_progress',
        ]);

        $this->stateMachine->transitionStep($instanceStep, 'completed', $user);

        $this->assertEquals('completed', $instanceStep->fresh()->status);
        $this->assertNotNull($instanceStep->fresh()->completed_at);
        $this->assertEquals($user->id, $instanceStep->fresh()->completed_by);
    }
}