<?php

namespace Kumogire\Workflow\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Kumogire\Workflow\Models\WorkflowStep;
use Kumogire\Workflow\Http\Resources\WorkflowStepResource;
use Kumogire\Workflow\Http\Requests\Admin\CreateWorkflowStepRequest;
use Kumogire\Workflow\Http\Requests\Admin\UpdateWorkflowStepRequest;

/**
 * API/JSON controller for managing workflow steps in the admin panel. 
 * Provides endpoints for listing, creating, viewing, updating, deleting, and reordering workflow steps.
 * All endpoints are protected by admin authentication middleware. 
 * The controller uses request validation to ensure data integrity and returns standardized JSON responses using API resources.
 */
class WorkflowStepAdminController extends Controller
{
    /**
     * Display a listing of steps for a workflow.
     */
    public function index(Request $request)
    {
        $query = WorkflowStep::query();

        if ($request->has('workflow_id')) {
            $query->where('workflow_id', $request->workflow_id);
        }

        $steps = $query->orderBy('order')
            ->paginate($request->get('per_page', 50));

        return WorkflowStepResource::collection($steps);
    }

    /**
     * Store a newly created step.
     */
    public function store(CreateWorkflowStepRequest $request)
    {
        $step = WorkflowStep::create($request->validated());

        return new WorkflowStepResource($step);
    }

    /**
     * Display the specified step.
     */
    public function show(WorkflowStep $step)
    {
        return new WorkflowStepResource($step);
    }

    /**
     * Update the specified step.
     */
    public function update(UpdateWorkflowStepRequest $request, WorkflowStep $step)
    {
        $step->update($request->validated());

        return new WorkflowStepResource($step->fresh());
    }

    /**
     * Remove the specified step.
     */
    public function destroy(WorkflowStep $step)
    {
        $step->delete();

        return response()->json([
            'message' => 'Step deleted successfully',
        ]);
    }

    /**
     * Reorder steps.
     */
    public function reorder(Request $request)
    {
        $request->validate([
            'steps' => 'required|array',
            'steps.*.id' => 'required|exists:workflow_steps,id',
            'steps.*.order' => 'required|integer|min:0',
        ]);

        foreach ($request->steps as $stepData) {
            WorkflowStep::where('id', $stepData['id'])
                ->update(['order' => $stepData['order']]);
        }

        return response()->json([
            'message' => 'Steps reordered successfully',
        ]);
    }
}
