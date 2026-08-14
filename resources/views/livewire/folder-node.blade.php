<div class="group">

    {{-- Folder --}}
    <div
        class="flex items-center justify-between rounded-lg px-1 py-0.5 transition-all duration-150 hover:bg-slate-100 dark:hover:bg-slate-800">

        @if($editing)

            <div class="flex w-full items-center gap-2">

                <input
                    type="text"
                    wire:model.defer="name"
                    wire:keydown.enter="save"
                    class="h-9 flex-1 rounded-lg border border-slate-300 bg-white px-3 text-sm shadow-sm outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 dark:border-slate-700 dark:bg-slate-900 dark:text-white"
                >

                <button
                    wire:click="save"
                    class="rounded-lg bg-blue-600 px-1 py-1 text-xs font-medium text-white transition hover:bg-blue-700">
                    Save
                </button>

                <button
                    wire:click="cancel"
                    class="rounded-lg border border-slate-300 bg-white px-1 py-1 text-xs font-medium text-slate-600 transition hover:bg-slate-100 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-300 dark:hover:bg-slate-800">
                    Cancel
                </button>

            </div>

        @else

            <button
                wire:click="selectFolder"
                class="flex flex-1 items-center gap-2 overflow-hidden rounded-lg py-1 text-left">

                {{-- Chevron --}}
                @if($folder->childrenRecursive->count())

                    <svg
                        class="h-4 w-4 text-slate-400"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2"
                        viewBox="0 0 24 24">

                        <path stroke-linecap="round"
                              stroke-linejoin="round"
                              d="M9 5l7 7-7 7"/>

                    </svg>

                @else

                    <div class="w-4"></div>

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
                    class="truncate text-xs font-medium text-slate-700 dark:text-slate-200">

                    {{ $folder->name }}

                </span>

            </button>

            {{-- Hover Actions --}}
            <div
                class="flex items-center gap-1 opacity-0 transition-all duration-150 group-hover:opacity-100">

                <button
                    wire:click="edit"
                    class="rounded-md p-1.5 text-slate-500 transition hover:bg-slate-200 hover:text-blue-600 dark:hover:bg-slate-700"
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
                    wire:click="$set('showDeleteModal', true)"
                    class="rounded-md p-1.5 text-slate-500 transition hover:bg-red-100 hover:text-red-600 dark:hover:bg-red-900/30"
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

    {{-- Delete confirmation modal --}}
    @if($showDeleteModal)
        <div
            class="fixed inset-0 z-[9999] flex items-center justify-center"
            wire:key="delete-modal-{{ $folder->id }}"
        >
            <div
                class="absolute inset-0 bg-black/50"
                wire:click="$set('showDeleteModal', false)"
            ></div>

            <div
                class="relative z-10 w-full max-w-md rounded-xl bg-white p-6 shadow-xl dark:bg-slate-800"
                wire:key="delete-dialog-{{ $folder->id }}"
            >
                <h2 class="text-lg font-semibold text-slate-900 dark:text-white">
                    Delete Folder
                </h2>

                <p class="mt-2 text-sm text-slate-600 dark:text-slate-300">
                    Are you sure you want to delete
                    <strong>{{ $folder->name }}</strong>?
                    This action cannot be undone.
                </p>

                <div class="mt-6 flex justify-end gap-3">
                    <button
                        type="button"
                        wire:click="$set('showDeleteModal', false)"
                        class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50 dark:border-slate-600 dark:text-slate-200 dark:hover:bg-slate-700"
                    >
                        Cancel
                    </button>

                    <button
                        type="button"
                        wire:click="delete"
                        wire:loading.attr="disabled"
                        class="rounded-lg bg-red-600 px-4 py-2 text-sm font-medium text-white hover:bg-red-700"
                    >
                        <span wire:loading.remove wire:target="delete">
                            Delete
                        </span>

                        <span wire:loading wire:target="delete">
                            Deleting...
                        </span>
                    </button>
                </div>
            </div>
        </div>
    @endif

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

</div>