<form action="{{ $action }}" method="POST" class="space-y-6">
    @csrf
    @if($method !== 'POST')
        @method($method)
    @endif
    
    <div class="space-y-4">
        <div>
            <label for="name" class="block text-sm font-medium leading-6 text-gray-900">Name *</label>
            <div class="mt-2">
                <input type="text" name="name" id="name" 
                       value="{{ old('name', $workflow->name ?? '') }}" 
                       required
                       class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
            </div>
            @error('name')
                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="description" class="block text-sm font-medium leading-6 text-gray-900">Description</label>
            <div class="mt-2">
                <textarea name="description" id="description" rows="3"
                          class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">{{ old('description', $workflow->description ?? '') }}</textarea>
            </div>
            @error('description')
                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="type" class="block text-sm font-medium leading-6 text-gray-900">Type *</label>
            <div class="mt-2">
                <input type="text" name="type" id="type" 
                       value="{{ old('type', $workflow->type ?? '') }}" 
                       required
                       placeholder="e.g., onboarding, interview, approval"
                       class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
            </div>
            @error('type')
                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div class="flex items-start">
            <div class="flex h-6 items-center">
                <input type="checkbox" name="is_active" id="is_active" value="1" 
                       {{ old('is_active', $workflow->is_active ?? true) ? 'checked' : '' }}
                       class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-600">
            </div>
            <div class="ml-3 text-sm leading-6">
                <label for="is_active" class="font-medium text-gray-900">Active</label>
                <p class="text-gray-500">Allow users to start new instances of this workflow</p>
            </div>
        </div>
    </div>

    <div class="flex justify-end gap-x-3">
        {{ $slot }}
        <button type="submit" 
                class="rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">
            {{ $workflow ? 'Update Workflow' : 'Create Workflow' }}
        </button>
    </div>
</form>