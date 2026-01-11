<?php

namespace Kumogire\Workflow\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateWorkflowStepRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'order' => 'sometimes|integer|min:0',
            'title' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'type' => 'sometimes|string|max:255',
            'configuration' => 'nullable|array',
            'condition_type' => 'sometimes|string|in:always,if_data_equals,if_data_contains,if_role',
            'condition_config' => 'nullable|array',
            'skip_if_condition_false' => 'boolean',
            'can_view_roles' => 'nullable|array',
            'can_complete_roles' => 'nullable|array',
        ];
    }
}
