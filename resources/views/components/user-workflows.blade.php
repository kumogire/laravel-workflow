<div class="bg-white shadow sm:rounded-lg">
    <div class="px-4 py-5 sm:p-6">
        <h3 class="text-base font-semibold leading-6 text-gray-900 mb-4">
            {{ $status ? ucfirst($status) . ' Workflows' : 'My Workflows' }}
        </h3>
        
        @if($instances->count() > 0)
            <ul role="list" class="divide-y divide-gray-200">
                @foreach($instances as $instance)
                    <li class="py-4">
                        <div class="flex items-center justify-between">
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-900">
                                    {{ $instance->workflow->name }}
                                </p>
                                <p class="text-sm text-gray-500">
                                    @if($instance->currentStep)
                                        Current: {{ $instance->currentStep->title }}
                                    @else
                                        Completed
                                    @endif
                                </p>
                                <p class="text-xs text-gray-500 mt-1">
                                    Started {{ $instance->started_at?->diffForHumans() ?? $instance->created_at->diffForHumans() }}
                                </p>
                            </div>
                            <div class="ml-4 flex-shrink-0">
                                @if($instance->status === 'in_progress')
                                    <span class="inline-flex rounded-full bg-blue-100 px-2 py-1 text-xs font-semibold text-blue-800">
                                        In Progress
                                    </span>
                                @elseif($instance->status === 'completed')
                                    <span class="inline-flex rounded-full bg-green-100 px-2 py-1 text-xs font-semibold text-green-800">
                                        Completed
                                    </span>
                                @elseif($instance->status === 'paused')
                                    <span class="inline-flex rounded-full bg-yellow-100 px-2 py-1 text-xs font-semibold text-yellow-800">
                                        Paused
                                    </span>
                                @else
                                    <span class="inline-flex rounded-full bg-gray-100 px-2 py-1 text-xs font-semibold text-gray-800">
                                        {{ ucfirst($instance->status) }}
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