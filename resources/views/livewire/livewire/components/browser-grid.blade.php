<div class="h-full flex flex-col">
    {{-- Grid --}}
    @if($assets->count())
        <div class="grid grid-cols-2 gap-5 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 2xl:grid-cols-6">
            @foreach($assets as $asset)
                <livewire:asset-manager.asset-card :asset="$asset" :key="'asset-'.$asset->id" />
            @endforeach
        </div>
        {{-- Pagination --}}
        <div class="mt-8">
            {{ $assets->links() }}
        </div>
    @else
        {{-- Empty State --}}
        <div class="flex h-[500px] flex-col items-center justify-center rounded-xl border border-dashed bg-white dark:border-gray-700 dark:bg-gray-800">
            <svg xmlns="http://www.w3.org/2000/svg" class="mb-4 h-20 w-20 text-gray-300" fill="none"
                viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round"  stroke-width="1.5"  d="M3 16l4-4 4 4 8-8"/>
            </svg>
            <h2 class="text-xl font-semibold">
                No Assets Found
            </h2>
            <p class="mt-2 text-sm text-gray-500">
                Upload your first asset to get started.
            </p>
            <button wire:click="$dispatch('open-upload-modal')" class="mt-6 rounded-lg bg-blue-600 px-5 py-2 text-white hover:bg-blue-700">
                Upload Asset
            </button>
        </div>
    @endif
</div>