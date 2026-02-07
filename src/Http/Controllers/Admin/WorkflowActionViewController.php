<?php

namespace Kumogire\Workflow\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Kumogire\Workflow\Models\Workflow;
use Kumogire\Workflow\Models\WorkflowStep;
use Kumogire\Workflow\Models\WorkflowAction;

/****
 * Web UI/HTML Controller for managing workflow actions in the admin panel.
 */
class WorkflowActionViewController extends Controller
{
    /**
     * Show the form for creating a new action.
     */
    public function create(Workflow $workflow, WorkflowStep $step)
    {
        return view('workflow::admin.actions.create', compact('workflow', 'step'));
    }

    /**
     * Store a newly created action.
     */
    public function store(Request $request, Workflow $workflow, WorkflowStep $step)
    {
        $validated = $request->validate([
            'type' => 'required|string|in:email,sms,webhook,data_save',
            'trigger' => 'required|string|in:on_step_start,on_step_complete',
            'configuration' => 'required|array',
        ]);

        $validated['workflow_step_id'] = $step->id;

        $action = WorkflowAction::create($validated);

        return redirect()
            ->route('workflow-admin.workflows.show', $workflow)
            ->with('success', 'Action created successfully.');
    }

    /**
     * Show the form for editing the specified action.
     */
    public function edit(Workflow $workflow, WorkflowStep $step, WorkflowAction $action)
    {
        return view('workflow::admin.actions.edit', compact('workflow', 'step', 'action'));
    }

    /**
     * Update the specified action.
     */
    public function update(Request $request, Workflow $workflow, WorkflowStep $step, WorkflowAction $action)
    {
        $validated = $request->validate([
            'type' => 'required|string|in:email,sms,webhook,data_save',
            'trigger' => 'required|string|in:on_step_start,on_step_complete',
            'configuration' => 'required|array',
        ]);

        $action->update($validated);

        return redirect()
            ->route('workflow-admin.workflows.show', $workflow)
            ->with('success', 'Action updated successfully.');
    }

    /**
     * Remove the specified action.
     */
    public function destroy(Workflow $workflow, WorkflowStep $step, WorkflowAction $action)
    {
        $action->delete();

        return redirect()
            ->route('workflow-admin.workflows.show', $workflow)
            ->with('success', 'Action deleted successfully.');
    }
}