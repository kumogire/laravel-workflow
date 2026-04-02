<?php

namespace Kumogire\Workflow\Tests;

use Orchestra\Testbench\TestCase as Orchestra;
use Kumogire\Workflow\WorkflowServiceProvider;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Kumogire\Workflow\Tests\Models\User;

abstract class TestCase extends Orchestra
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Load package migrations
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');
        
        // Load test-only migrations (users table)
        $this->loadMigrationsFrom(__DIR__ . '/database/migrations');
    }

    protected function getPackageProviders($app)
    {
        return [
            WorkflowServiceProvider::class,
            \Laravel\Sanctum\SanctumServiceProvider::class,
        ];
    }

    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('database.default', 'testbench');
        $app['config']->set('database.connections.testbench', [
            'driver'   => 'sqlite',
            'database' => ':memory:',
            'prefix'   => '',
        ]);
        // Add Sanctum configuration
        $app['config']->set('auth.guards.sanctum', [
            'driver' => 'sanctum',
            'provider' => 'users',
        ]);
        $app['config']->set('auth.providers.users', [
            'driver' => 'eloquent',
            'model' => \Kumogire\Workflow\Tests\Models\User::class,
        ]);
        $app['config']->set('app.key', 'base64:'.base64_encode(str_repeat('x', 32)));
        $app['config']->set('workflow.routes.admin_middleware', ['api', 'auth:sanctum', 'admin']);
        $app['config']->set('workflow.queue_actions', false);
    }

    protected function createUser(array $attributes = [])
    {
        return User::create(array_merge([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
            'role' => 'employee',
        ], $attributes));
    }
}
