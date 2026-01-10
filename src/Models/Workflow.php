<?php

namespace Kumogire\Workflow\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Cache;

class Workflow extends Model
{
    // Allow users to override this model
    public static function getModel()
    {
        return config('workflow.models.workflow', static::class);
    }

    protected $fillable = [
        'name',
        'description',
        'type',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Get the steps for this workflow
     */
    public function steps(): HasMany
    {
        return $this->hasMany(WorkflowStep::class)->orderBy('order');
    }

    /**
     * Get all instances of this workflow
     */
    public function instances(): HasMany
    {
        return $this->hasMany(WorkflowInstance::class);
    }

    /**
     * Get workflows this workflow depends on
     */
    public function dependencies(): HasMany
    {
        return $this->hasMany(WorkflowDependency::class);
    }

    /**
     * Get workflows that depend on this workflow
     */
    public function dependents(): HasMany
    {
        return $this->hasMany(WorkflowDependency::class, 'depends_on_workflow_id');
    }

    /**
     * Scope to get only active workflows
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to filter by type
     */
    public function scopeOfType($query, string $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Get the first step of the workflow
     */
    public function firstStep(): ?WorkflowStep
    {
        return $this->steps()->orderBy('order')->first();
    }

    /**
     * Clear workflow cache
     */
    public function clearCache(): void
    {
        if (config('workflow.cache_workflows')) {
            Cache::forget("workflow.{$this->id}");
            Cache::forget("workflow.{$this->id}.steps");
        }
    }

    /**
     * Boot method to handle model events
     */
    protected static function boot()
    {
        parent::boot();

        static::saved(function ($workflow) {
            $workflow->clearCache();
        });

        static::deleted(function ($workflow) {
            $workflow->clearCache();
        });
    }
}
