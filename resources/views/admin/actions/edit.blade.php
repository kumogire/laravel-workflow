@extends('workflow::admin.layouts.app')

@section('title', 'Edit Action')

@section('content')
<div class="space-y-6">
    <div>
        <a href="{{ route('workflow-admin.workflows.show', $workflow) }}" class="text-sm text-gray-500 hover:text-gray-700">
            ← Back to {{ $workflow->name }}
        </a>
        <h1 class="mt-2 text-2xl font-bold text-gray-900">Edit Action for Step: {{ $step->title }}</h1>
    </div>

    <form action="{{ route('workflow-admin.actions.update', [$workflow, $step, $action]) }}" method="POST" class="space-y-6">
        @csrf
        @method('PUT')
        
        <div class="bg-white shadow sm:rounded-lg">
            <div class="px-4 py-5 sm:p-6 space-y-6">
                <!-- Action Type -->
                <div>
                    <label for="type" class="block text-sm font-medium leading-6 text-gray-900">Action Type *</label>
                    <div class="mt-2">
                        <select name="type" id="type" required
                                onchange="updateConfigurationFields()"
                                class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                            <option value="">Select action type...</option>
                            <option value="email" {{ old('type', $action->type) == 'email' ? 'selected' : '' }}>Send Email</option>
                            <option value="sms" {{ old('type', $action->type) == 'sms' ? 'selected' : '' }}>Send SMS</option>
                            <option value="webhook" {{ old('type', $action->type) == 'webhook' ? 'selected' : '' }}>Call Webhook</option>
                            <option value="data_save" {{ old('type', $action->type) == 'data_save' ? 'selected' : '' }}>Save Data</option>
                        </select>
                    </div>
                </div>

                <!-- Trigger -->
                <div>
                    <label for="trigger" class="block text-sm font-medium leading-6 text-gray-900">When to Trigger *</label>
                    <div class="mt-2">
                        <select name="trigger" id="trigger" required
                                class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                            <option value="on_step_start" {{ old('trigger', $action->trigger) == 'on_step_start' ? 'selected' : '' }}>When step starts</option>
                            <option value="on_step_complete" {{ old('trigger', $action->trigger) == 'on_step_complete' ? 'selected' : '' }}>When step completes</option>
                        </select>
                    </div>
                </div>

                <!-- Configuration Fields (Dynamic based on type) -->
                <div id="configuration-fields" class="border-t border-gray-200 pt-6">
                    <h3 class="text-base font-semibold leading-6 text-gray-900 mb-4">Action Configuration</h3>
                    
                    <!-- Email Configuration -->
                    <div id="email-config" class="space-y-4 hidden">
                        <div>
                            <label for="email_to" class="block text-sm font-medium leading-6 text-gray-900">To *</label>
                            <div class="mt-2">
                                <input type="text" id="email_to" 
                                       value="{{ $action->type == 'email' ? ($action->configuration['to'] ?? '') : '' }}"
                                       placeholder="{{user.email}} or specific@email.com"
                                       class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                            </div>
                        </div>

                        <div>
                            <label for="email_subject" class="block text-sm font-medium leading-6 text-gray-900">Subject *</label>
                            <div class="mt-2">
                                <input type="text" id="email_subject" 
                                       value="{{ $action->type == 'email' ? ($action->configuration['subject'] ?? '') : '' }}"
                                       placeholder="Welcome to {{workflow.name}}"
                                       class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                            </div>
                        </div>

                        <div>
                            <label for="email_template" class="block text-sm font-medium leading-6 text-gray-900">Template</label>
                            <div class="mt-2">
                                <input type="text" id="email_template" 
                                       value="{{ $action->type == 'email' ? ($action->configuration['template'] ?? '') : '' }}"
                                       placeholder="emails.workflow.notification"
                                       class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                            </div>
                        </div>

                        <div>
                            <label for="email_body" class="block text-sm font-medium leading-6 text-gray-900">Body (if no template)</label>
                            <div class="mt-2">
                                <textarea id="email_body" rows="4"
                                          placeholder="Plain text email content..."
                                          class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">{{ $action->type == 'email' ? ($action->configuration['body'] ?? '') : '' }}</textarea>
                            </div>
                        </div>
                    </div>

                    <!-- SMS Configuration -->
                    <div id="sms-config" class="space-y-4 hidden">
                        <div>
                            <label for="sms_to" class="block text-sm font-medium leading-6 text-gray-900">To *</label>
                            <div class="mt-2">
                                <input type="text" id="sms_to" 
                                       value="{{ $action->type == 'sms' ? ($action->configuration['to'] ?? '') : '' }}"
                                       placeholder="{{user.phone}} or +1234567890"
                                       class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                            </div>
                        </div>

                        <div>
                            <label for="sms_message" class="block text-sm font-medium leading-6 text-gray-900">Message *</label>
                            <div class="mt-2">
                                <textarea id="sms_message" rows="3"
                                          placeholder="Hi {{user.name}}, your next step is ready!"
                                          class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">{{ $action->type == 'sms' ? ($action->configuration['message'] ?? '') : '' }}</textarea>
                            </div>
                        </div>
                    </div>

                    <!-- Webhook Configuration -->
                    <div id="webhook-config" class="space-y-4 hidden">
                        <div>
                            <label for="webhook_url" class="block text-sm font-medium leading-6 text-gray-900">URL *</label>
                            <div class="mt-2">
                                <input type="url" id="webhook_url" 
                                       value="{{ $action->type == 'webhook' ? ($action->configuration['url'] ?? '') : '' }}"
                                       placeholder="https://api.example.com/webhook"
                                       class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                            </div>
                        </div>

                        <div>
                            <label for="webhook_method" class="block text-sm font-medium leading-6 text-gray-900">HTTP Method</label>
                            <div class="mt-2">
                                <select id="webhook_method"
                                        class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                                    <option value="POST" {{ ($action->type == 'webhook' && ($action->configuration['method'] ?? 'POST') == 'POST') ? 'selected' : '' }}>POST</option>
                                    <option value="GET" {{ ($action->type == 'webhook' && ($action->configuration['method'] ?? '') == 'GET') ? 'selected' : '' }}>GET</option>
                                    <option value="PUT" {{ ($action->type == 'webhook' && ($action->configuration['method'] ?? '') == 'PUT') ? 'selected' : '' }}>PUT</option>
                                    <option value="PATCH" {{ ($action->type == 'webhook' && ($action->configuration['method'] ?? '') == 'PATCH') ? 'selected' : '' }}>PATCH</option>
                                </select>
                            </div>
                        </div>

                        <div>
                            <label for="webhook_payload" class="block text-sm font-medium leading-6 text-gray-900">Payload (JSON)</label>
                            <div class="mt-2">
                                <textarea id="webhook_payload" rows="6"
                                          placeholder='{"user_id": "{{user.id}}", "workflow_id": "{{workflow.id}}"}'
                                          class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6 font-mono text-xs">{{ $action->type == 'webhook' ? json_encode($action->configuration['payload'] ?? [], JSON_PRETTY_PRINT) : '' }}</textarea>
                            </div>
                        </div>
                    </div>

                    <!-- Data Save Configuration -->
                    <div id="data-save-config" class="space-y-4 hidden">
                        <div>
                            <label for="data_model" class="block text-sm font-medium leading-6 text-gray-900">Model Class *</label>
                            <div class="mt-2">
                                <input type="text" id="data_model" 
                                       value="{{ $action->type == 'data_save' ? ($action->configuration['model'] ?? '') : '' }}"
                                       placeholder="App\Models\UserProfile"
                                       class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                            </div>
                        </div>

                        <div>
                            <label for="data_attributes" class="block text-sm font-medium leading-6 text-gray-900">Attributes (JSON) *</label>
                            <div class="mt-2">
                                <textarea id="data_attributes" rows="6"
                                          placeholder='{"onboarding_status": "completed", "completed_at": "{{now}}"}'
                                          class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6 font-mono text-xs">{{ $action->type == 'data_save' ? json_encode($action->configuration['attributes'] ?? [], JSON_PRETTY_PRINT) : '' }}</textarea>
                            </div>
                        </div>

                        <div>
                            <label for="data_find_by" class="block text-sm font-medium leading-6 text-gray-900">Find By (JSON, optional)</label>
                            <div class="mt-2">
                                <textarea id="data_find_by" rows="3"
                                          placeholder='{"user_id": "{{user.id}}"}'
                                          class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6 font-mono text-xs">{{ $action->type == 'data_save' ? json_encode($action->configuration['find_by'] ?? [], JSON_PRETTY_PRINT) : '' }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Hidden field to store final configuration JSON -->
                <input type="hidden" name="configuration" id="configuration_json">
            </div>

            <div class="bg-gray-50 px-4 py-3 sm:px-6 flex justify-end gap-x-3">
                <a href="{{ route('workflow-admin.workflows.show', $workflow) }}" 
                   class="rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50">
                    Cancel
                </a>
                <button type="submit" 
                        class="rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">
                    Update Action
                </button>
            </div>
        </div>
    </form>
</div>

@push('scripts')
<script>
function updateConfigurationFields() {
    const type = document.getElementById('type').value;
    
    // Hide all config sections
    document.getElementById('email-config').classList.add('hidden');
    document.getElementById('sms-config').classList.add('hidden');
    document.getElementById('webhook-config').classList.add('hidden');
    document.getElementById('data-save-config').classList.add('hidden');
    
    // Show relevant config section
    if (type === 'email') {
        document.getElementById('email-config').classList.remove('hidden');
    } else if (type === 'sms') {
        document.getElementById('sms-config').classList.remove('hidden');
    } else if (type === 'webhook') {
        document.getElementById('webhook-config').classList.remove('hidden');
    } else if (type === 'data_save') {
        document.getElementById('data-save-config').classList.remove('hidden');
    }
}

// Build configuration JSON before form submission
document.querySelector('form').addEventListener('submit', function(e) {
    const type = document.getElementById('type').value;
    let config = {};
    
    if (type === 'email') {
        config = {
            to: document.getElementById('email_to').value,
            subject: document.getElementById('email_subject').value,
            template: document.getElementById('email_template').value || null,
            body: document.getElementById('email_body').value || null,
        };
    } else if (type === 'sms') {
        config = {
            to: document.getElementById('sms_to').value,
            message: document.getElementById('sms_message').value,
        };
    } else if (type === 'webhook') {
        const payloadText = document.getElementById('webhook_payload').value;
        let payload = {};
        try {
            payload = payloadText ? JSON.parse(payloadText) : {};
        } catch (error) {
            alert('Invalid JSON in webhook payload');
            e.preventDefault();
            return;
        }
        
        config = {
            url: document.getElementById('webhook_url').value,
            method: document.getElementById('webhook_method').value,
            payload: payload,
        };
    } else if (type === 'data_save') {
        const attributesText = document.getElementById('data_attributes').value;
        const findByText = document.getElementById('data_find_by').value;
        
        let attributes = {};
        let findBy = null;
        
        try {
            attributes = JSON.parse(attributesText);
            if (findByText) {
                findBy = JSON.parse(findByText);
            }
        } catch (error) {
            alert('Invalid JSON in data save configuration');
            e.preventDefault();
            return;
        }
        
        config = {
            model: document.getElementById('data_model').value,
            attributes: attributes,
            find_by: findBy,
        };
    }
    
    document.getElementById('configuration_json').value = JSON.stringify(config);
});

// Initialize on page load
updateConfigurationFields();
</script>
@endpush
@endsection