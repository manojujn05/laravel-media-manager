<div class="flex items-center justify-between border-b border-gray-200 bg-white px-6 py-4 dark:border-gray-700 dark:bg-gray-800">

    <div class="flex items-center gap-4">

        <input
            type="text"
            wire:model.live.debounce.500ms="search"
            placeholder="Search assets..."
            class="w-96 rounded-lg border border-gray-300 px-4 py-2 focus:border-blue-500 focus:ring focus:ring-blue-200 dark:border-gray-600 dark:bg-gray-900 dark:text-white"
        >

        <button
            wire:click="openUploader"
            class="rounded-lg bg-blue-600 px-4 py-2 text-white transition hover:bg-blue-700">

            Upload

        </button>

    </div>

    <div class="flex items-center gap-3">

        <select
            wire:model.live="sort"
            class="rounded-lg border border-gray-300 px-3 py-2 dark:border-gray-600 dark:bg-gray-900 dark:text-white">

            <option value="latest">
                Latest
            </option>

            <option value="oldest">
                Oldest
            </option>

            <option value="name">
                Name
            </option>

            <option value="size">
                Size
            </option>

        </select>

        <button
            wire:click="gridView"
            class="rounded-lg border px-4 py-2 {{ $view === 'grid' ? 'bg-blue-600 text-white' : '' }}">

            Grid

        </button>

        <button
            wire:click="listView"
            class="rounded-lg border px-4 py-2 {{ $view === 'list' ? 'bg-blue-600 text-white' : '' }}">

            List

        </button>

    </div>

</div>