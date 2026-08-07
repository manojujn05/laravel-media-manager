<div class="space-y-4">
    <!-- Search Input -->
    <input
        wire:model.live="search"
        type="text"
        placeholder="Search assets..."
        class="w-full border rounded-lg p-2 border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100"
    />

    <!-- Assets Grid -->
    <div class="grid grid-cols-5 gap-4 mt-4 max-h-96 overflow-y-auto p-1">
        @foreach($assets as $asset)
            <div
                wire:click="selectAsset({{ $asset->id }})"
                class="cursor-pointer border rounded-lg p-2 relative transition-all hover:shadow-md 
                    {{ $selectedAsset === $asset->id ? 'border-primary-600 ring-2 ring-primary-600 bg-primary-50 dark:bg-primary-950/20' : 'border-gray-200 dark:border-gray-700' }}"
            >
                <img
                    src="{{ $asset->url }}"
                    alt="{{ $asset->title ?? 'Asset' }}"
                    class="h-24 w-full object-cover rounded-md"
                />
                
                <div class="mt-1 text-xs truncate text-gray-600 dark:text-gray-400 text-center">
                    {{ $asset->title ?: $asset->filename }}
                </div>
            </div>
        @endforeach
    </div>
</div>