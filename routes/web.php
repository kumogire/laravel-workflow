<?php

use Illuminate\Support\Facades\Route;
use Kumogire\Workflow\Http\Controllers\Admin\DashboardController;
use Kumogire\Workflow\Http\Controllers\Admin\WorkflowViewController;
use Kumogire\Workflow\Http\Controllers\Admin\WorkflowStepViewController;
use Kumogire\Workflow\Http\Controllers\Admin\WorkflowActionViewController;

Route::prefix(config('workflow.routes.admin_web_prefix', 'workflow-admin'))
    ->middleware(config('workflow.routes.admin_web_middleware', ['web', 'auth']))
    ->name('workflow-admin.')
    ->group(function () {
        
        // Dashboard
        Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
        
        // Workflows
        Route::resource('workflows', WorkflowViewController::class);
        
        // Steps (nested under workflows)
        Route::prefix('workflows/{workflow}')->group(function () {
            Route::get('steps/create', [WorkflowStepViewController::class, 'create'])
                ->name('steps.create');
            Route::post('steps', [WorkflowStepViewController::class, 'store'])
                ->name('steps.store');
            Route::get('steps/{step}/edit', [WorkflowStepViewController::class, 'edit'])
                ->name('steps.edit');
            Route::put('steps/{step}', [WorkflowStepViewController::class, 'update'])
                ->name('steps.update');
            Route::delete('steps/{step}', [WorkflowStepViewController::class, 'destroy'])
                ->name('steps.destroy');
            
            // Actions (nested under steps)
            Route::prefix('steps/{step}')->group(function () {
                Route::get('actions/create', [WorkflowActionViewController::class, 'create'])
                    ->name('actions.create');
                Route::post('actions', [WorkflowActionViewController::class, 'store'])
                    ->name('actions.store');
                Route::get('actions/{action}/edit', [WorkflowActionViewController::class, 'edit'])
                    ->name('actions.edit');
                Route::put('actions/{action}', [WorkflowActionViewController::class, 'update'])
                    ->name('actions.update');
                Route::delete('actions/{action}', [WorkflowActionViewController::class, 'destroy'])
                    ->name('actions.destroy');
            });
        });
    });