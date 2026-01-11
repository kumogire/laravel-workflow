<?php

namespace Kumogire\Workflow\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class CreateWorkflowActionRequest extends FormRequest
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
            'workflow_step_id' => 'required|exists:workflow_steps,id',
            'type' => 'required|string|in:email,sms,webhook,data_save',
            'trigger' => 'required|string|in:on_step_start,on_step_complete',
            'configuration' => 'required|array',
        ];
    }
}
