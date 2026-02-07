<nav class="bg-gray-800">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="flex h-16 items-center justify-between">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <h1 class="text-white text-xl font-bold">Workflow Admin</h1>
                </div>
                <div class="hidden md:block">
                    <div class="ml-10 flex items-baseline space-x-4">
                        <a href="{{ route('workflow-admin.dashboard') }}" 
                           class="@if(request()->routeIs('workflow-admin.dashboard')) bg-gray-900 @else hover:bg-gray-700 @endif text-white rounded-md px-3 py-2 text-sm font-medium">
                            Dashboard
                        </a>
                        <a href="{{ route('workflow-admin.workflows.index') }}" 
                           class="@if(request()->routeIs('workflow-admin.workflows.*')) bg-gray-900 @else hover:bg-gray-700 @endif text-white rounded-md px-3 py-2 text-sm font-medium">
                            Workflows
                        </a>
                    </div>
                </div>
            </div>
            <div class="hidden md:block">
                <div class="ml-4 flex items-center md:ml-6">
                    <a href="/" class="text-gray-300 hover:text-white text-sm font-medium">
                        ← Back to App
                    </a>
                </div>
            </div>
        </div>
    </div>
</nav>