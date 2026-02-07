<div class="bg-white shadow sm:rounded-lg">
    <div class="px-4 py-5 sm:p-6">
        <h3 class="text-base font-semibold leading-6 text-gray-900 mb-4">
            Steps for {{ $workflow->name }}
        </h3>
        
        @if($steps->count() > 0)
            <ul role="list" class="space-y-3">
                @foreach($steps as $step)
                    <li class="border border-gray-200 rounded-lg p-4">
                        <div class="flex items-start gap-x-4">
                            <div class="flex h-8 w-8 items-center justify-center rounded-full bg-indigo-100 text-indigo-600 font-semibold text-sm flex-shrink-0">
                                {{ $step->order }}
                            </div>
                            <div class="flex-1 min-w-0">
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
                                    @if($showActions && $step->actions->count() > 0)
                                        <span>{{ $step->actions->count() }} action(s)</span>
                                    @endif
                                </div>

                                @if($showActions && $step->actions->count() > 0)
                                    <div class="mt-3 pl-4 border-l-2 border-gray-200">
                                        <p class="text-xs font-medium text-gray-500 uppercase tracking-wide mb-2">Actions</p>
                                        <ul class="space-y-1">
                                            @foreach($step->actions as $action)
                                                <li class="text-xs text-gray-600">
                                                    <span class="inline-flex items-center rounded-md bg-blue-100 px-2 py-0.5 font-medium text-blue-700">
                                                        {{ $action->type }}
                                                    </span>
                                                    <span class="text-gray-500">on {{ str_replace('_', ' ', $action->trigger) }}</span>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </li>
                @endforeach
            </ul>
        @else
            <div class="text-center py-8">
                <p class="text-sm text-gray-500">No steps defined yet.</p>
            </div>
        @endif
    </div>
</div>