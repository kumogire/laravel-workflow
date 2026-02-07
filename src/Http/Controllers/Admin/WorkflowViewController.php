<?php

namespace Kumogire\Workflow\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Kumogire\Workflow\Models\Workflow;
use Kumogire\Workflow\Models\WorkflowStep;

/**
 * Web UI/HTML Controller for managing workflows in the admin panel.
 */
class WorkflowViewController extends Controller
{
    /**
     * Display a listing of workflows.
     */
    public function index()
    {
        $workflows = Workflow::withCount('steps')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('workflow::admin.workflows.index', compact('workflows'));
    }

    /**
     * Show the form for creating a new workflow.
     */
    public function create()
    {
        return view('workflow::admin.workflows.create');
    }

    /**
     * Store a newly created workflow.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|string|max:255',
            'is_active' => 'boolean',
        ]);

        $workflow = Workflow::create($validated);

        return redirect()
            ->route('workflow-admin.workflows.show', $workflow)
            ->with('success', 'Workflow created successfully.');
    }

    /**
     * Display the specified workflow.
     */
    public function show(Workflow $workflow)
    {
        $workflow->load('steps');

        return view('workflow::admin.workflows.show', compact('workflow'));
    }

    /**
     * Show the form for editing the specified workflow.
     */
    public function edit(Workflow $workflow)
    {
        return view('workflow::admin.workflows.edit', compact('workflow'));
    }

    /**
     * Update the specified workflow.
     */
    public function update(Request $request, Workflow $workflow)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|string|max:255',
            'is_active' => 'boolean',
        ]);

        $workflow->update($validated);

        return redirect()
            ->route('workflow-admin.workflows.show', $workflow)
            ->with('success', 'Workflow updated successfully.');
    }

    /**
     * Remove the specified workflow.
     */
    public function destroy(Workflow $workflow)
    {
        $workflow->delete();

        return redirect()
            ->route('workflow-admin.workflows.index')
            ->with('success', 'Workflow deleted successfully.');
    }
}