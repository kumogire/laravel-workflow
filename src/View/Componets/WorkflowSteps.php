<?php

namespace Kumogire\Workflow\View\Components;

use Illuminate\View\Component;
use Kumogire\Workflow\Models\Workflow;

class WorkflowSteps extends Component
{
    public $workflow;
    public $steps;
    public $showActions;

    /**
     * Create a new component instance.
     */
    public function __construct($workflow, $showActions = true)
    {
        if (is_numeric($workflow)) {
            $this->workflow = Workflow::findOrFail($workflow);
        } else {
            $this->workflow = $workflow;
        }

        $this->steps = $this->workflow->steps()->with('actions')->orderBy('order')->get();
        $this->showActions = $showActions;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render()
    {
        return view('workflow::components.workflow-steps');
    }
}