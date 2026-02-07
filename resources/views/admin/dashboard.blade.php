@extends('workflow::admin.layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="space-y-6">
    <!-- Stats -->
    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
        <div class="overflow-hidden rounded-lg bg-white px-4 py-5 shadow sm:p-6">
            <dt class="truncate text-sm font-medium text-gray-500">Total Workflows</dt>
            <dd class="mt-1 text-3xl font-semibold tracking-tight text-gray-900">{{ $stats['total_workflows'] }}</dd>
        </div>
        <div class="overflow-hidden rounded-lg bg-white px-4 py-5 shadow sm:p-6">
            <dt class="truncate text-sm font-medium text-gray-500">Active Workflows</dt>
            <dd class="mt-1 text-3xl font-semibold tracking-tight text-gray-900">{{ $stats['active_workflows'] }}</dd>
        </div>
        <div class="overflow-hidden rounded-lg bg-white px-4 py-5 shadow sm:p-6">
            <dt class="truncate text-sm font-medium text-gray-500">In Progress</dt>
            <dd class="mt-1 text-3xl font-semibold tracking-tight text-gray-900">{{ $stats['in_progress_instances'] }}</dd>
        </div>
    </div>

    <!-- Recent Workflows -->
    <div class="bg-white shadow sm:rounded-lg">
        <div class="px-4 py-5 sm:px-6 flex justify-between items-center">
            <h3 class="text-base font-semibold leading-6 text-gray-900">Recent Workflows</h3>
            <a href="{{ route('workflow-admin.workflows.create') }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-500">
                Create New →
            </a>
        </div>
        <div class="border-t border-gray-200">
            <ul role="list" class="divide-y divide-gray-200">
                @forelse($recentWorkflows as $workflow)
                    <li class="px-4 py-4 sm:px-6 hover:bg-gray-50">
                        <a href="{{ route('workflow-admin.workflows.show', $workflow) }}" class="block">
                            <div class="flex items-center justify-between">
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-gray-900 truncate">{{ $workflow->name }}</p>
                                    <p class="text-sm text-gray-500">{{ $workflow->type }}</p>
                                </div>
                                <div class="ml-2 flex-shrink-0">
                                    @if($workflow->is_active)
                                        <span class="inline-flex rounded-full bg-green-100 px-2 text-xs font-semibold leading-5 text-green-800">Active</span>
                                    @else
                                        <span class="inline-flex rounded-full bg-gray-100 px-2 text-xs font-semibold leading-5 text-gray-800">Inactive</span>
                                    @endif
                                </div>
                            </div>
                        </a>
                    </li>
                @empty
                    <li class="px-4 py-4 sm:px-6 text-center text-gray-500">
                        No workflows yet. <a href="{{ route('workflow-admin.workflows.create') }}" class="text-indigo-600 hover:text-indigo-500">Create one</a>
                    </li>
                @endforelse
            </ul>
        </div>
    </div>
</div>
@endsection