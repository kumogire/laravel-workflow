<?php

use Illuminate\Support\Facades\Route;
use Kumogire\Workflow\Http\Controllers\WorkflowController;
use Kumogire\Workflow\Http\Controllers\WorkflowInstanceController;

Route::prefix(config('workflow.routes.api_prefix', 'api/workflows'))
    ->middleware(config('workflow.routes.api_middleware', ['api', 'auth:sanctum']))
    ->group(function () {
        
        // Workflows
        Route::get('/', [WorkflowController::class, 'index']);
        Route::get('/{workflow}', [WorkflowController::class, 'show']);
        Route::get('/{workflow}/availability', [WorkflowController::class, 'checkAvailability']);
        
        // Workflow Instances
        Route::post('/instances', [WorkflowInstanceController::class, 'store']);
        Route::get('/instances/user', [WorkflowInstanceController::class, 'userInstances']);
        Route::get('/instances/{instance}', [WorkflowInstanceController::class, 'show']);
        Route::post('/instances/{instance}/complete-step', [WorkflowInstanceController::class, 'completeStep']);
        Route::post('/instances/{instance}/pause', [WorkflowInstanceController::class, 'pause']);
        Route::post('/instances/{instance}/resume', [WorkflowInstanceController::class, 'resume']);
        Route::post('/instances/{instance}/abandon', [WorkflowInstanceController::class, 'abandon']);
    });
    