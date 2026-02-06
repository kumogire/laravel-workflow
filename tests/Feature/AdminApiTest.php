<?php

namespace Kumogire\Workflow\Tests\Feature;

use Kumogire\Workflow\Tests\TestCase;
use Kumogire\Workflow\Models\Workflow;
use Kumogire\Workflow\Models\WorkflowStep;
use Laravel\Sanctum\Sanctum;

class AdminApiTest extends TestCase
{
    /** @test */
    public function it_can_create_a_workflow()
    {
        $user = $this->createUser(['role' => 'admin']);
        Sanctum::actingAs($user);

        $response = $this->postJson('/admin/workflows/workflows', [
            'name' => 'New Workflow',
            'description' => 'A test workflow',
            'type' => 'onboarding',
            'is_active' => true,
        ]);

        $response->assertStatus(200)
            ->assertJsonFragment(['name' => 'New Workflow']);

        $this->assertDatabaseHas('workflows', [
            'name' => 'New Workflow',
            'type' => 'onboarding',
        ]);
    }

    /** @test */
    public function it_can_update_a_workflow()
    {
        $workflow = Workflow::create([
            'name' => 'Original Name',
            'type' => 'test',
        ]);

        $user = $this->createUser(['role' => 'admin']);
        Sanctum::actingAs($user);

        $response = $this->putJson("/admin/workflows/workflows/{$workflow->id}", [
            'name' => 'Updated Name',
        ]);

        $response->assertStatus(200);
        $this->assertEquals('Updated Name', $workflow->fresh()->name);
    }

    /** @test */
    public function it_can_delete_a_workflow()
    {
        $workflow = Workflow::create(['name' => 'To Delete', 'type' => 'test']);

        $user = $this->createUser(['role' => 'admin']);
        Sanctum::actingAs($user);

        $response = $this->deleteJson("/admin/workflows/workflows/{$workflow->id}");

        $response->assertStatus(200);
        $this->assertDatabaseMissing('workflows', ['id' => $workflow->id]);
    }

    /** @test */
    public function it_can_create_a_workflow_step()
    {
        $workflow = Workflow::create(['name' => 'Test', 'type' => 'test']);

        $user = $this->createUser(['role' => 'admin']);
        Sanctum::actingAs($user);

        $response = $this->postJson('/admin/workflows/steps', [
            'workflow_id' => $workflow->id,
            'order' => 1,
            'title' => 'First Step',
            'type' => 'task',
            'can_complete_roles' => ['employee'],
        ]);

        $response->assertStatus(200);
        
        $this->assertDatabaseHas('workflow_steps', [
            'workflow_id' => $workflow->id,
            'title' => 'First Step',
        ]);
    }

    /** @test */
    public function it_can_reorder_steps()
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

        $user = $this->createUser(['role' => 'admin']);
        Sanctum::actingAs($user);

        $response = $this->postJson('/admin/workflows/steps/reorder', [
            'steps' => [
                ['id' => $step2->id, 'order' => 1],
                ['id' => $step1->id, 'order' => 2],
            ],
        ]);

        $response->assertStatus(200);
        
        $this->assertEquals(2, $step1->fresh()->order);
        $this->assertEquals(1, $step2->fresh()->order);
    }
}