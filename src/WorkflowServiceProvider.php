<?php

namespace Kumogire\Workflow;

use Illuminate\Support\ServiceProvider;
use Kumogire\Workflow\Services\WorkflowService;
use Kumogire\Workflow\Console\Commands\InstallWorkflowPackage;

class WorkflowServiceProvider extends ServiceProvider
{
    public function register()
    {
        // Merge package config with app config
        $this->mergeConfigFrom(
            __DIR__.'/../config/workflow.php', 'workflow'
        );

        // Register the main service
        $this->app->singleton(WorkflowService::class, function ($app) {
            return new WorkflowService();
        });
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

        // Publish views (if you include admin UI)
        $this->publishes([
            __DIR__.'/../resources/views' => resource_path('views/vendor/workflow'),
        ], 'workflow-views');

        // Load migrations (optional - allows running without publishing)
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');

        // Load routes
        $this->loadRoutesFrom(__DIR__.'/../routes/api.php');
        
        if (config('workflow.enable_admin_routes', true)) {
            $this->loadRoutesFrom(__DIR__.'/../routes/admin.php');
        }

        // Load views
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'workflow');

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
        $events = $this->app['events'];

        // Register your event listeners here
        // Maybe EventServiceProvider pattern
    }
}