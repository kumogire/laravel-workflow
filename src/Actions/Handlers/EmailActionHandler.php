<?php

namespace Kumogire\Workflow\Actions\Handlers;

use Kumogire\Workflow\Contracts\ActionHandler;
use Kumogire\Workflow\Models\WorkflowAction;
use Kumogire\Workflow\Models\WorkflowInstance;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class EmailActionHandler implements ActionHandler
{
    public function handle(WorkflowAction $action, WorkflowInstance $instance): void
    {
        $config = $action->configuration;
        $user = $instance->user;

        try {
            $to = $this->interpolate($config['to'] ?? '', $instance);
            $subject = $this->interpolate($config['subject'] ?? 'Workflow Notification', $instance);
            $template = $config['template'] ?? null;
            $data = $config['data'] ?? [];

            // Interpolate data values
            $interpolatedData = [];
            foreach ($data as $key => $value) {
                $interpolatedData[$key] = $this->interpolate($value, $instance);
            }

            if ($template) {
                // Send using a mailable or view
                Mail::send($template, array_merge($interpolatedData, [
                    'instance' => $instance,
                    'user' => $user,
                ]), function ($message) use ($to, $subject) {
                    $message->to($to)
                            ->subject($subject);
                });
            } else {
                // Send plain text email
                $body = $config['body'] ?? '';
                Mail::raw($this->interpolate($body, $instance), function ($message) use ($to, $subject) {
                    $message->to($to)
                            ->subject($subject);
                });
            }

            Log::info("Email sent for workflow action", [
                'action_id' => $action->id,
                'instance_id' => $instance->id,
                'to' => $to,
            ]);
        } catch (\Exception $e) {
            Log::error("Failed to send email for workflow action", [
                'action_id' => $action->id,
                'instance_id' => $instance->id,
                'error' => $e->getMessage(),
            ]);

            // Don't throw exception to avoid blocking workflow progression
        }
    }

    /**
     * Interpolate template variables like {{user.email}}
     */
    protected function interpolate(string $template, WorkflowInstance $instance): string
    {
        $user = $instance->user;

        return preg_replace_callback('/\{\{([^}]+)\}\}/', function ($matches) use ($user, $instance) {
            $path = trim($matches[1]);
            $parts = explode('.', $path);

            // Handle user.field
            if ($parts[0] === 'user' && isset($parts[1])) {
                return $user->{$parts[1]} ?? '';
            }

            // Handle instance.field
            if ($parts[0] === 'instance' && isset($parts[1])) {
                return $instance->{$parts[1]} ?? '';
            }

            return $matches[0];
        }, $template);
    }
}
