@extends('workflow::admin.layouts.app')

@section('title', 'Create Workflow')

@section('content')
<div class="space-y-6">
    <div>
        <a href="{{ route('workflow-admin.workflows.index') }}" class="text-sm text-gray-500 hover:text-gray-700">
            ← Back to Workflows
        </a>
        <h1 class="mt-2 text-2xl font-bold text-gray-900">Create Workflow</h1>
    </div>

    <form action="{{ route('workflow-admin.workflows.store') }}" method="POST" class="space-y-6">
        @csrf
        
        <div class="bg-white shadow sm:rounded-lg">
            <div class="px-4 py-5 sm:p-6 space-y-6">
                <div>
                    <label for="name" class="block text-sm font-medium leading-6 text-gray-900">Name *</label>
                    <div class="mt-2">
                        <input type="text" name="name" id="name" value="{{ old('name') }}" required
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
                        <input type="text" name="type" id="type" value="{{ old('type') }}" required
                               placeholder="e.g., onboarding, interview, approval"
                               class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                    </div>
                    <p class="mt-2 text-sm text-gray-500">A category or identifier for this workflow type</p>
                </div>

                <div class="flex items-start">
                    <div class="flex h-6 items-center">
                        <input type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}
                               class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-600">
                    </div>
                    <div class="ml-3 text-sm leading-6">
                        <label for="is_active" class="font-medium text-gray-900">Active</label>
                        <p class="text-gray-500">Allow users to start new instances of this workflow</p>
                    </div>
                </div>
            </div>

            <div class="bg-gray-50 px-4 py-3 sm:px-6 flex justify-end gap-x-3">
                <a href="{{ route('workflow-admin.workflows.index') }}" 
                   class="rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50">
                    Cancel
                </a>
                <button type="submit" 
                        class="rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">
                    Create Workflow
                </button>
            </div>
        </div>
    </form>
</div>
@endsection