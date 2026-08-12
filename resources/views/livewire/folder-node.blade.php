<div class="group">

    {{-- Folder --}}
    <div
        class="flex items-center justify-between rounded-lg px-1 py-0.5 transition-all duration-150 hover:bg-slate-100 dark:hover:bg-slate-800">

        @if($editing)

            <div class="flex w-full items-center gap-2">

                {{-- Chevron Placeholder / Alignment Space --}}
                @if($folder->childrenRecursive->count())
                    <div class="w-4"></div>
                @else
                    <div class="w-4"></div>
                @endif

                {{-- Folder Icon for Edit Mode --}}
                <svg
                    class="h-4 w-4 flex-shrink-0 text-amber-500"
                    fill="currentColor"
                    viewBox="0 0 20 20">
                    <path d="M2 5a2 2 0 012-2h3l2 2h7a2 2 0 012 2v7a2 2 0 01-2 2H4a2 2 0 01-2-2V5z"/>
                </svg>

                <input
                    type="text"
                    wire:model.defer="name"
                    wire:keydown.enter="save"
                    class="h-9 flex-1 rounded-lg border border-slate-300 bg-white px-3 text-sm shadow-sm outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 dark:border-slate-700 dark:bg-slate-900 dark:text-white"
                >

                <button
                    type="button"
                    wire:click="save"
                    class="rounded-lg bg-blue-600 px-2 py-1 text-xs font-medium text-white transition hover:bg-blue-700 cursor-pointer">
                    Save
                </button>

                <button
                    type="button"
                    wire:click="cancel"
                    class="rounded-lg border border-slate-300 bg-white px-2 py-1 text-xs font-medium text-slate-600 transition hover:bg-slate-100 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-300 dark:hover:bg-slate-800 cursor-pointer">
                    Cancel
                </button>

            </div>

        @else

            <button
                type="button"
                wire:click="selectFolder"
                class="flex flex-1 items-center gap-2 rounded-lg py-1 text-left cursor-pointer min-w-0">

                {{-- Chevron --}}
                @if($folder->childrenRecursive->count())

                    <svg
                        class="h-4 w-4 flex-shrink-0 text-slate-400"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2"
                        viewBox="0 0 24 24">

                        <path stroke-linecap="round"
                              stroke-linejoin="round"
                              d="M9 5l7 7-7 7"/>

                    </svg>

                @else

                    <div class="w-4 flex-shrink-0"></div>

                @endif

                {{-- Folder Icon --}}
                <svg
                    class="h-4 w-4 flex-shrink-0 text-amber-500"
                    fill="currentColor"
                    viewBox="0 0 20 20">

                    <path d="M2 5a2 2 0 012-2h3l2 2h7a2 2 0 012 2v7a2 2 0 01-2 2H4a2 2 0 01-2-2V5z"/>

                </svg>

                {{-- Folder Name --}}
                <span
                    class="text-xs font-medium text-slate-700 dark:text-slate-200 break-words">
                    {{ $folder->name }}
                </span>

            </button>

            {{-- Hover Actions --}}
            <div
                class="flex items-center gap-1 opacity-0 transition-all duration-150 group-hover:opacity-100 flex-shrink-0 ml-2">

                {{-- Add Sub-folder Button --}}
                <button
                    type="button"
                    wire:click="$set('showSubFolderModal', true)"
                    class="rounded-md p-1.5 text-slate-500 transition hover:bg-slate-200 hover:text-emerald-600 dark:hover:bg-slate-700 cursor-pointer"
                    title="Add Sub-folder">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                    </svg>
                </button>

                <button
                    type="button"
                    wire:click="edit"
                    class="rounded-md p-1.5 text-slate-500 transition hover:bg-slate-200 hover:text-blue-600 dark:hover:bg-slate-700 cursor-pointer"
                    title="Rename">

                    <svg class="h-4 w-4"
                         fill="none"
                         stroke="currentColor"
                         stroke-width="2"
                         viewBox="0 0 24 24">

                        <path stroke-linecap="round"
                              stroke-linejoin="round"
                              d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5"/>

                        <path stroke-linecap="round"
                              stroke-linejoin="round"
                              d="M18.5 2.5a2.121 2.121 0 113 3L12 15l-4 1 1-4 9.5-9.5z"/>

                    </svg>

                </button>

                <button
                    type="button"
                    wire:click="$set('showDeleteModal', true)"
                    class="rounded-md p-1.5 text-slate-500 transition hover:bg-red-100 hover:text-red-600 dark:hover:bg-red-900/30 cursor-pointer"
                    title="Delete">

                    <svg class="h-4 w-4"
                         fill="none"
                         stroke="currentColor"
                         stroke-width="2"
                         viewBox="0 0 24 24">

                        <path stroke-linecap="round"
                              stroke-linejoin="round"
                              d="M19 7L5 7"/>

                        <path stroke-linecap="round"
                              stroke-linejoin="round"
                              d="M10 11V17"/>

                        <path stroke-linecap="round"
                              stroke-linejoin="round"
                              d="M14 11V17"/>

                        <path stroke-linecap="round"
                              stroke-linejoin="round"
                              d="M6 7L7 20A2 2 0 009 22H15A2 2 0 0017 20L18 7"/>

                        <path stroke-linecap="round"
                              stroke-linejoin="round"
                              d="M9 7V4A1 1 0 0110 3H14A1 1 0 0115 4V7"/>

                    </svg>

                </button>

            </div>

        @endif

    </div>

    {{-- Child Folders --}}
    @if($folder->childrenRecursive->count())

        <div
            class="ml-7 border-l border-slate-200 pl-3 dark:border-slate-700">

            @foreach($folder->childrenRecursive as $child)

                <livewire:asset-manager.folder-node
                    :folder="$child"
                    :key="'folder-'.$child->id"
                />

            @endforeach

        </div>

    @endif

    {{-- Create Sub-folder Modal --}}
    @if(isset($showSubFolderModal) && $showSubFolderModal)
        <div class="fixed inset-0 z-[999999] flex items-center justify-center bg-slate-950/90 backdrop-blur-xl">
            <div class="w-full max-w-md rounded-2xl bg-white p-6 shadow-2xl dark:bg-gray-800 border border-gray-200 dark:border-gray-700 relative">
                <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100">Create Sub-folder inside: {{ $folder->name }}</h3>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Enter a name for the new sub-folder.</p>
                
                <div class="mt-4">
                    <input 
                        type="text" 
                        wire:model.defer="subFolderName" 
                        wire:keydown.enter="createSubFolder"
                        placeholder="Sub-folder name"
                        class="w-full rounded-xl border border-gray-300 px-4 py-2 text-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:text-white"
                    >
                </div>

                <div class="mt-6 flex justify-end gap-3">
                    <button type="button" wire:click="$set('showSubFolderModal', false)" class="rounded-xl px-4 py-2 text-xs font-semibold text-gray-600 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-700 transition-colors cursor-pointer">
                        Cancel
                    </button>
                    <button type="button" wire:click="createSubFolder" class="rounded-xl bg-indigo-600 px-4 py-2 text-xs font-semibold text-white hover:bg-indigo-700 shadow-sm transition-colors cursor-pointer">
                        Create Folder
                    </button>
                </div>
            </div>
        </div>
    @endif

 {{-- Folder Delete Confirmation Modal --}}
@if($showDeleteModal)
    <div x-data class="fixed inset-0 z-[999999] flex items-center justify-center bg-slate-900/90">
        <div class="w-full max-w-md rounded-2xl bg-white p-6 shadow-2xl dark:bg-gray-800 border border-gray-200 dark:border-gray-700 relative">
            <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100">Delete Folder: {{ $folder->name }}</h3>
            <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">Are you sure you want to delete this folder? This action cannot be undone.</p>
            
            <div class="mt-6 flex justify-end gap-3">
                <button type="button" wire:click="$set('showDeleteModal', false)" class="rounded-xl px-4 py-2 text-xs font-semibold text-gray-600 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-700 transition-colors cursor-pointer">
                    Cancel
                </button>
                <button type="button" wire:click="delete" class="rounded-xl bg-rose-600 px-4 py-2 text-xs font-semibold text-white hover:bg-rose-700 shadow-sm transition-colors cursor-pointer">
                    Yes, Delete
                </button>
            </div>
        </div>
    </div>
@endif

    {{-- Session Error Alert --}}
    @if (session()->has('error'))
        <div class="mt-3 rounded-2xl bg-red-100 p-5 text-base font-semibold text-red-800 shadow-lg dark:bg-red-900/40 dark:text-red-300 border border-red-200 dark:border-red-800 flex items-center justify-between">
            <span>{{ session('error') }}</span>
            <button type="button" onclick="this.parentElement.remove()" class="text-red-500 hover:text-red-700 font-bold ml-4 text-lg cursor-pointer">&times;</button>
        </div>
    @endif

</div>