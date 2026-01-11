<?php

namespace Kumogire\Workflow\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Kumogire\Workflow\Models\Workflow;
use Kumogire\Workflow\Http\Resources\WorkflowResource;

class WorkflowController extends Controller
{
    /**
     * Display a listing of workflows.
     */
    public function index(Request $request)
    {
        $query = Workflow::query();

        // Filter by type
        if ($request->has('type')) {
            $query->where('type', $request->type);
        }

        // Only active workflows by default
        if ($request->get('include_inactive') !== 'true') {
            $query->where('is_active', true);
        }

        // Load steps if requested
        if ($request->get('include_steps') === 'true') {
            $query->with('steps');
        }

        $workflows = $query->withCount('steps')->paginate($request->get('per_page', 15));

        return WorkflowResource::collection($workflows);
    }

    /**
     * Display the specified workflow.
     */
    public function show(Workflow $workflow)
    {
        $workflow->load('steps');

        return new WorkflowResource($workflow);
    }

    /**
     * Check if user can start a workflow (considering dependencies).
     */
    public function checkAvailability(Request $request, Workflow $workflow)
    {
        $user = $request->user();

        // Check if workflow is active
        if (!$workflow->is_active) {
            return response()->json([
                'available' => false,
                'reason' => 'Workflow is not active',
            ]);
        }

        // Check dependencies
        $dependencies = $workflow->dependencies()->with('dependsOnWorkflow')->get();
        $missingDependencies = [];

        foreach ($dependencies as $dependency) {
            $completed = \Kumogire\Workflow\Models\WorkflowInstance::where('workflow_id', $dependency->depends_on_workflow_id)
                ->where('user_id', $user->id)
                ->where('status', 'completed')
                ->exists();

            if (!$completed) {
                $missingDependencies[] = [
                    'id' => $dependency->dependsOnWorkflow->id,
                    'name' => $dependency->dependsOnWorkflow->name,
                ];
            }
        }

        return response()->json([
            'available' => empty($missingDependencies),
            'missing_dependencies' => $missingDependencies,
        ]);
    }
}
