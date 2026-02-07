<div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-3">
    <div class="overflow-hidden rounded-lg bg-white px-4 py-5 shadow sm:p-6">
        <dt class="truncate text-sm font-medium text-gray-500">Total Workflows</dt>
        <dd class="mt-1 text-3xl font-semibold tracking-tight text-gray-900">{{ $stats['total_workflows'] }}</dd>
    </div>

    <div class="overflow-hidden rounded-lg bg-white px-4 py-5 shadow sm:p-6">
        <dt class="truncate text-sm font-medium text-gray-500">Active Workflows</dt>
        <dd class="mt-1 text-3xl font-semibold tracking-tight text-gray-900">{{ $stats['active_workflows'] }}</dd>
    </div>

    <div class="overflow-hidden rounded-lg bg-white px-4 py-5 shadow sm:p-6">
        <dt class="truncate text-sm font-medium text-gray-500">Total Instances</dt>
        <dd class="mt-1 text-3xl font-semibold tracking-tight text-gray-900">{{ $stats['total_instances'] }}</dd>
    </div>

    <div class="overflow-hidden rounded-lg bg-white px-4 py-5 shadow sm:p-6">
        <dt class="truncate text-sm font-medium text-gray-500">In Progress</dt>
        <dd class="mt-1 text-3xl font-semibold tracking-tight text-indigo-600">{{ $stats['in_progress_instances'] }}</dd>
    </div>

    <div class="overflow-hidden rounded-lg bg-white px-4 py-5 shadow sm:p-6">
        <dt class="truncate text-sm font-medium text-gray-500">Completed</dt>
        <dd class="mt-1 text-3xl font-semibold tracking-tight text-green-600">{{ $stats['completed_instances'] }}</dd>
    </div>

    <div class="overflow-hidden rounded-lg bg-white px-4 py-5 shadow sm:p-6">
        <dt class="truncate text-sm font-medium text-gray-500">Paused</dt>
        <dd class="mt-1 text-3xl font-semibold tracking-tight text-yellow-600">{{ $stats['paused_instances'] }}</dd>
    </div>
</div>