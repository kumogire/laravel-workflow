@extends('workflow::admin.layouts.app')

@section('title', $workflow->name)

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="md:flex md:items-center md:justify-between">
        <div class="min-w-0 flex-1">
            <div class="flex items-center">
                <a href="{{ route('workflow-admin.workflows.index') }}" class="text-sm text-gray-500 hover:text-gray-700">
                    ← Back to Workflows
                </a>
            </div>
            <h1 class="mt-2 text-2xl font-bold leading-7 text-gray-900 sm:truncate sm:text-3xl sm:tracking-tight">
                {{ $workflow->name }}
            </h1>
            <div class="mt-1 flex flex-col sm:mt-0 sm:flex-row sm:flex-wrap sm:space-x-6">
                <div class="mt-2 flex items-center text-sm text-gray-500">
                    Type: {{ $workflow->type }}
                </div>
                <div class="mt-2 flex items-center text-sm text-gray-500">
                    @if($workflow->is_active)
                        <span class="inline-flex items-center rounded-full bg-green-100 px-2.5 py-0.5 text-xs font-medium text-green-800">
                            Active
                        </span>
                    @else
                        <span class="inline-flex items-center rounded-full bg-gray-100 px-2.5 py-0.5 text-xs font-medium text-gray-800">
                            Inactive
                        </span>
                    @endif
                </div>
            </div>
        </div>
        <div class="mt-4 flex md:ml-4 md:mt-0 gap-x-3">
            <a href="{{ route('workflow-admin.workflows.edit', $workflow) }}" 
               class="inline-flex items-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50">
                Edit
            </a>
            <form action="{{ route('workflow-admin.workflows.destroy', $workflow) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this workflow?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="inline-flex items-center rounded-md bg-red-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-red-500">
                    Delete
                </button>
            </form>
        </div>
    </div>

    <!-- Details -->
    <div class="bg-white shadow sm:rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <h3 class="text-base font-semibold leading-6 text-gray-900">Workflow Details</h3>
            <dl class="mt-5 grid grid-cols-1 gap-5 sm:grid-cols-2">
                <div class="overflow-hidden rounded-lg bg-gray-50 px-4 py-5">
                    <dt class="truncate text-sm font-medium text-gray-500">Description</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $workflow->description ?: 'No description' }}</dd>
                </div>
                <div class="overflow-hidden rounded-lg bg-gray-50 px-4 py-5">
                    <dt class="truncate text-sm font-medium text-gray-500">Created</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $workflow->created_at->format('M d, Y g:i A') }}</dd>
                </div>
            </dl>
        </div>
    </div>

    <!-- Steps -->
    <div class="bg-white shadow sm:rounded-lg">
        <div class="px-4 py-5 sm:px-6 flex justify-between items-center">
            <div>
                <h3 class="text-base font-semibold leading-6 text-gray-900">Workflow Steps</h3>
                <p class="mt-1 max-w-2xl text-sm text-gray-500">Define the sequence of steps in this workflow</p>
            </div>
            <a href="{{ route('workflow-admin.steps.create', $workflow) }}" 
               class="inline-flex items-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500">
                Add Step
            </a>
        </div>
        <div class="border-t border-gray-200">
            @if($workflow->steps->count() > 0)
                <ul role="list" class="divide-y divide-gray-200">
                    @foreach($workflow->steps as $step)
                        <li class="px-4 py-4 sm:px-6 hover:bg-gray-50">
                            <div class="flex items-center justify-between">
                                <div class="flex items-start gap-x-4 flex-1">
                                    <div class="flex h-8 w-8 items-center justify-center rounded-full bg-indigo-100 text-indigo-600 font-semibold text-sm">
                                        {{ $step->order }}
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <p class="text-sm font-medium text-gray-900">{{ $step->title }}</p>
                                        <p class="text-sm text-gray-500">{{ $step->description }}</p>
                                        <div class="mt-2 flex items-center gap-x-4 text-xs text-gray-500">
                                            <span class="inline-flex items-center rounded-md bg-gray-100 px-2 py-1 font-medium text-gray-600">
                                                {{ $step->type }}
                                            </span>
                                            @if($step->condition_type !== 'always')
                                                <span class="inline-flex items-center rounded-md bg-yellow-100 px-2 py-1 font-medium text-yellow-800">
                                                    Conditional
                                                </span>
                                            @endif
                                            @if($step->actions->count() > 0)
                                                <span class="text-gray-500">{{ $step->actions->count() }} action(s)</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <div class="flex items-center gap-x-3">
                                    <a href="{{ route('workflow-admin.actions.create', [$workflow, $step]) }}" 
                                       class="text-indigo-600 hover:text-indigo-900 text-sm font-medium">
                                        Add Action
                                    </a>
                                    <a href="{{ route('workflow-admin.steps.edit', [$workflow, $step]) }}" 
                                       class="text-indigo-600 hover:text-indigo-900 text-sm font-medium">
                                        Edit
                                    </a>
                                    <form action="{{ route('workflow-admin.steps.destroy', [$workflow, $step]) }}" method="POST" onsubmit="return confirm('Are you sure?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-900 text-sm font-medium">
                                            Delete
                                        </button>
                                    </form>
                                </div>
                            </div>
                            
                            <!-- Actions for this step -->
                            @if($step->actions->count() > 0)
                                <div class="mt-4 ml-12 pl-4 border-l-2 border-gray-200">
                                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wide mb-2">Actions</p>
                                    <ul class="space-y-2">
                                        @foreach($step->actions as $action)
                                            <li class="flex items-center justify-between text-sm">
                                                <div class="flex items-center gap-x-2">
                                                    <span class="inline-flex items-center rounded-md bg-blue-100 px-2 py-1 text-xs font-medium text-blue-700">
                                                        {{ $action->type }}
                                                    </span>
                                                    <span class="text-gray-500">on {{ str_replace('_', ' ', $action->trigger) }}</span>
                                                </div>
                                                <div class="flex gap-x-2">
                                                    <a href="{{ route('workflow-admin.actions.edit', [$workflow, $step, $action]) }}" 
                                                       class="text-indigo-600 hover:text-indigo-900 text-xs font-medium">
                                                        Edit
                                                    </a>
                                                    <form action="{{ route('workflow-admin.actions.destroy', [$workflow, $step, $action]) }}" method="POST" onsubmit="return confirm('Are you sure?');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="text-red-600 hover:text-red-900 text-xs font-medium">
                                                            Delete
                                                        </button>
                                                    </form>
                                                </div>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
                        </li>
                    @endforeach
                </ul>
            @else
                <div class="px-4 py-12 text-center">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                    </svg>
                    <h3 class="mt-2 text-sm font-semibold text-gray-900">No steps</h3>
                    <p class="mt-1 text-sm text-gray-500">Get started by creating a step for this workflow.</p>
                    <div class="mt-6">
                        <a href="{{ route('workflow-admin.steps.create', $workflow) }}" 
                           class="inline-flex items-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500">
                            Add First Step
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection