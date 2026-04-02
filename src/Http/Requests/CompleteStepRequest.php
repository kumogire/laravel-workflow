<?php

namespace Kumogire\Workflow\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CompleteStepRequest extends FormRequest
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
            'data' => 'sometimes|array',
            'step_id' => 'sometimes|exists:workflow_steps,id',
            'comment' => 'nullable|string',
            'payload' => 'sometimes|array',
        ];
    }
}
