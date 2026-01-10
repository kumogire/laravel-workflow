<?php

namespace Kumogire\Workflow\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class WorkflowStep extends Model
{
    protected $fillable = [
        'workflow_id',
        'order',
        'title',
        'description',
        'type',
        'configuration',
        'condition_type',
        'condition_config',
        'skip_if_condition_false',
        'can_view_roles',
        'can_complete_roles',
    ];

    protected $casts = [
        'configuration' => 'array',
        'condition_config' => 'array',
        'can_view_roles' => 'array',
        'can_complete_roles' => 'array',
        'skip_if_condition_false' => 'boolean',
    ];

    /**
     * Get the workflow this step belongs to
     */
    public function workflow(): BelongsTo
    {
        return $this->belongsTo(Workflow::class);
    }

    /**
     * Get all actions for this step
     */
    public function actions(): HasMany
    {
        return $this->hasMany(WorkflowAction::class);
    }

    /**
     * Get instance steps for this workflow step
     */
    public function instanceSteps(): HasMany
    {
        return $this->hasMany(WorkflowInstanceStep::class);
    }

    /**
     * Get actions that trigger on step start
     */
    public function onStartActions(): HasMany
    {
        return $this->actions()->where('trigger', 'on_step_start');
    }

    /**
     * Get actions that trigger on step complete
     */
    public function onCompleteActions(): HasMany
    {
        return $this->actions()->where('trigger', 'on_step_complete');
    }

    /**
     * Get the next step in the workflow
     */
    public function nextStep(): ?WorkflowStep
    {
        return WorkflowStep::where('workflow_id', $this->workflow_id)
            ->where('order', '>', $this->order)
            ->orderBy('order')
            ->first();
    }

    /**
     * Get the previous step in the workflow
     */
    public function previousStep(): ?WorkflowStep
    {
        return WorkflowStep::where('workflow_id', $this->workflow_id)
            ->where('order', '<', $this->order)
            ->orderBy('order', 'desc')
            ->first();
    }

    /**
     * Check if user can view this step
     */
    public function canView($user): bool
    {
        if (empty($this->can_view_roles)) {
            return true;
        }

        return $this->hasAnyRole($user, $this->can_view_roles);
    }

    /**
     * Check if user can complete this step
     */
    public function canComplete($user): bool
    {
        if (empty($this->can_complete_roles)) {
            return true;
        }

        return $this->hasAnyRole($user, $this->can_complete_roles);
    }

    /**
     * Check if user has any of the specified roles
     */
    protected function hasAnyRole($user, array $roles): bool
    {
        $roleField = config('workflow.role_field', 'role');
        $userRole = $user->{$roleField};

        if (is_array($userRole)) {
            return !empty(array_intersect($userRole, $roles));
        }

        return in_array($userRole, $roles);
    }
}
