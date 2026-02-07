<div class="bg-white shadow sm:rounded-lg">
    <div class="px-4 py-5 sm:p-6">
        <h3 class="text-base font-semibold leading-6 text-gray-900 mb-4">{{ $title }}</h3>
        
        @if($workflows->count() > 0)
            <ul role="list" class="divide-y divide-gray-200">
                @foreach($workflows as $workflow)
                    <li class="py-3">
                        <div class="flex items-center justify-between">
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-900 truncate">
                                    {{ $workflow->name }}
                                </p>
                                <p class="text-xs text-gray-500">{{ $workflow->type }}</p>
                            </div>
                            <div class="ml-2 flex-shrink-0">
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
            <div class="text-center py-4">
                <p class="text-sm text-gray-500">No workflows yet.</p>
            </div>
        @endif
    </div>
</div>