<x-dynamic-component
    :component="$getFieldWrapperView()"
    :field="$field"
>
    <div 
        x-data="{ 
            state: $wire.$entangle(@js($getStatePath())),
            selectedAssets: @js($getDisplayUrls()),
            isOpen: false,
            removeImage(index = null) {
                if (index !== null && Array.isArray(this.state)) {
                    this.state.splice(index, 1);
                    this.selectedAssets.splice(index, 1);
                    // trigger reactivity
                    this.state = [...this.state];
                    this.selectedAssets = [...this.selectedAssets];
                } else {
                    this.state = @js($isMultiple() ? [] : null);
                    this.selectedAssets = [];
                }
            }
        }"
        x-on:asset-manager:image-selected.window="
            if (!@js($isMultiple()) && $event.detail.pickerId === '{{ $getId() }}') {
                state = @js($returnsIds()) ? $event.detail.id : $event.detail.url;
                selectedAssets = [{ id: $event.detail.id, url: $event.detail.url }];
                isOpen = false;
            }
        "
        x-on:asset-manager:assets-selected.window="
            if (@js($isMultiple()) && $event.detail.pickerId === '{{ $getId() }}') {
                const assets = $event.detail.assets || [];
                state = @js($returnsIds()) ? assets.map(a => a.id) : assets.map(a => a.url);
                selectedAssets = assets;
                isOpen = false;
            }
        "
        {{ $attributes->merge($getExtraAttributes())->class(['fi-fo-field-asset-picker space-y-3']) }}
    >
        
        <div x-show="selectedAssets.length > 0" x-cloak class="{{ $isMultiple() ? 'grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4' : 'flex items-center gap-3 p-3 border border-gray-200 dark:border-gray-700 rounded-lg bg-gray-50 dark:bg-gray-800' }}">
            
            <template x-for="(asset, index) in selectedAssets" :key="index">
                <div :class="'{{ $isMultiple() ? 'relative group flex flex-col overflow-hidden rounded-xl border border-gray-200/90 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-800 p-2' : 'flex items-center gap-3 w-full' }}'">
                    <div :class="'{{ $isMultiple() ? 'relative h-32 w-full flex items-center justify-center overflow-hidden rounded bg-gray-50 dark:bg-gray-900' : 'shrink-0 h-16 w-16 overflow-hidden rounded shadow-sm bg-white dark:bg-gray-900 border border-gray-100 dark:border-gray-700 flex items-center justify-center' }}'">
                        <img x-bind:src="asset.url" class="h-full w-full object-cover" alt="Selected Media" />
                    </div>
                    
                    <div :class="'{{ $isMultiple() ? 'mt-2 text-xs truncate text-gray-500' : 'flex-1 min-w-0' }}'">
                        <input 
                            type="text" 
                            readonly 
                            x-bind:value="asset.url" 
                            :class="'{{ $isMultiple() ? 'w-full bg-transparent border-0 p-0 text-xs text-gray-500 truncate focus:ring-0' : 'block w-full text-sm border-0 bg-transparent p-0 focus:ring-0 text-gray-700 dark:text-gray-300 truncate' }}'"
                        />
                    </div>

                    @if($isMultiple())
                        <button
                            type="button"
                            x-on:click.stop="removeImage(index)"
                            class="absolute top-1 right-1 flex h-6 w-6 items-center justify-center rounded-full bg-red-500 text-white shadow-sm hover:bg-red-600 transition-colors opacity-0 group-hover:opacity-100"
                        >
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    @endif
                </div>
            </template>
            
        </div>


        <div class="flex items-center gap-2">
            <button
                x-show="selectedAssets.length === 0"
                type="button"
                x-on:click="$dispatch('asset-picker-opened', { pickerId: '{{ $getId() }}', selection: selectedAssets.map(a => a.id).filter(id => id !== null) }); isOpen = true"
                class="shrink-0 px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 font-semibold shadow-sm transition-colors text-sm"
            >
                {{ $isMultiple() ? 'Select Images' : 'Select Image' }}
            </button>

            <button
                x-show="selectedAssets.length > 0"
                x-cloak
                type="button"
                x-on:click="$dispatch('asset-picker-opened', { pickerId: '{{ $getId() }}', selection: selectedAssets.map(a => a.id).filter(id => id !== null) }); isOpen = true"
                class="px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-700 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 font-semibold shadow-sm transition-colors text-sm"
            >
                {{ $isMultiple() ? 'Add / Change Images' : 'Change Image' }}
            </button>

            <button
                x-show="selectedAssets.length > 0"
                x-cloak
                type="button"
                x-on:click="removeImage()"
                class="px-4 py-2 bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-800 text-red-600 dark:text-red-400 rounded-lg hover:bg-red-100 dark:hover:bg-red-900/50 font-semibold shadow-sm transition-colors text-sm"
            >
                {{ $isMultiple() ? 'Clear All' : 'Remove Image' }}
            </button>
        </div>

        <div x-show="isOpen" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm p-4">
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
                        x-on:click="isOpen = false"
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
                        :picker-id="$getId()"
                        :multiple="$isMultiple()"
                        wire:key="media-browser-modal-{{ $getId() }}"
                    />
                </div>
            </div>
        </div>
    </div>
</x-dynamic-component>
