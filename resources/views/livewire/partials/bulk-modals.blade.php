{{-- Bulk Move Modal --}}
@if($showBulkMoveModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-xs p-4">
        <div class="w-full max-w-md rounded-2xl bg-white dark:bg-gray-900 p-6 shadow-xl border border-gray-200 dark:border-gray-800 space-y-4">
            <h3 class="text-sm font-bold text-gray-900 dark:text-gray-100 uppercase tracking-wider">Move Selected Assets</h3>
            
            <select wire:model="bulkFolderId" class="w-full rounded-lg border border-gray-300 dark:border-gray-700 dark:bg-gray-800 text-xs p-2.5 dark:text-gray-200">
                <option value="">Root Folder (No Folder)</option>
                @foreach($folders as $f)
                    <option value="{{ $f->id }}">{{ $f->name }}</option>
                @endforeach
            </select>

            <div class="flex justify-end gap-2 pt-2 border-t border-gray-100 dark:border-gray-800">
                <button type="button" wire:click="$set('showBulkMoveModal', false)" class="px-3 py-1.5 rounded-lg border border-gray-300 dark:border-gray-700 text-xs font-semibold text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800">Cancel</button>
                <button type="button" wire:click="bulkMove" class="px-4 py-1.5 rounded-lg bg-indigo-600 hover:bg-indigo-700 text-xs font-semibold text-white shadow-xs">Move Assets</button>
            </div>
        </div>
    </div>
@endif

{{-- Bulk Delete Confirmation Modal --}}
@if($showBulkDeleteModal ?? false)
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-xs p-4">
        <div class="w-full max-w-sm rounded-2xl bg-white dark:bg-gray-900 p-6 shadow-xl border border-gray-200 dark:border-gray-800 text-center">
            
            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-rose-100 dark:bg-rose-900/30 mb-4">
                <svg class="h-6 w-6 text-rose-600 dark:text-rose-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
            </div>

            <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100 mb-2">Confirm Deletion</h3>
            
            <p class="text-sm text-gray-500 dark:text-gray-400 mb-6">
                Are you sure you want to delete the <span class="font-bold text-gray-900 dark:text-white">{{ count($selectedAssets ?? []) }}</span> selected files? This action cannot be undone.
            </p>

            <div class="flex items-center justify-center gap-3">
                <button type="button" wire:click="$set('showBulkDeleteModal', false)" class="px-4 py-2 rounded-xl border border-gray-300 dark:border-gray-700 text-sm font-semibold text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors">
                    Cancel
                </button>
                <button type="button" wire:click="bulkDelete" class="px-4 py-2 rounded-xl bg-rose-600 hover:bg-rose-700 text-sm font-semibold text-white shadow-xs transition-colors">
                    Delete Files
                </button>
            </div>
        </div>
    </div>
@endif