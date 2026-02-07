@extends('workflow::admin.layouts.app')

@section('title', 'Workflows')

@section('content')
<div class="space-y-6">
    <div class="sm:flex sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Workflows</h1>
            <p class="mt-2 text-sm text-gray-700">Manage your workflow definitions</p>
        </div>
        <div class="mt-4 sm:mt-0">
            <a href="{{ route('workflow-admin.workflows.create') }}" 
               class="inline-flex items-center justify-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500">
                Create Workflow
            </a>
        </div>
    </div>

    <div class="bg-white shadow sm:rounded-lg">
        <ul role="list" class="divide-y divide-gray-200">
            @forelse($workflows as $workflow)
                <li class="px-4 py-4 sm:px-6 hover:bg-gray-50">
                    <div class="flex items-center justify-between">
                        <div class="flex-1 min-w-0">
                            <a href="{{ route('workflow-admin.workflows.show', $workflow) }}" class="block">
                                <p class="text-sm font-medium text-indigo-600 truncate">{{ $workflow->name }}</p>
                                <p class="text-sm text-gray-500">{{ $workflow->description }}</p>
                                <div class="mt-2 flex items-center gap-x-4 text-xs">
                                    <span class="text-gray-500">Type: {{ $workflow->type }}</span>
                                    <span class="text-gray-500">{{ $workflow->steps_count }} steps</span>
                                    <span class="text-gray-500">Created {{ $workflow->created_at->diffForHumans() }}</span>
                                </div>
                            </a>
                        </div>
                        <div class="ml-4 flex items-center gap-x-4">
                            @if($workflow->is_active)
                                <span class="inline-flex rounded-full bg-green-100 px-2 text-xs font-semibold leading-5 text-green-800">Active</span>
                            @else
                                <span class="inline-flex rounded-full bg-gray-100 px-2 text-xs font-semibold leading-5 text-gray-800">Inactive</span>
                            @endif
                            <div class="flex gap-x-2">
                                <a href="{{ route('workflow-admin.workflows.edit', $workflow) }}" class="text-indigo-600 hover:text-indigo-900 text-sm font-medium">
                                    Edit
                                </a>
                                <form action="{{ route('workflow-admin.workflows.destroy', $workflow) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this workflow?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-900 text-sm font-medium">
                                        Delete
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </li>
            @empty
                <li class="px-4 py-12 text-center">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                    </svg>
                    <h3 class="mt-2 text-sm font-semibold text-gray-900">No workflows</h3>
                    <p class="mt-1 text-sm text-gray-500">Get started by creating a new workflow.</p>
                    <div class="mt-6">
                        <a href="{{ route('workflow-admin.workflows.create') }}" class="inline-flex items-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500">
                            Create Workflow
                        </a>
                    </div>
                </li>
            @endforelse
        </ul>
    </div>

    @if($workflows->hasPages())
        <div class="bg-white px-4 py-3 sm:px-6">
            {{ $workflows->links() }}
        </div>
    @endif
</div>
@endsection