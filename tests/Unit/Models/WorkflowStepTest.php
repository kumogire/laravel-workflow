<?php

namespace Kumogire\Workflow\Tests\Unit\Models;

use Kumogire\Workflow\Tests\TestCase;
use Kumogire\Workflow\Models\Workflow;
use Kumogire\Workflow\Models\WorkflowStep;

class WorkflowStepTest extends TestCase
{
    /** @test */
    public function it_can_get_next_step()
    {
        $workflow = Workflow::create(['name' => 'Test', 'type' => 'test']);

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

        $nextStep = $step1->nextStep();

        $this->assertEquals($step2->id, $nextStep->id);
    }

    /** @test */
    public function it_can_get_previous_step()
    {
        $workflow = Workflow::create(['name' => 'Test', 'type' => 'test']);

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

        $previousStep = $step2->previousStep();

        $this->assertEquals($step1->id, $previousStep->id);
    }

    /** @test */
    public function it_checks_user_can_view_based_on_roles()
    {
        $workflow = Workflow::create(['name' => 'Test', 'type' => 'test']);

        $step = WorkflowStep::create([
            'workflow_id' => $workflow->id,
            'order' => 1,
            'title' => 'Step 1',
            'type' => 'task',
            'can_view_roles' => ['admin', 'manager'],
        ]);

        $adminUser = $this->createUser(['role' => 'admin']);
        $employeeUser = $this->createUser(['role' => 'employee', 'email' => 'employee@example.com']);

        $this->assertTrue($step->canView($adminUser));
        $this->assertFalse($step->canView($employeeUser));
    }

    /** @test */
    public function it_allows_view_when_no_roles_specified()
    {
        $workflow = Workflow::create(['name' => 'Test', 'type' => 'test']);

        $step = WorkflowStep::create([
            'workflow_id' => $workflow->id,
            'order' => 1,
            'title' => 'Step 1',
            'type' => 'task',
            'can_view_roles' => [],
        ]);

        $user = $this->createUser();

        $this->assertTrue($step->canView($user));
    }

    /** @test */
    public function it_checks_user_can_complete_based_on_roles()
    {
        $workflow = Workflow::create(['name' => 'Test', 'type' => 'test']);

        $step = WorkflowStep::create([
            'workflow_id' => $workflow->id,
            'order' => 1,
            'title' => 'Step 1',
            'type' => 'task',
            'can_complete_roles' => ['manager'],
        ]);

        $managerUser = $this->createUser(['role' => 'manager']);
        $employeeUser = $this->createUser(['role' => 'employee', 'email' => 'employee@example.com']);

        $this->assertTrue($step->canComplete($managerUser));
        $this->assertFalse($step->canComplete($employeeUser));
    }
}