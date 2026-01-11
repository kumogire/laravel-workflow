<?php

namespace Kumogire\Workflow\Actions\Handlers;

use Kumogire\Workflow\Contracts\ActionHandler;
use Kumogire\Workflow\Models\WorkflowAction;
use Kumogire\Workflow\Models\WorkflowInstance;
use Illuminate\Support\Facades\Log;

class SmsActionHandler implements ActionHandler
{
    public function handle(WorkflowAction $action, WorkflowInstance $instance): void
    {
        $config = $action->configuration;
        $user = $instance->user;

        try {
            $to = $this->interpolate($config['to'] ?? '', $instance);
            $message = $this->interpolate($config['message'] ?? '', $instance);

            // This is a placeholder - integrate with your SMS provider
            // Examples: Twilio, Vonage (Nexmo), AWS SNS, etc.
            
            // Example with Twilio:
            // $twilio = app(\Twilio\Rest\Client::class);
            // $twilio->messages->create($to, [
            //     'from' => config('services.twilio.from'),
            //     'body' => $message
            // ]);

            Log::info("SMS would be sent for workflow action", [
                'action_id' => $action->id,
                'instance_id' => $instance->id,
                'to' => $to,
                'message' => $message,
            ]);
        } catch (\Exception $e) {
            Log::error("Failed to send SMS for workflow action", [
                'action_id' => $action->id,
                'instance_id' => $instance->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Interpolate template variables like {{user.phone}}
     */
    protected function interpolate(string $template, WorkflowInstance $instance): string
    {
        $user = $instance->user;

        return preg_replace_callback('/\{\{([^}]+)\}\}/', function ($matches) use ($user, $instance) {
            $path = trim($matches[1]);
            $parts = explode('.', $path);

            if ($parts[0] === 'user' && isset($parts[1])) {
                return $user->{$parts[1]} ?? '';
            }

            if ($parts[0] === 'instance' && isset($parts[1])) {
                return $instance->{$parts[1]} ?? '';
            }

            return $matches[0];
        }, $template);
    }
}
