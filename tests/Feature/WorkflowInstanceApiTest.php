<?php

namespace Kumogire\Workflow\Tests\Feature;

use Kumogire\Workflow\Tests\TestCase;
use Kumogire\Workflow\Models\Workflow;
use Kumogire\Workflow\Models\WorkflowStep;
use Kumogire\Workflow\Models\WorkflowInstance;
use Laravel\Sanctum\Sanctum;
use Illuminate\Support\Facades\Event;

class WorkflowInstanceApiTest extends TestCase
{
    /** @test */
    public function it_can_start_a_workflow_instance()
    {
        Event::fake();

        $workflow = Workflow::create(['name' => 'Test', 'type' => 'test', 'is_active' => true]);
        WorkflowStep::create([
            'workflow_id' => $workflow->id,
            'order' => 1,
            'title' => 'Step 1',
            'type' => 'task',
        ]);

        $user = $this->createUser();
        Sanctum::actingAs($user);

        $response = $this->postJson('/api/workflows/instances', [
            'workflow_id' => $workflow->id,
            'metadata' => ['department' => 'engineering'],
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'workflow_id',
                    'status',
                    'current_step_id',
                ],
            ]);

        $this->assertDatabaseHas('workflow_instances', [
            'workflow_id' => $workflow->id,
            'user_id' => $user->id,
            'status' => 'in_progress',
        ]);
    }

    /** @test */
    public function it_cannot_start_inactive_workflow()
    {
        $workflow = Workflow::create(['name' => 'Test', 'type' => 'test', 'is_active' => false]);
        WorkflowStep::create([
            'workflow_id' => $workflow->id,
            'order' => 1,
            'title' => 'Step 1',
            'type' => 'task',
        ]);

        $user = $this->createUser();
        Sanctum::actingAs($user);

        $response = $this->postJson('/api/workflows/instances', [
            'workflow_id' => $workflow->id,
        ]);

        $response->assertStatus(422)
            ->assertJson([
                'message' => 'Workflow Test is not active',
            ]);
    }

    /** @test */
    public function it_can_get_workflow_instance_details()
    {
        $workflow = Workflow::create(['name' => 'Test', 'type' => 'test', 'is_active' => true]);
        $step = WorkflowStep::create([
            'workflow_id' => $workflow->id,
            'order' => 1,
            'title' => 'Step 1',
            'type' => 'task',
        ]);

        $user = $this->createUser();
        Sanctum::actingAs($user);

        $instance = WorkflowInstance::create([
            'workflow_id' => $workflow->id,
            'user_id' => $user->id,
            'current_step_id' => $step->id,
            'status' => 'in_progress',
        ]);

        $response = $this->getJson("/api/workflows/instances/{$instance->id}");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'instance_id',
                'status',
                'current_step',
                'workflow',
            ]);
    }

    /** @test */
    public function it_prevents_users_from_viewing_other_users_instances()
    {
        $workflow = Workflow::create(['name' => 'Test', 'type' => 'test', 'is_active' => true]);
        $step = WorkflowStep::create([
            'workflow_id' => $workflow->id,
            'order' => 1,
            'title' => 'Step 1',
            'type' => 'task',
        ]);

        $user1 = $this->createUser();
        $user2 = $this->createUser(['email' => 'user2@example.com']);

        $instance = WorkflowInstance::create([
            'workflow_id' => $workflow->id,
            'user_id' => $user1->id,
            'current_step_id' => $step->id,
            'status' => 'in_progress',
        ]);

        Sanctum::actingAs($user2);

        $response = $this->getJson("/api/workflows/instances/{$instance->id}");

        $response->assertStatus(403);
    }

    /** @test */
    public function it_can_complete_a_step()
    {
        Event::fake();

        $workflow = Workflow::create(['name' => 'Test', 'type' => 'test', 'is_active' => true]);
        
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
        Sanctum::actingAs($user);

        $instance = WorkflowInstance::create([
            'workflow_id' => $workflow->id,
            'user_id' => $user->id,
            'current_step_id' => $step1->id,
            'status' => 'in_progress',
        ]);

        $response = $this->postJson("/api/workflows/instances/{$instance->id}/complete-step", [
            'data' => ['answer' => 'yes'],
        ]);

        $response->assertStatus(200);

        $this->assertEquals($step2->id, $instance->fresh()->current_step_id);
    }

    /** @test */
    public function it_prevents_completing_step_without_permission()
    {
        $workflow = Workflow::create(['name' => 'Test', 'type' => 'test', 'is_active' => true]);
        
        $step = WorkflowStep::create([
            'workflow_id' => $workflow->id,
            'order' => 1,
            'title' => 'Manager Only',
            'type' => 'task',
            'can_complete_roles' => ['manager'],
        ]);

        $user = $this->createUser(['role' => 'employee']);
        Sanctum::actingAs($user);

        $instance = WorkflowInstance::create([
            'workflow_id' => $workflow->id,
            'user_id' => $user->id,
            'current_step_id' => $step->id,
            'status' => 'in_progress',
        ]);

        $response = $this->postJson("/api/workflows/instances/{$instance->id}/complete-step");

        $response->assertStatus(403);
    }

    /** @test */
    public function it_can_list_user_workflow_instances()
    {
        $workflow = Workflow::create(['name' => 'Test', 'type' => 'test', 'is_active' => true]);
        $step = WorkflowStep::create([
            'workflow_id' => $workflow->id,
            'order' => 1,
            'title' => 'Step 1',
            'type' => 'task',
        ]);

        $user = $this->createUser();
        Sanctum::actingAs($user);

        WorkflowInstance::create([
            'workflow_id' => $workflow->id,
            'user_id' => $user->id,
            'current_step_id' => $step->id,
            'status' => 'in_progress',
        ]);

        WorkflowInstance::create([
            'workflow_id' => $workflow->id,
            'user_id' => $user->id,
            'current_step_id' => $step->id,
            'status' => 'completed',
        ]);

        $response = $this->getJson('/api/workflows/instances/user');

        $response->assertStatus(200)
            ->assertJsonCount(2, 'data');
    }

    /** @test */
    public function it_can_pause_a_workflow_instance()
    {
        $workflow = Workflow::create(['name' => 'Test', 'type' => 'test', 'is_active' => true]);
        $step = WorkflowStep::create([
            'workflow_id' => $workflow->id,
            'order' => 1,
            'title' => 'Step 1',
            'type' => 'task',
        ]);

        $user = $this->createUser();
        Sanctum::actingAs($user);

        $instance = WorkflowInstance::create([
            'workflow_id' => $workflow->id,
            'user_id' => $user->id,
            'current_step_id' => $step->id,
            'status' => 'in_progress',
        ]);

        $response = $this->postJson("/api/workflows/instances/{$instance->id}/pause");

        $response->assertStatus(200);
        $this->assertEquals('paused', $instance->fresh()->status);
    }

    /** @test */
    public function it_can_resume_a_workflow_instance()
    {
        $workflow = Workflow::create(['name' => 'Test', 'type' => 'test', 'is_active' => true]);
        $step = WorkflowStep::create([
            'workflow_id' => $workflow->id,
            'order' => 1,
            'title' => 'Step 1',
            'type' => 'task',
        ]);

        $user = $this->createUser();
        Sanctum::actingAs($user);

        $instance = WorkflowInstance::create([
            'workflow_id' => $workflow->id,
            'user_id' => $user->id,
            'current_step_id' => $step->id,
            'status' => 'paused',
        ]);

        $response = $this->postJson("/api/workflows/instances/{$instance->id}/resume");

        $response->assertStatus(200);
        $this->assertEquals('in_progress', $instance->fresh()->status);
    }
}