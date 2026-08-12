@php
    use Illuminate\Support\Facades\Storage;
@endphp

<div>
    @if(isset($assets) && $assets->count())
        {{-- Grid columns adjusted for wider cards --}}
        <div class="grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5">
            @foreach($assets as $asset)
                @php
                    $disk = $asset->disk ?? 'public';
                    
                    if (filter_var($asset->path, FILTER_VALIDATE_URL)) {
                        $assetUrl = $asset->path;
                    } else {
                        $cleanPath = ltrim(str_replace('public/', '', $asset->path), '/');
                        $assetUrl = asset('storage/' . $cleanPath);
                    }

                    $extension = strtolower($asset->extension ?? pathinfo($asset->path, PATHINFO_EXTENSION));
                    $isImage = str_starts_with($asset->mime_type ?? '', 'image/') 
                        || in_array($extension, ['jpg', 'jpeg', 'png', 'webp', 'gif', 'svg'])
                        || in_array($mime ?? '', ['application/octet-stream', 'binary/octet-stream']) && in_array($extension, ['jpg', 'jpeg', 'png', 'webp', 'gif', 'svg']);
                @endphp

                <div wire:key="asset-{{ $asset->id }}" class="group relative flex flex-col overflow-hidden rounded-xl border border-gray-200/90 bg-white shadow-sm transition-all duration-300 hover:border-indigo-500/50 hover:shadow-lg dark:border-gray-700 dark:bg-gray-800">
                    
                    {{-- Asset Preview Container with reduced height (h-32) to prevent page scrolling and fill card properly --}}
                    <div wire:click="preview({{ $asset->id }})" class="relative h-32 w-full cursor-pointer overflow-hidden bg-gradient-to-br from-gray-50 to-gray-100/60 dark:from-gray-900/40 dark:to-gray-900/80 flex items-center justify-center">
                        
                        @if($isImage)
                            <div class="relative h-full w-full flex items-center justify-center">
                                <img 
                                    src="{{ $assetUrl }}" 
                                    alt="{{ $asset->title ?? 'Asset Image' }}"
                                    loading="lazy"
                                    class="h-full w-full object-cover drop-shadow-sm transition-transform duration-300 group-hover:scale-105"
                                >
                            </div>
                        @else
                            <div class="relative flex h-full flex-col items-center justify-center text-center p-2">
                                <svg class="h-10 w-10 text-indigo-500/70" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                                </svg>
                                <span class="mt-1 rounded-md bg-indigo-50 px-2 py-0.5 text-[9px] font-bold uppercase tracking-wider text-indigo-600 dark:bg-indigo-950/50 dark:text-indigo-400">
                                    {{ $asset->extension ?? 'FILE' }}
                                </span>
                            </div>
                        @endif

                        {{-- Checkbox Top Left --}}
                    {{-- Checkbox Top Left --}}
<div class="absolute left-2.5 top-2.5 z-0 p-1 rounded" onclick="event.stopPropagation()">
    <input 
        type="checkbox" 
        value="{{ $asset->id }}"
        wire:model.live="selectedAssets"
        class="h-3.5 w-3.5 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500 cursor-pointer shadow-sm transition"
    >
</div>
                    </div>

                    {{-- Card Footer Info --}}
                    <div class="flex flex-col justify-between flex-grow p-2.5 bg-white dark:bg-gray-800 border-t border-gray-100 dark:border-gray-700/60">
                        <div 
                            class="text-xs font-medium truncate text-gray-900 dark:text-gray-100" 
                            title="{{ basename($asset->path) }}"
                        >
                            {{ basename($asset->path) }}
                        </div>

                        <button
                            type="button"
                            wire:click="selectAsset({{ $asset->id }})"
                            class="mt-1.5 w-full rounded-md bg-indigo-600 px-2 py-1 text-[11px] font-bold text-white hover:bg-indigo-700 shadow-sm transition-colors"
                        >
                            Select
                        </button>
                    </div>

                </div>
            @endforeach
        </div>

        <div class="mt-6">
            {{ $assets->links() }}
        </div>
    @else
        <div class="flex h-[350px] flex-col items-center justify-center rounded-2xl border-2 border-dashed border-gray-200 bg-gray-50/50 dark:border-gray-700 dark:bg-gray-800/50">
            <div class="rounded-full bg-gray-100 p-4 dark:bg-gray-700/50 mb-3">
                <svg class="h-8 w-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
            </div>

            <h2 class="text-base font-semibold text-gray-700 dark:text-gray-200">
                No Assets Found
            </h2>

            <p class="text-xs text-gray-400 mt-1">
                Try uploading new files or clearing active search filters.
            </p>
        </div>
    @endif
</div>