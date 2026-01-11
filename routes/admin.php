<?php

use Illuminate\Support\Facades\Route;
use Kumogire\Workflow\Http\Controllers\Admin\WorkflowAdminController;
use Kumogire\Workflow\Http\Controllers\Admin\WorkflowStepAdminController;
use Kumogire\Workflow\Http\Controllers\Admin\WorkflowActionAdminController;

Route::prefix(config('workflow.routes.admin_prefix', 'admin/workflows'))
    ->middleware(config('workflow.routes.admin_middleware', ['web', 'auth', 'admin']))
    ->group(function () {
        
        // Workflow Management
        Route::apiResource('workflows', WorkflowAdminController::class);
        
        // Workflow Step Management
        Route::apiResource('steps', WorkflowStepAdminController::class);
        Route::post('steps/reorder', [WorkflowStepAdminController::class, 'reorder']);
        
        // Workflow Action Management
        Route::apiResource('actions', WorkflowActionAdminController::class);
    });
