<div class="h-full border-l bg-white dark:border-gray-700 dark:bg-gray-900">
    @if($asset)
        <div class="p-5">
            <img
                src="{{ $asset->getFirstMediaUrl('assets') ?: (str_starts_with($asset->path, 'http') ? $asset->path : Storage::disk($asset->disk ?? 'public')->url($asset->path)) }}"
                alt="{{ $asset->title }}"
                class="w-full h-48 rounded-lg border object-cover">

            <h2 class="mt-5 text-lg font-semibold">
                {{ $asset->title }}
            </h2>

            <div class="mt-6 space-y-3 text-sm">
                <div class="flex justify-between gap-4">
                    <span class="shrink-0">File Name</span>
                    <span class="text-right break-all">{{ $asset->title }}</span>
                </div>

                <div class="flex justify-between">
                    <span>Type</span>
                    <span>{{ strtoupper($asset->extension ?? pathinfo($asset->path, PATHINFO_EXTENSION)) }}</span>
                </div>

                <div class="flex justify-between">
                    <span>Size</span>
                    <span>{{ $asset->size ? number_format($asset->size / 1024, 1) : '0' }} KB</span>
                </div>

                <div class="flex justify-between">
                    <span>MIME</span>
                    <span>{{ $asset->mime_type }}</span>
                </div>

                <div class="flex justify-between">
                    <span>Dimensions</span>
                    <span>{{ $asset->width ?? 'N/A' }} × {{ $asset->height ?? 'N/A' }}</span>
                </div>

                <div class="flex justify-between">
                    <span>Uploaded</span>
                    <span>{{ $asset->created_at?->format('d M Y') }}</span>
                </div>
            </div>

            <div class="mt-8 space-y-3">
                <button
                    wire:click="selectAsset"
                    class="w-full rounded-lg bg-green-600 py-2 text-white hover:bg-green-700 font-bold shadow-sm transition-colors">
                    ✓ Select Asset
                </button>

                <button
                    wire:click="preview"
                    class="w-full rounded-lg bg-gray-600 py-2 text-white hover:bg-gray-700">
                    Preview
                </button>

                <button
                    wire:click="download"
                    class="w-full rounded-lg bg-blue-600 py-2 text-white hover:bg-blue-700">
                    Download
                </button>

                <button
                    wire:click="copyUrl"
                    class="w-full rounded-lg bg-indigo-600 py-2 text-white hover:bg-indigo-700">
                    Copy URL
                </button>

                <button
                    wire:click="replace"
                    class="w-full rounded-lg bg-yellow-500 py-2 text-white hover:bg-yellow-600">
                    Replace
                </button>

                <button
                    wire:click="delete"
                    wire:confirm="Delete this asset?"
                    class="w-full rounded-lg bg-red-600 py-2 text-white hover:bg-red-700">
                    Delete
                </button>
            </div>
        </div>
    @else
        <div class="flex h-full items-center justify-center">
            <div class="text-center">
                <svg
                    class="mx-auto h-16 w-16 text-gray-300"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24">
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="1.5"
                        d="M9 12h6m-3-3v6m9-3A9 9 0 1112 3a9 9 0 019 9z"/>
                </svg>
                <h3 class="mt-4 text-lg font-medium">
                    No Asset Selected
                </h3>
                <p class="mt-2 text-sm text-gray-500">
                    Select an asset to view details.
                </p>
            </div>
        </div>
    @endif
</div>