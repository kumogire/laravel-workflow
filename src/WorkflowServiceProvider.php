<?php

namespace Kumogire\Workflow;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Event;
use Kumogire\Workflow\Http\Middleware\AdminMiddleware;
use Kumogire\Workflow\Services\WorkflowService;
use Kumogire\Workflow\Services\WorkflowStateMachine;
use Kumogire\Workflow\Actions\ActionHandlerFactory;
use Kumogire\Workflow\Console\Commands\InstallWorkflowPackage;
use Kumogire\Workflow\Events\StepStarted;
use Kumogire\Workflow\Events\StepCompleted;
use Kumogire\Workflow\Listeners\ExecuteWorkflowActions;

class WorkflowServiceProvider extends ServiceProvider
{
    public function register()
    {
        // Merge package config with app config
        $this->mergeConfigFrom(
            __DIR__.'/../config/workflow.php', 'workflow'
        );

        // Register services
        $this->app->singleton(WorkflowStateMachine::class);
        $this->app->singleton(ActionHandlerFactory::class);
        
        $this->app->singleton(WorkflowService::class, function ($app) {
            return new WorkflowService(
                $app->make(WorkflowStateMachine::class)
            );
        });

        // Register facade alias
        $this->app->alias(WorkflowService::class, 'workflow');
    }

    public function boot()
    {
        // Publish config
        $this->publishes([
            __DIR__.'/../config/workflow.php' => config_path('workflow.php'),
        ], 'workflow-config');

        // Publish migrations
        $this->publishes([
            __DIR__.'/../database/migrations' => database_path('migrations'),
        ], 'workflow-migrations');

        // Publish views
        $this->publishes([
            __DIR__.'/../resources/views' => resource_path('views/vendor/workflow'),
        ], 'workflow-views');

        // Load migrations
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');

        // Register middleware alias
        $this->app['router']->aliasMiddleware('admin', AdminMiddleware::class);

        // Load routes
        $this->loadRoutesFrom(__DIR__.'/../routes/api.php');
        
        if (config('workflow.enable_admin_routes', true)) {
            $this->loadRoutesFrom(__DIR__.'/../routes/admin.php');
        }

        if (config('workflow.routes.enable_admin_web', true)) {
            $this->loadRoutesFrom(__DIR__.'/../routes/web.php');
        }

        // Load views
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'workflow');

        // Register Blade components
        $this->loadViewComponentsAs('workflow', [
            \Kumogire\Workflow\View\Components\WorkflowList::class,
            \Kumogire\Workflow\View\Components\WorkflowForm::class,
            \Kumogire\Workflow\View\Components\WorkflowSteps::class,
            \Kumogire\Workflow\View\Components\RecentWorkflows::class,
            \Kumogire\Workflow\View\Components\WorkflowStats::class,
            \Kumogire\Workflow\View\Components\UserWorkflows::class,
        ]);

        // Register commands
        if ($this->app->runningInConsole()) {
            $this->commands([
                InstallWorkflowPackage::class,
            ]);
        }

        // Register event listeners
        $this->registerEventListeners();
    }

    protected function registerEventListeners()
    {
        Event::listen(StepStarted::class, [ExecuteWorkflowActions::class, 'handleStepStarted']);
        Event::listen(StepCompleted::class, [ExecuteWorkflowActions::class, 'handleStepCompleted']);
    }
}