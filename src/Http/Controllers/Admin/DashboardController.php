<?php

namespace Kumogire\Workflow\Http\Controllers\Admin;

use Illuminate\Routing\Controller;
use Kumogire\Workflow\Models\Workflow;
use Kumogire\Workflow\Models\WorkflowInstance;

/**
 * Web UI/HTML Controller for the admin dashboard, showing workflow statistics and recent activity.
 */
class DashboardController extends Controller
{
    /**
     * Display the admin dashboard.
     */
    public function index()
    {
        $stats = [
            'total_workflows' => Workflow::count(),
            'active_workflows' => Workflow::where('is_active', true)->count(),
            'total_instances' => WorkflowInstance::count(),
            'in_progress_instances' => WorkflowInstance::where('status', 'in_progress')->count(),
            'completed_instances' => WorkflowInstance::where('status', 'completed')->count(),
        ];

        $recentWorkflows = Workflow::latest()->take(5)->get();
        $recentInstances = WorkflowInstance::with('workflow', 'user')
            ->latest()
            ->take(10)
            ->get();

        return view('workflow::admin.dashboard', compact('stats', 'recentWorkflows', 'recentInstances'));
    }
}