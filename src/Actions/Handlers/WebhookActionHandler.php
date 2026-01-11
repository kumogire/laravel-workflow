<?php

namespace Kumogire\Workflow\Actions\Handlers;

use Kumogire\Workflow\Contracts\ActionHandler;
use Kumogire\Workflow\Models\WorkflowAction;
use Kumogire\Workflow\Models\WorkflowInstance;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WebhookActionHandler implements ActionHandler
{
    public function handle(WorkflowAction $action, WorkflowInstance $instance): void
    {
        $config = $action->configuration;

        try {
            $url = $config['url'] ?? null;
            $method = strtoupper($config['method'] ?? 'POST');
            $payload = $config['payload'] ?? [];
            $headers = $config['headers'] ?? [];

            if (!$url) {
                throw new \Exception("Webhook URL is required");
            }

            // Interpolate payload values
            $interpolatedPayload = $this->interpolateArray($payload, $instance);

            // Make HTTP request
            $response = Http::withHeaders($headers)
                ->timeout(30);

            switch ($method) {
                case 'POST':
                    $response = $response->post($url, $interpolatedPayload);
                    break;
                case 'PUT':
                    $response = $response->put($url, $interpolatedPayload);
                    break;
                case 'PATCH':
                    $response = $response->patch($url, $interpolatedPayload);
                    break;
                case 'DELETE':
                    $response = $response->delete($url, $interpolatedPayload);
                    break;
                default:
                    $response = $response->get($url, $interpolatedPayload);
            }

            if ($response->successful()) {
                Log::info("Webhook called successfully", [
                    'action_id' => $action->id,
                    'instance_id' => $instance->id,
                    'url' => $url,
                    'status' => $response->status(),
                ]);
            } else {
                Log::warning("Webhook returned non-successful status", [
                    'action_id' => $action->id,
                    'instance_id' => $instance->id,
                    'url' => $url,
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
            }
        } catch (\Exception $e) {
            Log::error("Failed to call webhook for workflow action", [
                'action_id' => $action->id,
                'instance_id' => $instance->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Recursively interpolate array values
     */
    protected function interpolateArray(array $data, WorkflowInstance $instance): array
    {
        $result = [];

        foreach ($data as $key => $value) {
            if (is_array($value)) {
                $result[$key] = $this->interpolateArray($value, $instance);
            } elseif (is_string($value)) {
                $result[$key] = $this->interpolate($value, $instance);
            } else {
                $result[$key] = $value;
            }
        }

        return $result;
    }

    /**
     * Interpolate template variables
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

            if ($parts[0] === 'workflow' && isset($parts[1])) {
                return $instance->workflow->{$parts[1]} ?? '';
            }

            return $matches[0];
        }, $template);
    }
}
