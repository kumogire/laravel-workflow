<?php

namespace Kumogire\Workflow\View\Components;

use Illuminate\View\Component;
use Kumogire\Workflow\Models\Workflow;

class WorkflowList extends Component
{
    public $workflows;
    public $type;
    public $showInactive;
    public $limit;

    /**
     * Create a new component instance.
     */
    public function __construct($type = null, $showInactive = false, $limit = null)
    {
        $this->type = $type;
        $this->showInactive = $showInactive;
        $this->limit = $limit;

        $query = Workflow::query();

        if ($type) {
            $query->where('type', $type);
        }

        if (!$showInactive) {
            $query->where('is_active', true);
        }

        $query->withCount('steps')->orderBy('created_at', 'desc');

        if ($limit) {
            $this->workflows = $query->limit($limit)->get();
        } else {
            $this->workflows = $query->get();
        }
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render()
    {
        return view('workflow::components.workflow-list');
    }
}