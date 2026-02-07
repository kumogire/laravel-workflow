<?php

namespace Kumogire\Workflow\View\Components;

use Illuminate\View\Component;
use Kumogire\Workflow\Models\Workflow;

class RecentWorkflows extends Component
{
    public $workflows;
    public $limit;
    public $title;

    /**
     * Create a new component instance.
     */
    public function __construct($limit = 5, $title = 'Recent Workflows')
    {
        $this->limit = $limit;
        $this->title = $title;
        $this->workflows = Workflow::latest()->limit($limit)->get();
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render()
    {
        return view('workflow::components.recent-workflows');
    }
}