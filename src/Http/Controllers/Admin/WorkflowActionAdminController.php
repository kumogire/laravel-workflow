<?php

namespace Kumogire\Workflow\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Kumogire\Workflow\Models\WorkflowAction;
use Kumogire\Workflow\Http\Requests\Admin\CreateWorkflowActionRequest;

/**
 * API/JSON controller for managing workflow actions in the admin panel. 
 * Provides endpoints for listing, creating, viewing, updating, and deleting workflow actions.
 * All endpoints are protected by admin authentication middleware. 
 * The controller uses request validation to ensure data integrity and returns standardized JSON responses using API resources.
 */
class WorkflowActionAdminController extends Controller
{
    /**
     * Display a listing of actions.
     */
    public function index(Request $request)
    {
        $query = WorkflowAction::query();

        if ($request->has('workflow_step_id')) {
            $query->where('workflow_step_id', $request->workflow_step_id);
        }

        $actions = $query->paginate($request->get('per_page', 50));

        return response()->json($actions);
    }

    /**
     * Store a newly created action.
     */
    public function store(CreateWorkflowActionRequest $request)
    {
        $action = WorkflowAction::create($request->validated());

        return response()->json($action, 201);
    }

    /**
     * Display the specified action.
     */
    public function show(WorkflowAction $action)
    {
        return response()->json($action);
    }

    /**
     * Update the specified action.
     */
    public function update(Request $request, WorkflowAction $action)
    {
        $validated = $request->validate([
            'type' => 'sometimes|string|in:email,sms,webhook,data_save',
            'trigger' => 'sometimes|string|in:on_step_start,on_step_complete',
            'configuration' => 'sometimes|array',
        ]);

        $action->update($validated);

        return response()->json($action);
    }

    /**
     * Remove the specified action.
     */
    public function destroy(WorkflowAction $action)
    {
        $action->delete();

        return response()->json([
            'message' => 'Action deleted successfully',
        ]);
    }
}
