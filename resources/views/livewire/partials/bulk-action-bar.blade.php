@if(count($selectedAssets) > 0)
    <div class="fixed bottom-6 left-1/2 -translate-x-1/2 z-50 flex items-center gap-2 bg-gray-900/90 backdrop-blur-md text-white px-4 py-2.5 rounded-2xl shadow-2xl border border-gray-700 animate-in fade-in slide-in-from-bottom-5">
        
        <div class="flex items-center gap-2 border-r border-gray-700 pr-3">
            <span class="bg-indigo-600 text-white text-xs font-bold px-2 py-0.5 rounded-full">
                {{ count($selectedAssets) }}
            </span>
            <span class="text-xs font-medium text-gray-300">Selected</span>
        </div>

        {{-- Move --}}
        <button type="button" wire:click="$set('showBulkMoveModal', true)" class="p-2 rounded-lg hover:bg-gray-800 text-gray-300 hover:text-indigo-400 transition-colors text-xs flex items-center gap-1.5" title="Move Files">
            📁 <span>Move</span>
        </button>

        {{-- Download Zip --}}
        <button type="button" wire:click="bulkDownloadZip" class="p-2 rounded-lg hover:bg-gray-800 text-gray-300 hover:text-sky-400 transition-colors text-xs flex items-center gap-1.5" title="Download ZIP">
            📦 <span>Zip</span>
        </button>

        {{-- Delete --}}
        <button type="button" wire:click="$set('showBulkDeleteModal', true)" class="p-2 rounded-lg hover:bg-rose-950/50 text-rose-400 hover:text-rose-300 transition-colors text-xs flex items-center gap-1.5" title="Delete Selected">
            🗑️ <span>Delete</span>
        </button>

        {{-- Clear --}}
        <button type="button" wire:click="clearSelection" class="border-l border-gray-700 pl-3 text-xs text-gray-400 hover:text-white">
            ✕ Clear
        </button>

    </div>
@endif