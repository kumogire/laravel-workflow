<?php

use Illuminate\Support\Facades\Route;
use Kumogire\Workflow\Http\Controllers\WorkflowController;
use Kumogire\Workflow\Http\Controllers\WorkflowInstanceController;

Route::prefix(config('workflow.routes.api_prefix'))
    ->middleware(config('workflow.routes.api_middleware'))
    ->group(function () {
        
        Route::get('/', [WorkflowController::class, 'index']);
        Route::get('/{workflow}', [WorkflowController::class, 'show']);
        
        Route::post('/instances', [WorkflowInstanceController::class, 'store']);
        Route::get('/instances/{instance}', [WorkflowInstanceController::class, 'show']);
        Route::post('/instances/{instance}/complete-step', [WorkflowInstanceController::class, 'completeStep']);
        Route::get('/user/{user}', [WorkflowInstanceController::class, 'userInstances']);
    });