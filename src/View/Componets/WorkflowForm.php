<?php

namespace Kumogire\Workflow\View\Components;

use Illuminate\View\Component;
use Kumogire\Workflow\Models\Workflow;

class WorkflowForm extends Component
{
    public $workflow;
    public $action;
    public $method;

    /**
     * Create a new component instance.
     */
    public function __construct($workflow = null, $action = null)
    {
        $this->workflow = $workflow;
        
        if ($workflow) {
            $this->action = $action ?? route('workflow-admin.workflows.update', $workflow);
            $this->method = 'PUT';
        } else {
            $this->action = $action ?? route('workflow-admin.workflows.store');
            $this->method = 'POST';
        }
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render()
    {
        return view('workflow::components.workflow-form');
    }
}