<?php

namespace Kumogire\Workflow\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Kumogire\Workflow\Models\Workflow;
use Kumogire\Workflow\Models\WorkflowInstance;
use Kumogire\Workflow\Services\WorkflowService;
use Kumogire\Workflow\Http\Requests\StartWorkflowRequest;
use Kumogire\Workflow\Http\Requests\CompleteStepRequest;
use Kumogire\Workflow\Http\Resources\WorkflowInstanceResource;
use Kumogire\Workflow\Exceptions\WorkflowException;
use Kumogire\Workflow\Exceptions\PermissionDeniedException;

class WorkflowInstanceController extends Controller
{
    protected WorkflowService $workflowService;

    public function __construct(WorkflowService $workflowService)
    {
        $this->workflowService = $workflowService;
    }

    /**
     * Start a new workflow instance.
     */
    public function store(StartWorkflowRequest $request)
    {
        try {
            $workflow = Workflow::findOrFail($request->workflow_id);
            $user = $request->user();
            $metadata = $request->get('metadata', []);

            $instance = $this->workflowService->startWorkflow($workflow, $user, $metadata);

            return new WorkflowInstanceResource($instance->load(['workflow', 'currentStep']));
        } catch (WorkflowException $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Display the specified workflow instance.
     */
    public function show(Request $request, WorkflowInstance $instance)
    {
        // Check if user owns this instance
        if ($instance->user_id !== $request->user()->id) {
            return response()->json([
                'message' => 'Unauthorized',
            ], 403);
        }

        $details = $this->workflowService->getCurrentStepDetails($instance, $request->user());

        return response()->json($details);
    }

    /**
     * Complete the current step and advance.
     */
    public function completeStep(CompleteStepRequest $request, WorkflowInstance $instance)
    {
        try {
            // Check if user owns this instance
            if ($instance->user_id !== $request->user()->id) {
                return response()->json([
                    'message' => 'Unauthorized',
                ], 403);
            }

            $data = $request->get('data', []);
            $instance = $this->workflowService->completeStep($instance, $request->user(), $data);

            return new WorkflowInstanceResource($instance->load(['workflow', 'currentStep']));
        } catch (PermissionDeniedException $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 403);
        } catch (WorkflowException $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Get all workflow instances for a user.
     */
    public function userInstances(Request $request)
    {
        $user = $request->user();

        $query = WorkflowInstance::where('user_id', $user->id);

        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // Filter by workflow type
        if ($request->has('workflow_type')) {
            $query->whereHas('workflow', function ($q) use ($request) {
                $q->where('type', $request->workflow_type);
            });
        }

        $instances = $query->with(['workflow', 'currentStep'])
            ->orderBy('created_at', 'desc')
            ->paginate($request->get('per_page', 15));

        return WorkflowInstanceResource::collection($instances);
    }

    /**
     * Pause a workflow instance.
     */
    public function pause(Request $request, WorkflowInstance $instance)
    {
        try {
            if ($instance->user_id !== $request->user()->id) {
                return response()->json(['message' => 'Unauthorized'], 403);
            }

            $this->workflowService->pauseWorkflow($instance);

            return new WorkflowInstanceResource($instance->fresh());
        } catch (WorkflowException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }
    }

    /**
     * Resume a paused workflow instance.
     */
    public function resume(Request $request, WorkflowInstance $instance)
    {
        try {
            if ($instance->user_id !== $request->user()->id) {
                return response()->json(['message' => 'Unauthorized'], 403);
            }

            $this->workflowService->resumeWorkflow($instance);

            return new WorkflowInstanceResource($instance->fresh());
        } catch (WorkflowException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }
    }

    /**
     * Abandon a workflow instance.
     */
    public function abandon(Request $request, WorkflowInstance $instance)
    {
        try {
            if ($instance->user_id !== $request->user()->id) {
                return response()->json(['message' => 'Unauthorized'], 403);
            }

            $this->workflowService->abandonWorkflow($instance);

            return new WorkflowInstanceResource($instance->fresh());
        } catch (WorkflowException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }
    }
}
