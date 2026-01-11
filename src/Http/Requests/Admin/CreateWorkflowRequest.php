<?php

namespace Kumogire\Workflow\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class CreateWorkflowRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Authorization handled via middleware
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|string|max:255',
            'is_active' => 'boolean',
        ];
    }
}
