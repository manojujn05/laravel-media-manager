<div
    class="group overflow-hidden rounded-xl border bg-white shadow-sm transition duration-300 hover:-translate-y-1 hover:shadow-lg dark:border-gray-700 dark:bg-gray-800">

    <!-- Thumbnail -->

    <div
        wire:click="preview"
        class="relative aspect-square cursor-pointer overflow-hidden bg-gray-100">

        @if($asset->hasMedia('assets'))

            <img
                src="{{ asset_thumbnail($asset) }}"
                alt="{{ $asset->title }}"
                class="h-full w-full object-cover transition duration-300 group-hover:scale-105">

        @else

            <div class="flex h-full items-center justify-center">

                <svg
                    xmlns="http://www.w3.org/2000/svg"
                    class="h-16 w-16 text-gray-300"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor">

                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="1.5"
                        d="M3 7l9-4 9 4v10l-9 4-9-4V7z"/>

                </svg>

            </div>

        @endif

        <!-- Checkbox -->

        <div class="absolute left-3 top-3">

            <input
                type="checkbox"
                wire:click.stop="toggleSelection"
                @checked($selected)
                class="h-5 w-5 rounded">

        </div>

        <!-- Favorite -->

        <button
            wire:click.stop="toggleFavorite"
            class="absolute right-3 top-3 rounded-full bg-white/80 p-2 backdrop-blur">

            @if($asset->is_favorite)

                ⭐

            @else

                ☆

            @endif

        </button>

        <!-- Hover Overlay -->

        <div
            class="absolute inset-0 hidden items-center justify-center bg-black/40 group-hover:flex">

            <button
                wire:click.stop="preview"
                class="rounded-lg bg-white px-4 py-2 text-sm font-medium">

                Preview

            </button>

        </div>

    </div>

    <!-- Footer -->

    <div class="p-3">

        <h3
            class="truncate font-medium text-gray-800 dark:text-white">

            {{ $asset->title }}

        </h3>

        <div
            class="mt-2 flex items-center justify-between text-xs text-gray-500">

            <span>

                {{ strtoupper($asset->extension) }}

            </span>

            <span>

                {{ number_format($asset->size / 1024,1) }} KB

            </span>

        </div>

    </div>

</div>