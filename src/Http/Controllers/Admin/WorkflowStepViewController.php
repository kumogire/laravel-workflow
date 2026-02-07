<?php

namespace Kumogire\Workflow\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Kumogire\Workflow\Models\Workflow;
use Kumogire\Workflow\Models\WorkflowStep;

/**
 * Web UI/HTML Controller for managing workflow steps in the admin panel.
 */
class WorkflowStepViewController extends Controller
{
    /**
     * Show the form for creating a new step.
     */
    public function create(Workflow $workflow)
    {
        $maxOrder = $workflow->steps()->max('order') ?? 0;
        $nextOrder = $maxOrder + 1;

        return view('workflow::admin.steps.create', compact('workflow', 'nextOrder'));
    }

    /**
     * Store a newly created step.
     */
    public function store(Request $request, Workflow $workflow)
    {
        $validated = $request->validate([
            'order' => 'required|integer|min:0',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|string|max:255',
            'configuration' => 'nullable|array',
            'condition_type' => 'nullable|string|in:always,if_data_equals,if_data_contains,if_role',
            'condition_config' => 'nullable|array',
            'skip_if_condition_false' => 'boolean',
            'can_view_roles' => 'nullable|array',
            'can_complete_roles' => 'nullable|array',
        ]);

        $validated['workflow_id'] = $workflow->id;

        $step = WorkflowStep::create($validated);

        return redirect()
            ->route('workflow-admin.workflows.show', $workflow)
            ->with('success', 'Step created successfully.');
    }

    /**
     * Show the form for editing the specified step.
     */
    public function edit(Workflow $workflow, WorkflowStep $step)
    {
        return view('workflow::admin.steps.edit', compact('workflow', 'step'));
    }

    /**
     * Update the specified step.
     */
    public function update(Request $request, Workflow $workflow, WorkflowStep $step)
    {
        $validated = $request->validate([
            'order' => 'required|integer|min:0',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|string|max:255',
            'configuration' => 'nullable|array',
            'condition_type' => 'nullable|string|in:always,if_data_equals,if_data_contains,if_role',
            'condition_config' => 'nullable|array',
            'skip_if_condition_false' => 'boolean',
            'can_view_roles' => 'nullable|array',
            'can_complete_roles' => 'nullable|array',
        ]);

        $step->update($validated);

        return redirect()
            ->route('workflow-admin.workflows.show', $workflow)
            ->with('success', 'Step updated successfully.');
    }

    /**
     * Remove the specified step.
     */
    public function destroy(Workflow $workflow, WorkflowStep $step)
    {
        $step->delete();

        return redirect()
            ->route('workflow-admin.workflows.show', $workflow)
            ->with('success', 'Step deleted successfully.');
    }
}