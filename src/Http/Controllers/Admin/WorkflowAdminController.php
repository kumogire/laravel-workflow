<?php

namespace Kumogire\Workflow\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Kumogire\Workflow\Models\Workflow;
use Kumogire\Workflow\Http\Resources\WorkflowResource;
use Kumogire\Workflow\Http\Requests\Admin\CreateWorkflowRequest;
use Kumogire\Workflow\Http\Requests\Admin\UpdateWorkflowRequest;

/**
 * API/JSON controller for managing workflows in the admin panel. 
 * Provides endpoints for listing, creating, viewing, updating, and deleting workflows.
 * All endpoints are protected by admin authentication middleware. 
 * The controller uses request validation to ensure data integrity and returns standardized JSON responses using API resources.
 */
class WorkflowAdminController extends Controller
{
    /**
     * Display a listing of all workflows.
     */
    public function index(Request $request)
    {
        $query = Workflow::query();

        if ($request->has('type')) {
            $query->where('type', $request->type);
        }

        $workflows = $query->withCount('steps')
            ->orderBy('created_at', 'desc')
            ->paginate($request->get('per_page', 15));

        return WorkflowResource::collection($workflows);
    }

    /**
     * Store a newly created workflow.
     */
    public function store(CreateWorkflowRequest $request)
    {
        $workflow = Workflow::create($request->validated());

        return new WorkflowResource($workflow);
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
     * Update the specified workflow.
     */
    public function update(UpdateWorkflowRequest $request, Workflow $workflow)
    {
        $workflow->update($request->validated());

        return new WorkflowResource($workflow->fresh());
    }

    /**
     * Remove the specified workflow.
     */
    public function destroy(Workflow $workflow)
    {
        $workflow->delete();

        return response()->json([
            'message' => 'Workflow deleted successfully',
        ]);
    }
}
