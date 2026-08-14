@if($showConfirmationModal ?? false)
    <div class="fixed inset-0 z-[999999] flex items-center justify-center">
        <div class="absolute inset-0 bg-slate-900/90" wire:click="cancelConfirmation"></div>
        <div class="relative z-10 w-full max-w-md rounded-2xl bg-white p-6 shadow-2xl dark:bg-gray-800 border border-gray-200 dark:border-gray-700 text-center">
            
            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full mb-4 {{ ($confirmationModalType ?? 'destructive') === 'destructive' ? 'bg-rose-100 dark:bg-rose-900/30' : 'bg-indigo-100 dark:bg-indigo-900/30' }}">
                @if(($confirmationModalType ?? 'destructive') === 'destructive')
                    <svg class="h-6 w-6 text-rose-600 dark:text-rose-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                @else
                    <svg class="h-6 w-6 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                @endif
            </div>

            <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100 mb-2">{{ $confirmationModalTitle ?? 'Confirm Action' }}</h3>
            
            <p class="text-sm text-gray-500 dark:text-gray-400 mb-6">
                {{ $confirmationModalMessage ?? 'Are you sure you want to proceed?' }}
            </p>

            <div class="flex items-center justify-center gap-3">
                <button type="button" wire:click="cancelConfirmation" class="px-4 py-2 rounded-xl border border-gray-300 dark:border-gray-700 text-sm font-semibold text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors cursor-pointer">
                    Cancel
                </button>
                <button type="button" wire:click="executeConfirmation" class="px-4 py-2 rounded-xl text-sm font-semibold text-white shadow-xs transition-colors cursor-pointer {{ ($confirmationModalType ?? 'destructive') === 'destructive' ? 'bg-rose-600 hover:bg-rose-700' : 'bg-indigo-600 hover:bg-indigo-700' }}">
                    {{ $confirmationModalConfirmText ?? 'Confirm' }}
                </button>
            </div>
        </div>
    </div>
@endif
