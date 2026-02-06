<?php

namespace Kumogire\Workflow\Tests\Feature;

use Kumogire\Workflow\Tests\TestCase;
use Kumogire\Workflow\Models\Workflow;
use Kumogire\Workflow\Models\WorkflowStep;
use Laravel\Sanctum\Sanctum;

class WorkflowApiTest extends TestCase
{
    /** @test */
    public function it_can_list_active_workflows()
    {
        Workflow::create(['name' => 'Active 1', 'type' => 'test', 'is_active' => true]);
        Workflow::create(['name' => 'Active 2', 'type' => 'test', 'is_active' => true]);
        Workflow::create(['name' => 'Inactive', 'type' => 'test', 'is_active' => false]);

        $user = $this->createUser();
        Sanctum::actingAs($user);

        $response = $this->getJson('/api/workflows');

        $response->assertStatus(200)
            ->assertJsonCount(2, 'data');
    }

    /** @test */
    public function it_can_show_a_workflow_with_steps()
    {
        $workflow = Workflow::create(['name' => 'Test', 'type' => 'test', 'is_active' => true]);
        
        WorkflowStep::create([
            'workflow_id' => $workflow->id,
            'order' => 1,
            'title' => 'Step 1',
            'type' => 'task',
        ]);

        $user = $this->createUser();
        Sanctum::actingAs($user);

        $response = $this->getJson("/api/workflows/{$workflow->id}");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'name',
                    'type',
                    'steps',
                ],
            ]);
    }

    /** @test */
    public function it_can_filter_workflows_by_type()
    {
        Workflow::create(['name' => 'Onboarding', 'type' => 'onboarding', 'is_active' => true]);
        Workflow::create(['name' => 'Interview', 'type' => 'interview', 'is_active' => true]);

        $user = $this->createUser();
        Sanctum::actingAs($user);

        $response = $this->getJson('/api/workflows?type=onboarding');

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data')
            ->assertJsonFragment(['type' => 'onboarding']);
    }

    /** @test */
    public function it_requires_authentication_to_access_workflows()
    {
        $response = $this->getJson('/api/workflows');

        $response->assertStatus(401);
    }
}