<div>
    <div class="{{ $wrapperClass ?: 'space-y-3' }}">
        @if($selectedAssetUrl)
            <div class="flex items-center gap-3 p-3 border border-gray-200 dark:border-gray-700 rounded-lg bg-gray-50 dark:bg-gray-800">
                @if($showPreview)
                    <div class="shrink-0 h-16 w-16 overflow-hidden rounded shadow-sm bg-white dark:bg-gray-900 border border-gray-100 dark:border-gray-700 flex items-center justify-center">
                        <img src="{{ $selectedAssetUrl }}" class="h-full w-full object-cover" alt="Selected Media" />
                    </div>
                @endif
                
                @if($showUrl)
                    <div class="flex-1 min-w-0">
                        <input 
                            type="text" 
                            readonly 
                            wire:model="selectedAssetUrl" 
                            class="block w-full text-sm border-0 bg-transparent p-0 focus:ring-0 text-gray-700 dark:text-gray-300 truncate"
                        />
                    </div>
                @endif
            </div>

            <div class="flex items-center gap-2">
                <button
                    type="button"
                    wire:click.prevent="open"
                    class="{{ $buttonClass ?: 'px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-700 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 font-semibold shadow-sm transition-colors text-sm' }}"
                >
                    Change
                </button>

                @if($showRemove)
                    <button
                        type="button"
                        wire:click.prevent="removeMedia"
                        class="px-4 py-2 bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-800 text-red-600 dark:text-red-400 rounded-lg hover:bg-red-100 dark:hover:bg-red-900/50 font-semibold shadow-sm transition-colors text-sm"
                    >
                        Remove Image
                    </button>
                @endif
            </div>
        @else
            <div class="flex items-center gap-2">
                <button
                    type="button"
                    wire:click.prevent="open"
                    class="{{ $buttonClass ?: 'shrink-0 px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 font-semibold shadow-sm transition-colors text-sm' }}"
                >
                    {{ $buttonLabel }}
                </button>
            </div>
        @endif
    </div>

    @if($isOpen)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm p-4">
            
            <div class="bg-white dark:bg-gray-950 rounded-2xl shadow-2xl w-full max-w-[95vw] h-[95vh] flex flex-col overflow-hidden border border-gray-200 dark:border-gray-800">
                
                {{-- Modal Header --}}
                <div class="relative z-10 flex justify-between items-center px-6 py-4 border-b border-gray-100 dark:border-gray-800 bg-white dark:bg-gray-900 shrink-0">
                    <h2 class="font-bold text-lg text-gray-800 dark:text-gray-100 flex items-center gap-2">
                        <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        Media Library
                    </h2>

                    <button
                        type="button"
                        wire:click="$set('isOpen', false)"
                        title="Close Media Library"
                        aria-label="Close Media Library"
                        class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 transition-colors p-1 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800"
                    >
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                {{-- Modal Body: Inner Media Browser --}}
                <div class="flex-1 overflow-hidden relative">
                    <livewire:asset-manager.media-browser
                        :collection="$collection"
                        :picker-id="$this->getId()"
                        wire:key="media-browser-modal-{{ $this->getId() }}"
                    />
                </div>
                
            </div>
        </div>
    @endif

    @script
    <script>
        window.addEventListener('asset-manager:image-selected', event => {
            const detail = event.detail;

            if (!detail) {
                return;
            }

            if (detail.pickerId !== $wire.id) {
                return;
            }

            $wire.handleAssetSelected(
                Number(detail.id),
                detail.url
            );
        });
    </script>
    @endscript
</div>
