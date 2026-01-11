<?php

namespace Kumogire\Workflow\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StartWorkflowRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Authorization handled in controller
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'workflow_id' => 'required|exists:workflows,id',
            'metadata' => 'sometimes|array',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'workflow_id.required' => 'A workflow ID is required.',
            'workflow_id.exists' => 'The selected workflow does not exist.',
        ];
    }
}
