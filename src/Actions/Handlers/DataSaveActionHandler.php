<?php

namespace Kumogire\Workflow\Actions\Handlers;

use Kumogire\Workflow\Contracts\ActionHandler;
use Kumogire\Workflow\Models\WorkflowAction;
use Kumogire\Workflow\Models\WorkflowInstance;
use Illuminate\Support\Facades\Log;

class DataSaveActionHandler implements ActionHandler
{
    public function handle(WorkflowAction $action, WorkflowInstance $instance): void
    {
        $config = $action->configuration;

        try {
            $modelClass = $config['model'] ?? null;
            $attributes = $config['attributes'] ?? [];
            $findBy = $config['find_by'] ?? null;

            if (!$modelClass) {
                throw new \Exception("Model class is required");
            }

            if (!class_exists($modelClass)) {
                throw new \Exception("Model class not found: {$modelClass}");
            }

            // Interpolate attribute values
            $interpolatedAttributes = [];
            foreach ($attributes as $key => $value) {
                if (is_string($value)) {
                    $interpolatedAttributes[$key] = $this->interpolate($value, $instance);
                } else {
                    $interpolatedAttributes[$key] = $value;
                }
            }

            // Find existing record or create new
            if ($findBy && is_array($findBy)) {
                $query = $modelClass::query();
                foreach ($findBy as $field => $value) {
                    $interpolatedValue = is_string($value) ? $this->interpolate($value, $instance) : $value;
                    $query->where($field, $interpolatedValue);
                }

                $model = $query->first();

                if ($model) {
                    $model->update($interpolatedAttributes);
                } else {
                    $modelClass::create(array_merge($findBy, $interpolatedAttributes));
                }
            } else {
                $modelClass::create($interpolatedAttributes);
            }

            Log::info("Data saved for workflow action", [
                'action_id' => $action->id,
                'instance_id' => $instance->id,
                'model' => $modelClass,
            ]);
        } catch (\Exception $e) {
            Log::error("Failed to save data for workflow action", [
                'action_id' => $action->id,
                'instance_id' => $instance->id,
                'error' => $e->getMessage(),
            ]);
        }
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

            return $matches[0];
        }, $template);
    }
}
