{{-- Floating Toast Notification Component (Top-Left Corner) --}}
<div 
    x-data="{ show: false, message: '' }"
    @notify.window="
        message = $event.detail[0] ?? $event.detail; 
        show = true; 
        setTimeout(() => show = false, 3500)
    "
    x-show="show"
    x-transition:enter="transition ease-out duration-300"
    x-transition:enter-start="opacity-0 transform -translate-x-8"
    x-transition:enter-end="opacity-100 transform translate-x-0"
    x-transition:leave="transition ease-in duration-200"
    x-transition:leave-start="opacity-100 transform translate-x-0"
    x-transition:leave-end="opacity-0 transform -translate-x-8"
    style="display: none;"
    class="fixed top-6 left-6 z-50 flex items-center gap-3 rounded-2xl bg-gray-900 px-4 py-3 text-white shadow-2xl border border-gray-700"
>
    <div class="flex h-6 w-6 items-center justify-center rounded-full bg-emerald-500/20 text-emerald-400">
        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
        </svg>
    </div>
    <span class="text-xs font-medium text-gray-200" x-text="message"></span>
    <button @click="show = false" class="ml-2 text-gray-400 hover:text-white text-xs cursor-pointer">✕</button>
</div>

@if(count($selectedAssets) > 0)
    <div class="fixed bottom-6 left-1/2 -translate-x-1/2 z-50 flex items-center gap-2 bg-gray-900/90 backdrop-blur-md text-white px-4 py-2.5 rounded-2xl shadow-2xl border border-gray-700 animate-in fade-in slide-in-from-bottom-5">
        
        <div class="flex items-center gap-2 border-r border-gray-700 pr-3">
            <span class="bg-indigo-600 text-white text-xs font-bold px-2 py-0.5 rounded-full">
                {{ count($selectedAssets) }}
            </span>
            <span class="text-xs font-medium text-gray-300">Selected</span>
        </div>

        {{-- Move --}}
        <button type="button" wire:click="$set('showBulkMoveModal', true)" class="p-2 rounded-lg hover:bg-gray-800 text-gray-300 hover:text-indigo-400 transition-colors text-xs flex items-center gap-1.5 cursor-pointer" title="Move Files">
            📁 <span>Move</span>
        </button>

        {{-- Download Zip --}}
        <button type="button" wire:click="bulkDownloadZip" class="p-2 rounded-lg hover:bg-gray-800 text-gray-300 hover:text-sky-400 transition-colors text-xs flex items-center gap-1.5 cursor-pointer" title="Download ZIP">
            📦 <span>Zip</span>
        </button>

        {{-- Delete --}}
        <button type="button" wire:click="$set('showBulkDeleteModal', true)" class="p-2 rounded-lg hover:bg-rose-950/50 text-rose-400 hover:text-rose-300 transition-colors text-xs flex items-center gap-1.5 cursor-pointer" title="Delete Selected">
            🗑️ <span>Delete</span>
        </button>

        {{-- Clear --}}
        <button type="button" wire:click="clearSelection" class="border-l border-gray-700 pl-3 text-xs text-gray-400 hover:text-white cursor-pointer">
            ✕ Clear
        </button>

    </div>
@endif

{{-- Bulk Delete Confirmation Modal --}}
@if($showBulkDeleteModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm animate-in fade-in">
        <div class="w-full max-w-md rounded-2xl bg-white p-6 shadow-2xl dark:bg-gray-800 border border-gray-100 dark:border-gray-700">
            <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100">Delete Selected Assets</h3>
            <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">Are you sure you want to delete the selected assets? This action cannot be undone.</p>
            
            <div class="mt-6 flex justify-end gap-3">
                <button type="button" wire:click="$set('showBulkDeleteModal', false)" class="rounded-xl px-4 py-2 text-xs font-semibold text-gray-600 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-700 transition-colors cursor-pointer">
                    Cancel
                </button>
                <button type="button" wire:click="bulkDelete" class="rounded-xl bg-rose-600 px-4 py-2 text-xs font-semibold text-white hover:bg-rose-700 shadow-sm transition-colors cursor-pointer">
                    Yes, Delete
                </button>
            </div>
        </div>
    </div>
@endif

{{-- Bulk Move Modal --}}
@if($showBulkMoveModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm animate-in fade-in">
        <div class="w-full max-w-md rounded-2xl bg-white p-6 shadow-2xl dark:bg-gray-800 border border-gray-100 dark:border-gray-700">
            <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100">Move Selected Assets</h3>
            <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">Select the destination folder for the chosen assets.</p>
            
            <div class="mt-4">
                <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Destination Folder</label>
                <select wire:model="bulkFolderId" class="w-full rounded-xl border border-gray-300 bg-white px-3 py-2 text-xs text-gray-900 focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100 cursor-pointer">
                    <option value="">Root (All Assets)</option>
                    @if(isset($folders) && $folders->count())
                        @foreach($folders as $folder)
                            <option value="{{ $folder->id }}">{{ $folder->name }}</option>
                        @endforeach
                    @endif
                </select>
            </div>

            <div class="mt-6 flex justify-end gap-3">
                <button type="button" wire:click="$set('showBulkMoveModal', false)" class="rounded-xl px-4 py-2 text-xs font-semibold text-gray-600 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-700 transition-colors cursor-pointer">
                    Cancel
                </button>
                <button type="button" wire:click="bulkMove" class="rounded-xl bg-indigo-600 px-4 py-2 text-xs font-semibold text-white hover:bg-indigo-700 shadow-sm transition-colors cursor-pointer">
                    Move Assets
                </button>
            </div>
        </div>
    </div>
@endif