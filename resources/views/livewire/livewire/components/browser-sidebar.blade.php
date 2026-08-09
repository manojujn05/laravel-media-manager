<div>
<div class="flex h-full flex-col bg-white dark:bg-gray-900">

    {{-- Header --}}
    <div class="border-b border-gray-200 dark:border-gray-800 px-5 py-4">
        <h2 class="text-lg font-semibold text-gray-900 dark:text-white">
            Media Library
        </h2>

        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
            Browse and manage your assets
        </p>
    </div>

    {{-- Scroll Area --}}
    <div class="flex-1 overflow-y-auto">
        {{-- Folder Section --}}
        <div class="border-b border-gray-200 dark:border-gray-800 px-5 py-4">
            <div class="mb-3 flex items-center justify-between">

                <h3 class="text-[11px] font-bold uppercase tracking-wider text-gray-400 dark:text-gray-500">
                    Folders
                </h3>

                <button
                    type="button"
                    wire:click="$dispatch('open-create-folder')"
                    class="flex items-center gap-1 rounded-md text-[11px] font-semibold text-indigo-600 hover:text-indigo-700 dark:text-indigo-400 dark:hover:text-indigo-300 transition-colors"
                    title="Create New Folder"
                >
                    <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    New Folder
                </button>
            </div>

            <div>
                <livewire:asset-manager.folder-tree :active-folder="$activeFolder ?? $folder ?? null" />
            </div>

        </div>
    </div>

    {{-- Footer --}}
    <div class="border-t border-gray-200 dark:border-gray-800 p-4 bg-gray-50/50 dark:bg-gray-900/50">

        <div class="mb-2 flex justify-between text-xs">

            <span class="text-gray-500 dark:text-gray-400">
                Storage Used
            </span>

            <span class="font-semibold text-gray-700 dark:text-gray-200">
                {{ $usedStorage ?? '0 B' }}
            </span>

        </div>

        <div class="h-1.5 overflow-hidden rounded-full bg-gray-200 dark:bg-gray-700">

            <div
                class="h-full rounded-full bg-indigo-600 transition-all duration-500"
                style="width: {{ $storagePercentage ?? 0 }}%">
            </div>

        </div>

    </div>

</div>
</div>