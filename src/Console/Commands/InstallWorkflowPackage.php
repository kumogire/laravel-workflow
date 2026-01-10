<?php

namespace Kumogire\Workflow\Console\Commands;

use Illuminate\Console\Command;

class InstallWorkflowPackage extends Command
{
    protected $signature = 'workflow:install';
    protected $description = 'Install the Laravel Workflow package';

    public function handle()
    {
        $this->info('Installing Laravel Workflow...');

        // Publish config
        $this->call('vendor:publish', [
            '--tag' => 'workflow-config',
        ]);

        // Publish migrations
        $this->call('vendor:publish', [
            '--tag' => 'workflow-migrations',
        ]);

        // Run migrations
        if ($this->confirm('Run migrations now?', true)) {
            $this->call('migrate');
        }

        $this->info('Laravel Workflow installed successfully!');
        $this->info('You can customize the configuration in config/workflow.php');
    }
}