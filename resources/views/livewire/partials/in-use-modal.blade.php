@if($showInUseModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
        <div class="w-full max-w-lg rounded-xl bg-white p-6 shadow-2xl dark:bg-gray-800">
            <div class="flex items-center text-red-600 mb-4">
                <svg class="h-8 w-8 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
                <h3 class="text-xl font-bold">Asset in Use</h3>
            </div>
            
            <p class="text-gray-700 dark:text-gray-300 mb-4">
                {{ $inUseMessage }}
            </p>

            <div class="bg-gray-50 dark:bg-gray-900 rounded-lg p-4 mb-6 max-h-48 overflow-y-auto">
                <h4 class="text-sm font-semibold text-gray-500 mb-2 uppercase tracking-wide">Dependencies</h4>
                <ul class="space-y-2">
                    @foreach($inUseUsages as $usage)
                        <li class="flex items-center justify-between text-sm text-gray-800 dark:text-gray-200">
                            <span>
                                <span class="font-medium">{{ class_basename($usage['usable_type']) }}</span>
                                (ID: {{ $usage['usable_id'] }})
                            </span>
                            <span class="text-xs bg-gray-200 dark:bg-gray-700 px-2 py-1 rounded">
                                Field: {{ $usage['field'] }}
                            </span>
                        </li>
                    @endforeach
                </ul>
            </div>

            <div class="flex justify-end gap-3">
                <button type="button" wire:click="closeInUseModal" class="rounded-lg bg-gray-200 px-4 py-2 text-gray-800 hover:bg-gray-300 dark:bg-gray-700 dark:text-white dark:hover:bg-gray-600 font-medium transition-colors">
                    Acknowledge
                </button>
            </div>
        </div>
    </div>
@endif
