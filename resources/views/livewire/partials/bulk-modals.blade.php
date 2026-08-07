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

{{-- Bulk Tag Modal --}}
@if($showBulkTagModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-xs p-4">
        <div class="w-full max-w-md rounded-2xl bg-white dark:bg-gray-900 p-6 shadow-xl border border-gray-200 dark:border-gray-800 space-y-4">
            <h3 class="text-sm font-bold text-gray-900 dark:text-gray-100 uppercase tracking-wider">Attach Tags to Selected Assets</h3>
            
            <div class="flex flex-wrap gap-2 max-h-48 overflow-y-auto">
                @foreach($allTags as $t)
                    <label class="flex items-center gap-2 px-3 py-1.5 rounded-full border border-gray-200 dark:border-gray-700 text-xs cursor-pointer dark:text-gray-300">
                        <input type="checkbox" value="{{ $t->id }}" wire:model="bulkTagIds" class="rounded border-gray-300 text-indigo-600">
                        <span class="h-2 w-2 rounded-full" style="background-color: {{ $t->color ?? '#6366f1' }}"></span>
                        <span>{{ $t->name }}</span>
                    </label>
                @endforeach
            </div>

            <div class="flex justify-end gap-2 pt-2 border-t border-gray-100 dark:border-gray-800">
                <button type="button" wire:click="$set('showBulkTagModal', false)" class="px-3 py-1.5 rounded-lg border border-gray-300 dark:border-gray-700 text-xs font-semibold text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800">Cancel</button>
                <button type="button" wire:click="bulkAttachTags" class="px-4 py-1.5 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-xs font-semibold text-white shadow-xs">Apply Tags</button>
            </div>
        </div>
    </div>
@endif