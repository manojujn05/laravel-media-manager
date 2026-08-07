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

        {{-- Library --}}
        <div class="border-b border-gray-200 dark:border-gray-800 px-5 py-4">

            <h3 class="mb-2 text-[11px] font-bold uppercase tracking-wider text-gray-400 dark:text-gray-500">
                Library
            </h3>

            {{-- All Assets --}}
            <button
                type="button"
                wire:click="$parent.selectFolder(null)"
                class="flex w-full items-center gap-3 rounded-lg px-3 py-2 text-xs font-medium transition-colors text-left {{ is_null($activeFolder ?? null) && !($favorites ?? false) && is_null($activeTag ?? null) ? 'bg-indigo-50 text-indigo-600 dark:bg-indigo-950/50 dark:text-indigo-400 font-semibold' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800' }}"
            >
                <span class="text-base">📁</span>
                <span>Assets</span>
            </button>

      

        </div>

        {{-- Media Types --}}
        <div class="border-b border-gray-200 dark:border-gray-800 px-5 py-4">

            <h3 class="mb-2 text-[11px] font-bold uppercase tracking-wider text-gray-400 dark:text-gray-500">
                Media Types
            </h3>

            <div class="space-y-1">
       
            </div>

        </div>

        {{-- Folder Section --}}
        <div class="border-b border-gray-200 dark:border-gray-800 px-5 py-4">

            <div class="mb-3 flex items-center justify-between">

                <h3 class="text-[11px] font-bold uppercase tracking-wider text-gray-400 dark:text-gray-500">
                    Folders
                </h3>

                <button
                    type="button"
                    wire:click="$dispatch('open-create-folder')"
                    class="flex h-6 w-6 items-center justify-center rounded-md bg-indigo-600 text-white hover:bg-indigo-700 text-xs transition-colors shadow-2xs font-bold"
                    title="New Folder"
                >
                    +
                </button>

            </div>

            <div class="space-y-1">
                <livewire:asset-manager.folder-tree :active-folder="$activeFolder ?? $folder ?? null" />
            </div>

        </div>

        {{-- Dynamic Tags Section (Phase 9) --}}
        <div class="px-5 py-4">

            <div class="mb-3 flex items-center justify-between">
                <h3 class="text-[11px] font-bold uppercase tracking-wider text-gray-400 dark:text-gray-500">
                    Tags
                </h3>

                <button
                    type="button"
                    wire:click="$parent.openTagModal()"
                    class="text-[11px] font-semibold text-indigo-600 dark:text-indigo-400 hover:underline flex items-center gap-1"
                >
                    + New Tag
                </button>
            </div>

            <div class="space-y-1">
                @forelse($tags ?? $allTags ?? [] as $t)
                    <div class="group flex items-center justify-between rounded-lg px-2.5 py-1.5 text-xs transition-colors {{ ($activeTag ?? null) == $t->id ? 'bg-indigo-50 dark:bg-indigo-950/50 text-indigo-600 dark:text-indigo-400 font-semibold' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800' }}">
                        <button 
                            type="button" 
                            wire:click="$parent.selectTag({{ $t->id }})" 
                            class="flex items-center gap-2 flex-1 truncate text-left"
                        >
                            <span class="h-2 w-2 rounded-full shrink-0" style="background-color: {{ $t->color ?? '#6366f1' }}"></span>
                            <span class="truncate">{{ $t->name }}</span>
                            <span class="text-[10px] text-gray-400 font-normal">({{ $t->assets_count ?? $t->assets()->count() }})</span>
                        </button>

                        <button 
                            type="button" 
                            wire:click="$parent.deleteTag({{ $t->id }})" 
                            wire:confirm="Are you sure you want to delete this tag?" 
                            class="opacity-0 group-hover:opacity-100 text-rose-500 hover:text-rose-700 p-0.5 transition-opacity"
                            title="Delete Tag"
                        >
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>
                @empty
                    <p class="text-[11px] text-gray-400 italic px-1">No tags created yet.</p>
                @endforelse
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