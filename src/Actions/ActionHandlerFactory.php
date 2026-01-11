<?php

namespace Kumogire\Workflow\Actions;

use Kumogire\Workflow\Contracts\ActionHandler;
use Kumogire\Workflow\Exceptions\WorkflowException;

class ActionHandlerFactory
{
    /**
     * Create an action handler instance based on type
     */
    public function make(string $type): ActionHandler
    {
        $handlers = config('workflow.action_handlers', []);

        if (!isset($handlers[$type])) {
            throw new WorkflowException("Unknown action type: {$type}");
        }

        $handlerClass = $handlers[$type];

        if (!class_exists($handlerClass)) {
            throw new WorkflowException("Action handler class not found: {$handlerClass}");
        }

        $handler = app($handlerClass);

        if (!$handler instanceof ActionHandler) {
            throw new WorkflowException("Action handler must implement ActionHandler interface");
        }

        return $handler;
    }
}
