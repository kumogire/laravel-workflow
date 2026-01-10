# Laravel Workflow

A flexible, database-driven workflow system for Laravel applications.

## Installation
```bash
composer require kumogire/laravel-workflow
```

## Quick Start
```bash
php artisan workflow:install
```

## Configuration

Publish configuration:
```bash
php artisan vendor:publish --tag=workflow-config
```

## Usage

### Creating a Workflow
```php
use Kumogire\Workflow\Models\Workflow;

$workflow = Workflow::create([
    'name' => 'Employee Onboarding',
    'type' => 'onboarding',
    'is_active' => true,
]);
```

### Starting a Workflow Instance
```php
use Kumogire\Workflow\Services\WorkflowService;

$service = app(WorkflowService::class);
$instance = $service->startWorkflow($workflow, $user);
```

## Customization

### Custom Action Handlers

Create a class implementing `ActionHandler`:
```php
namespace App\Workflow\Actions;

use Kumogire\Workflow\Contracts\ActionHandler;

class SlackNotificationHandler implements ActionHandler
{
    public function handle($action, $instance): void
    {
        // Send Slack notification
    }
}
```

Register in `config/workflow.php`:
```php
'action_handlers' => [
    'slack' => \App\Workflow\Actions\SlackNotificationHandler::class,
],
```

## License

MIT