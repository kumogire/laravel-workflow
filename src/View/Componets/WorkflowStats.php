<?php

namespace Kumogire\Workflow\View\Components;

use Illuminate\View\Component;
use Kumogire\Workflow\Models\Workflow;
use Kumogire\Workflow\Models\WorkflowInstance;

class WorkflowStats extends Component
{
    public $stats;

    /**
     * Create a new component instance.
     */
    public function __construct()
    {
        $this->stats = [
            'total_workflows' => Workflow::count(),
            'active_workflows' => Workflow::where('is_active', true)->count(),
            'total_instances' => WorkflowInstance::count(),
            'in_progress_instances' => WorkflowInstance::where('status', 'in_progress')->count(),
            'completed_instances' => WorkflowInstance::where('status', 'completed')->count(),
            'paused_instances' => WorkflowInstance::where('status', 'paused')->count(),
        ];
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render()
    {
        return view('workflow::components.workflow-stats');
    }
}