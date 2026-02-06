<?php

namespace Kumogire\Workflow\Tests;

use Orchestra\Testbench\TestCase as Orchestra;
use Kumogire\Workflow\WorkflowServiceProvider;
use Illuminate\Foundation\Testing\RefreshDatabase;

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

        // Point to test User model
        $app['config']->set('workflow.user_model', \Kumogire\Workflow\Tests\User::class);
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