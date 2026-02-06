<?php

namespace Kumogire\Workflow\Tests\Unit\Models;

use Kumogire\Workflow\Tests\TestCase;
use Kumogire\Workflow\Models\Workflow;
use Kumogire\Workflow\Models\WorkflowStep;

class WorkflowTest extends TestCase
{
    /** @test */
    public function it_can_create_a_workflow()
    {
        $workflow = Workflow::create([
            'name' => 'Test Workflow',
            'description' => 'A test workflow',
            'type' => 'testing',
            'is_active' => true,
        ]);

        $this->assertDatabaseHas('workflows', [
            'name' => 'Test Workflow',
            'type' => 'testing',
        ]);

        $this->assertTrue($workflow->is_active);
    }

    /** @test */
    public function it_has_steps_relationship()
    {
        $workflow = Workflow::create([
            'name' => 'Test Workflow',
            'type' => 'testing',
        ]);

        WorkflowStep::create([
            'workflow_id' => $workflow->id,
            'order' => 1,
            'title' => 'Step 1',
            'type' => 'task',
        ]);

        WorkflowStep::create([
            'workflow_id' => $workflow->id,
            'order' => 2,
            'title' => 'Step 2',
            'type' => 'task',
        ]);

        $this->assertCount(2, $workflow->steps);
    }

    /** @test */
    public function it_can_get_first_step()
    {
        $workflow = Workflow::create([
            'name' => 'Test Workflow',
            'type' => 'testing',
        ]);

        $step1 = WorkflowStep::create([
            'workflow_id' => $workflow->id,
            'order' => 1,
            'title' => 'Step 1',
            'type' => 'task',
        ]);

        WorkflowStep::create([
            'workflow_id' => $workflow->id,
            'order' => 2,
            'title' => 'Step 2',
            'type' => 'task',
        ]);

        $firstStep = $workflow->firstStep();

        $this->assertEquals($step1->id, $firstStep->id);
        $this->assertEquals('Step 1', $firstStep->title);
    }

    /** @test */
    public function it_scopes_to_active_workflows()
    {
        Workflow::create(['name' => 'Active', 'type' => 'test', 'is_active' => true]);
        Workflow::create(['name' => 'Inactive', 'type' => 'test', 'is_active' => false]);

        $active = Workflow::active()->get();

        $this->assertCount(1, $active);
        $this->assertEquals('Active', $active->first()->name);
    }

    /** @test */
    public function it_scopes_by_type()
    {
        Workflow::create(['name' => 'Onboarding', 'type' => 'onboarding']);
        Workflow::create(['name' => 'Interview', 'type' => 'interview']);

        $onboarding = Workflow::ofType('onboarding')->get();

        $this->assertCount(1, $onboarding);
        $this->assertEquals('Onboarding', $onboarding->first()->name);
    }
}