<?php

namespace Kumogire\Workflow\View\Components;

use Illuminate\View\Component;
use Kumogire\Workflow\Models\WorkflowInstance;

class UserWorkflows extends Component
{
    public $instances;
    public $user;
    public $status;
    public $limit;

    /**
     * Create a new component instance.
     */
    public function __construct($user = null, $status = null, $limit = 10)
    {
        $this->user = $user ?? auth()->user();
        $this->status = $status;
        $this->limit = $limit;

        $query = WorkflowInstance::where('user_id', $this->user->id)
            ->with(['workflow', 'currentStep']);

        if ($status) {
            $query->where('status', $status);
        }

        $this->instances = $query->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render()
    {
        return view('workflow::components.user-workflows');
    }
}