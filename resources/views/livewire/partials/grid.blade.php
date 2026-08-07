@php
    use Illuminate\Support\Facades\Storage;
@endphp

<div>
    {{-- Bulk Action Bar --}}
    @if(!empty($selectedAssets) && count($selectedAssets) > 0)
        <div class="sticky top-4 z-30 mb-6 flex items-center justify-between rounded-xl border border-gray-200 bg-white/95 px-5 py-3 shadow-xl backdrop-blur-md dark:border-gray-700 dark:bg-gray-800/95">
            <div class="flex items-center gap-3 font-medium text-gray-800 dark:text-gray-200">
                <span class="inline-flex h-7 w-7 items-center justify-center rounded-lg bg-indigo-600 text-xs font-bold text-white shadow-sm">
                    {{ count($selectedAssets) }}
                </span>
                <span class="text-sm font-semibold">Asset(s) Selected</span>
            </div>

            <div class="flex items-center gap-2">
                <button wire:click="bulkDownloadZip" class="inline-flex items-center gap-1.5 rounded-lg bg-indigo-600 px-4 py-2 text-xs font-semibold text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition">
                    📥 Download
                </button>
                <button wire:click="bulkDelete" wire:confirm="Are you sure you want to delete selected assets?" class="inline-flex items-center gap-1.5 rounded-lg bg-rose-600 px-4 py-2 text-xs font-semibold text-white shadow-sm hover:bg-rose-700 focus:outline-none focus:ring-2 focus:ring-rose-500 focus:ring-offset-2 transition">
                    🗑️ Delete
                </button>
                <button wire:click="clearSelection" class="rounded-lg border border-gray-300 bg-white px-4 py-2 text-xs font-semibold text-gray-700 shadow-sm hover:bg-gray-50 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 dark:hover:bg-gray-600 transition">
                    Cancel
                </button>
            </div>
        </div>
    @endif

    @if(isset($assets) && $assets->count())
        {{-- Grid columns adjusted: fewer columns per row so cards look wider and more prominent --}}
        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
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

                <div wire:key="asset-{{ $asset->id }}" class="group relative flex flex-col overflow-hidden rounded-2xl border border-gray-200/90 bg-white shadow-sm transition-all duration-300 hover:border-indigo-500/50 hover:shadow-xl dark:border-gray-700 dark:bg-gray-800">
                    
                    {{-- Asset Preview Container --}}
                    <div wire:click="preview({{ $asset->id }})" class="relative h-56 w-full cursor-pointer overflow-hidden bg-gradient-to-br from-gray-50 to-gray-100/60 dark:from-gray-900/40 dark:to-gray-900/80 p-4">
                        
                        <div class="absolute inset-0 opacity-30 bg-[radial-gradient(#94a3b8_1px,transparent_1px)] [background-size:14px_14px] dark:bg-[radial-gradient(#475569_1px,transparent_1px)]"></div>

                        @if($isImage)
                            <div class="relative flex h-full w-full items-center justify-center">
                                <img 
                                    src="{{ $assetUrl }}" 
                                    alt="{{ $asset->title ?? 'Asset Image' }}"
                                    loading="lazy"
                                    class="max-h-full max-w-full object-contain drop-shadow-md transition-transform duration-300 group-hover:scale-105"
                                >
                            </div>
                        @else
                            <div class="relative flex h-full flex-col items-center justify-center text-center">
                                <svg class="h-16 w-16 text-indigo-500/70" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                                </svg>
                                <span class="mt-2 rounded-md bg-indigo-50 px-2.5 py-1 text-[11px] font-bold uppercase tracking-wider text-indigo-600 dark:bg-indigo-950/50 dark:text-indigo-400">{{ $asset->extension ?? 'FILE' }}</span>
                            </div>
                        @endif

                        {{-- Checkbox Top Left --}}
                        <div class="absolute left-3.5 top-3.5 z-10" onclick="event.stopPropagation()">
                            <input 
                                type="checkbox" 
                                value="{{ $asset->id }}"
                                wire:model.live="selectedAssets"
                                class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500 cursor-pointer shadow-sm transition"
                            >
                        </div>

                        {{-- Favorite Star Top Right --}}
                        <button 
                            type="button"
                            wire:click.stop="toggleFavorite({{ $asset->id }})"
                            class="absolute right-3.5 top-3.5 z-10 rounded-xl bg-white/90 p-2 text-xs shadow-sm backdrop-blur hover:bg-white dark:bg-gray-800/90 dark:hover:bg-gray-800 transition"
                            title="Toggle Favorite"
                        >
                            {{ $asset->is_favorite ? '⭐' : '☆' }}
                        </button>
                    </div>

                    {{-- Card Footer Info & SaaS Buttons --}}
                    <div class="flex flex-col justify-between flex-grow p-4 bg-white dark:bg-gray-800">
                        <div>
                            <div class="truncate text-sm font-semibold text-gray-900 dark:text-gray-100 mb-3" title="{{ $asset->title }}">
                                {{ $asset->title ?? basename($asset->path) }}
                            </div>
                        </div>

                        {{-- Professional Action Buttons --}}
                        <div class="grid grid-cols-2 gap-2 mb-3.5">
                            <button 
                                type="button"
                                wire:click.stop="preview({{ $asset->id }})"
                                class="inline-flex items-center justify-center gap-1.5 rounded-xl border border-gray-200 bg-white px-3 py-2 text-xs font-medium text-gray-700 shadow-sm hover:bg-gray-50 hover:border-gray-300 dark:border-gray-700 dark:bg-gray-700 dark:text-gray-200 dark:hover:bg-gray-600 transition"
                            >
                                👁 Preview
                            </button>
                            
                            <a 
                                href="{{ $assetUrl }}" 
                                download
                                onclick="event.stopPropagation()"
                                class="inline-flex items-center justify-center gap-1.5 rounded-xl border border-gray-200 bg-white px-3 py-2 text-xs font-medium text-gray-700 shadow-sm hover:bg-gray-50 hover:border-gray-300 dark:border-gray-700 dark:bg-gray-700 dark:text-gray-200 dark:hover:bg-gray-600 transition text-center"
                            >
                                ⬇ Download
                            </a>

                            <button 
                                type="button"
                                x-data
                                x-on:click.stop="navigator.clipboard.writeText('{{ $assetUrl }}'); alert('URL Copied successfully!')"
                                class="inline-flex items-center justify-center gap-1.5 rounded-xl border border-gray-200 bg-white px-3 py-2 text-xs font-medium text-gray-700 shadow-sm hover:bg-gray-50 hover:border-gray-300 dark:border-gray-700 dark:bg-gray-700 dark:text-gray-200 dark:hover:bg-gray-600 transition"
                            >
                                📋 Copy URL
                            </button>

                            <button 
                                type="button"
                                wire:click.stop="deleteAsset({{ $asset->id }})"
                                wire:confirm="Are you sure you want to delete this asset?"
                                class="inline-flex items-center justify-center gap-1.5 rounded-xl border border-rose-100 bg-rose-50/60 px-3 py-2 text-xs font-medium text-rose-600 shadow-sm hover:bg-rose-100 dark:border-rose-900/40 dark:bg-rose-950/30 dark:text-rose-400 dark:hover:bg-rose-900/50 transition"
                            >
                                🗑️ Delete
                            </button>
                        </div>

                        {{-- Metadata Bottom Info --}}
                        <div class="flex items-center justify-between border-t border-gray-100 pt-3 text-[11px] text-gray-500 dark:border-gray-700/60 dark:text-gray-400">
                            <span class="rounded bg-gray-100 px-1.5 py-0.5 font-bold uppercase tracking-wider text-gray-600 dark:bg-gray-700 dark:text-gray-300">{{ $asset->extension ?? 'IMG' }}</span>
                            <span class="font-medium">{{ $asset->size ? number_format($asset->size / 1024, 1) . ' KB' : '' }}</span>
                            <span class="font-medium">{{ $asset->created_at?->diffForHumans(null, true) }}</span>
                        </div>
                    </div>

                </div>
            @endforeach
        </div>

        <div class="mt-8">
            {{ $assets->links() }}
        </div>
    @else
        <div class="flex h-[400px] flex-col items-center justify-center rounded-2xl border-2 border-dashed border-gray-200 bg-gray-50/50 dark:border-gray-700 dark:bg-gray-800/50">
            <div class="rounded-full bg-gray-100 p-4 dark:bg-gray-700/50 mb-3">
                <svg class="h-8 w-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
            </div>
            <h2 class="text-base font-semibold text-gray-700 dark:text-gray-200">No Assets Found</h2>
            <p class="text-xs text-gray-400 mt-1">Try uploading new files or clearing active search filters.</p>
        </div>
    @endif
</div>