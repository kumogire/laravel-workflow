@extends('workflow::admin.layouts.app')

@section('title', 'Create Step')

@section('content')
<div class="space-y-6">
    <div>
        <a href="{{ route('workflow-admin.workflows.show', $workflow) }}" class="text-sm text-gray-500 hover:text-gray-700">
            ← Back to {{ $workflow->name }}
        </a>
        <h1 class="mt-2 text-2xl font-bold text-gray-900">Add Step to {{ $workflow->name }}</h1>
    </div>

    <form action="{{ route('workflow-admin.steps.store', $workflow) }}" method="POST" class="space-y-6">
        @csrf
        
        <div class="bg-white shadow sm:rounded-lg">
            <div class="px-4 py-5 sm:p-6 space-y-6">
                <!-- Basic Information -->
                <div class="border-b border-gray-200 pb-6">
                    <h3 class="text-base font-semibold leading-6 text-gray-900">Basic Information</h3>
                    <div class="mt-4 space-y-4">
                        <div>
                            <label for="order" class="block text-sm font-medium leading-6 text-gray-900">Order *</label>
                            <div class="mt-2">
                                <input type="number" name="order" id="order" value="{{ old('order', $nextOrder) }}" required min="0"
                                       class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                            </div>
                            <p class="mt-2 text-sm text-gray-500">The sequence number for this step (suggested: {{ $nextOrder }})</p>
                        </div>

                        <div>
                            <label for="title" class="block text-sm font-medium leading-6 text-gray-900">Title *</label>
                            <div class="mt-2">
                                <input type="text" name="title" id="title" value="{{ old('title') }}" required
                                       class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                            </div>
                        </div>

                        <div>
                            <label for="description" class="block text-sm font-medium leading-6 text-gray-900">Description</label>
                            <div class="mt-2">
                                <textarea name="description" id="description" rows="3"
                                          class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">{{ old('description') }}</textarea>
                            </div>
                        </div>

                        <div>
                            <label for="type" class="block text-sm font-medium leading-6 text-gray-900">Type *</label>
                            <div class="mt-2">
                                <select name="type" id="type" required
                                        class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                                    <option value="task" {{ old('type') == 'task' ? 'selected' : '' }}>Task</option>
                                    <option value="form" {{ old('type') == 'form' ? 'selected' : '' }}>Form</option>
                                    <option value="approval" {{ old('type') == 'approval' ? 'selected' : '' }}>Approval</option>
                                    <option value="review" {{ old('type') == 'review' ? 'selected' : '' }}>Review</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Permissions -->
                <div class="border-b border-gray-200 pb-6">
                    <h3 class="text-base font-semibold leading-6 text-gray-900">Permissions</h3>
                    <p class="mt-1 text-sm text-gray-500">Leave empty to allow all authenticated users</p>
                    <div class="mt-4 space-y-4">
                        <div>
                            <label for="can_view_roles" class="block text-sm font-medium leading-6 text-gray-900">Can View Roles</label>
                            <div class="mt-2">
                                <input type="text" name="can_view_roles" id="can_view_roles" value="{{ old('can_view_roles') }}"
                                       placeholder="e.g., employee, manager, admin"
                                       class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                            </div>
                            <p class="mt-2 text-sm text-gray-500">Comma-separated list of roles</p>
                        </div>

                        <div>
                            <label for="can_complete_roles" class="block text-sm font-medium leading-6 text-gray-900">Can Complete Roles</label>
                            <div class="mt-2">
                                <input type="text" name="can_complete_roles" id="can_complete_roles" value="{{ old('can_complete_roles') }}"
                                       placeholder="e.g., manager, admin"
                                       class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                            </div>
                            <p class="mt-2 text-sm text-gray-500">Comma-separated list of roles</p>
                        </div>
                    </div>
                </div>

                <!-- Conditional Logic -->
                <div>
                    <h3 class="text-base font-semibold leading-6 text-gray-900">Conditional Logic (Optional)</h3>
                    <div class="mt-4 space-y-4">
                        <div>
                            <label for="condition_type" class="block text-sm font-medium leading-6 text-gray-900">Condition Type</label>
                            <div class="mt-2">
                                <select name="condition_type" id="condition_type"
                                        class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                                    <option value="always" {{ old('condition_type') == 'always' ? 'selected' : '' }}>Always Execute</option>
                                    <option value="if_data_equals" {{ old('condition_type') == 'if_data_equals' ? 'selected' : '' }}>If Data Equals</option>
                                    <option value="if_data_contains" {{ old('condition_type') == 'if_data_contains' ? 'selected' : '' }}>If Data Contains</option>
                                    <option value="if_role" {{ old('condition_type') == 'if_role' ? 'selected' : '' }}>If User Has Role</option>
                                </select>
                            </div>
                        </div>

                        <div class="flex items-start">
                            <div class="flex h-6 items-center">
                                <input type="checkbox" name="skip_if_condition_false" id="skip_if_condition_false" value="1" {{ old('skip_if_condition_false') ? 'checked' : '' }}
                                       class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-600">
                            </div>
                            <div class="ml-3 text-sm leading-6">
                                <label for="skip_if_condition_false" class="font-medium text-gray-900">Skip if condition fails</label>
                                <p class="text-gray-500">If checked, this step will be skipped when the condition is not met</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-gray-50 px-4 py-3 sm:px-6 flex justify-end gap-x-3">
                <a href="{{ route('workflow-admin.workflows.show', $workflow) }}" 
                   class="rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50">
                    Cancel
                </a>
                <button type="submit" 
                        class="rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">
                    Create Step
                </button>
            </div>
        </div>
    </form>
</div>

@push('scripts')
<script>
// Convert comma-separated strings to arrays before form submission
document.querySelector('form').addEventListener('submit', function(e) {
    const canViewRoles = document.getElementById('can_view_roles');
    const canCompleteRoles = document.getElementById('can_complete_roles');
    
    // Convert to hidden array inputs
    if (canViewRoles.value) {
        const roles = canViewRoles.value.split(',').map(r => r.trim()).filter(r => r);
        canViewRoles.name = '';
        roles.forEach((role, index) => {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = `can_view_roles[${index}]`;
            input.value = role;
            this.appendChild(input);
        });
    }
    
    if (canCompleteRoles.value) {
        const roles = canCompleteRoles.value.split(',').map(r => r.trim()).filter(r => r);
        canCompleteRoles.name = '';
        roles.forEach((role, index) => {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = `can_complete_roles[${index}]`;
            input.value = role;
            this.appendChild(input);
        });
    }
});
</script>
@endpush
@endsection