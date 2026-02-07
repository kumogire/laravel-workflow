<div class="bg-white shadow sm:rounded-lg">
    <div class="px-4 py-5 sm:p-6">
        <h3 class="text-base font-semibold leading-6 text-gray-900 mb-4">Workflows</h3>
        
        @if($workflows->count() > 0)
            <ul role="list" class="divide-y divide-gray-200">
                @foreach($workflows as $workflow)
                    <li class="py-4">
                        <div class="flex items-center justify-between">
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-900 truncate">
                                    {{ $workflow->name }}
                                </p>
                                <p class="text-sm text-gray-500">{{ $workflow->description }}</p>
                                <div class="mt-2 flex items-center gap-x-4 text-xs text-gray-500">
                                    <span>Type: {{ $workflow->type }}</span>
                                    <span>{{ $workflow->steps_count }} steps</span>
                                    <span>Created {{ $workflow->created_at->diffForHumans() }}</span>
                                </div>
                            </div>
                            <div class="ml-4 flex items-center gap-x-2">
                                @if($workflow->is_active)
                                    <span class="inline-flex rounded-full bg-green-100 px-2 text-xs font-semibold leading-5 text-green-800">
                                        Active
                                    </span>
                                @else
                                    <span class="inline-flex rounded-full bg-gray-100 px-2 text-xs font-semibold leading-5 text-gray-800">
                                        Inactive
                                    </span>
                                @endif
                            </div>
                        </div>
                    </li>
                @endforeach
            </ul>
        @else
            <div class="text-center py-8">
                <p class="text-sm text-gray-500">No workflows found.</p>
            </div>
        @endif
    </div>
</div>