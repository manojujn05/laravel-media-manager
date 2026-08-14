@php
    use Illuminate\Support\Facades\Storage;
@endphp

@if($previewAsset)
    @php
        // Fetch asset
        $asset = \Innopanda\AssetManager\Models\Asset::find($previewAsset);
    @endphp

    @if($asset)
        <div class="flex h-full flex-col justify-between bg-white dark:bg-gray-950 border-l border-gray-200/80 dark:border-gray-800/80 font-sans">
            
            {{-- Header --}}
            <div class="flex items-center justify-between border-b border-gray-100 dark:border-gray-900 px-6 py-4">
                <div class="flex items-center gap-2">
                    <span class="h-2 w-2 rounded-full bg-emerald-500 animate-pulse"></span>
                    <h2 class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">
                        Asset Inspector
                    </h2>
                </div>
                <button 
                    wire:click="$set('previewAsset', null)" 
                    type="button"
                    class="rounded-lg p-1 text-gray-400 hover:bg-gray-100 hover:text-gray-700 dark:hover:bg-gray-800 dark:hover:text-gray-200 transition-colors cursor-pointer"
                >
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            {{-- Image Preview Box --}}
            <div class="p-6 pb-2">
                <div class="relative aspect-video w-full overflow-hidden rounded-xl border border-gray-200/60 dark:border-gray-800 bg-gray-50/50 dark:bg-gray-900/50 flex items-center justify-center shadow-sm">
                   @php
                    $disk = $asset->disk ?? 'public';
                    
                    $assetUrl = $asset->getFirstMediaUrl('original', 'medium');
                    if (!$assetUrl) {
                        $assetUrl = $asset->getFirstMediaUrl('assets', 'medium');
                    }
                    if (!$assetUrl) {
                        if (filter_var($asset->path, FILTER_VALIDATE_URL)) {
                            $assetUrl = $asset->path;
                        } else {
                            $cleanPath = ltrim(str_replace('public/', '', $asset->path), '/');
                            $assetUrl = asset('storage/' . $cleanPath);
                        }
                    }

                    $extension = strtolower($asset->extension ?? pathinfo($asset->path, PATHINFO_EXTENSION));
                    $isImage = str_starts_with($asset->mime_type ?? '', 'image/') 
                        || in_array($extension, ['jpg', 'jpeg', 'png', 'webp', 'gif', 'svg']);
                @endphp

                    @if(($asset->mime_type && str_starts_with($asset->mime_type, 'image/')) || in_array(strtolower($asset->extension ?? ''), ['jpg', 'jpeg', 'png', 'webp', 'gif', 'svg']))
                        <img
                            src="{{ $assetUrl }}"
                            alt="{{ $asset->title ?? 'Asset Image' }}"
                            class="h-full w-full object-contain p-3"
                        >
                    @else
                        <div class="flex flex-col items-center p-4">
                            <div class="p-3 rounded-xl bg-indigo-50 dark:bg-indigo-950/40 text-indigo-600 dark:text-indigo-400 mb-2">
                                <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                            </div>
                            <span class="text-[11px] font-bold uppercase tracking-wider text-gray-500">{{ $asset->extension ?? 'File' }}</span>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Specs List --}}
            <div class="flex-1 overflow-y-auto px-6 py-4 space-y-1.5 text-xs">
                <div class="flex justify-between items-center py-0.5">
                    <span class="text-gray-500 dark:text-gray-400 font-medium">Title</span>
                    <span class="font-semibold text-gray-900 dark:text-gray-100 truncate max-w-[160px]">{{ $asset->title ?? '-' }}</span>
                </div>

                <div class="flex justify-between items-center py-0.5">
                    <span class="text-gray-500 dark:text-gray-400 font-medium">File Name</span>
                    <span class="font-mono text-[11px] text-gray-600 dark:text-gray-300 truncate max-w-[160px]">{{ basename($asset->path) }}</span>
                </div>
                <div class="flex justify-between items-center py-0.5">
                    <span class="text-gray-500 dark:text-gray-400 font-medium">Storage Disk</span>
                    <span class="inline-flex items-center rounded-md bg-gray-100 dark:bg-gray-800 px-2 py-0.5 text-[10px] font-semibold text-gray-600 dark:text-gray-300 border border-gray-200/60 dark:border-gray-700/60">
                        {{ $asset->disk ?? 'public' }}
                    </span>
                </div>

                <div class="flex justify-between items-center py-0.5">
                    <span class="text-gray-500 dark:text-gray-400 font-medium">MIME Type</span>
                    <span class="font-mono text-[11px] text-gray-600 dark:text-gray-300 truncate max-w-[160px]">{{ $asset->mime_type }}</span>
                </div>

                <div class="flex justify-between items-center py-0.5">
                    <span class="text-gray-500 dark:text-gray-400 font-medium">Extension</span>
                    <span class="font-semibold uppercase text-gray-900 dark:text-gray-100">{{ $asset->extension }}</span>
                </div>

                <div class="flex justify-between items-center py-0.5">
                    <span class="text-gray-500 dark:text-gray-400 font-medium">Size</span>
                    <span class="font-medium text-gray-900 dark:text-gray-100">{{ number_format($asset->size / 1024, 2) }} KB</span>
                </div>

                @if($asset->width && $asset->height)
                    <div class="flex justify-between items-center py-0.5">
                        <span class="text-gray-500 dark:text-gray-400 font-medium">Dimensions</span>
                        <span class="font-medium text-gray-900 dark:text-gray-100">{{ $asset->width }} × {{ $asset->height }} px</span>
                    </div>
                @endif

                <div class="flex justify-between items-center py-0.5">
                    <span class="text-gray-500 dark:text-gray-400 font-medium">Uploaded</span>
                    <span class="font-medium text-gray-900 dark:text-gray-100">{{ $asset->created_at?->format('d M Y, h:i A') }}</span>
                </div>

                <div class="flex justify-between items-center pt-1 border-t border-gray-100 dark:border-gray-900">
                    <span class="text-gray-500 dark:text-gray-400 font-medium">Alt Text</span>
                    <span class="font-medium text-gray-900 dark:text-gray-100 italic truncate max-w-[160px]">{{ $asset->alt ?: 'None' }}</span>
                </div>
            </div>

            {{-- SaaS Style Action Buttons --}}
            <div class="border-t border-gray-100 dark:border-gray-900 p-1 bg-gray-50/30 dark:bg-gray-900/20">
                @php
                    $fileUrl = function_exists('asset_url') ? asset_url($asset) : Storage::disk($asset->disk ?? 'public')->url($asset->path);
                @endphp

                <div class="flex flex-col gap-2.5">
                    {{-- Primary Buttons --}}
                    <div class="grid grid-cols-1 gap-2 p-1">
                        <button
                            type="button"
                            wire:click="selectAsset({{ $asset->id }})"
                            class="inline-flex cursor-pointer items-center justify-center gap-2 rounded-lg bg-green-600 hover:bg-green-700 active:bg-green-800 text-white px-3 py-2 text-xs font-semibold shadow-xs transition-all focus:outline-none focus:ring-2 focus:ring-green-500/20"
                        >
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            Select Asset
                        </button>
                    </div>

                    {{-- Secondary Action Buttons --}}
                    <div class="flex items-center gap-2 justify-center mt-2 flex-wrap">
                        <button
                            type="button"
                            wire:click="copyAssetUrl({{ $asset->id }})"
                            title="Copy URL"
                            aria-label="Copy URL"
                            class="inline-flex cursor-pointer items-center justify-center p-2 rounded-full bg-indigo-50 hover:bg-indigo-100 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400 border border-indigo-200/50 dark:border-indigo-900/50 transition-all focus:outline-none"
                        >
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                            </svg>
                        </button>
                        
                        <button
                            type="button"
                            wire:click="$dispatch('open-replace-drawer', { assetId: {{ $asset->id }} })"
                            title="Replace File"
                            class="inline-flex cursor-pointer items-center justify-center p-2 rounded-full bg-amber-50 hover:bg-amber-100 dark:bg-amber-900/30 text-amber-600 dark:text-amber-400 border border-amber-200/50 dark:border-amber-900/50 transition-all focus:outline-none"
                        >
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                            </svg>
                        </button>

                        <button
                            type="button"
                            wire:click="$dispatch('open-version-history-modal', { assetId: {{ $asset->id }} })"
                            title="Version History & Usage"
                            class="inline-flex cursor-pointer items-center justify-center p-2 rounded-full bg-teal-50 hover:bg-teal-100 dark:bg-teal-900/30 text-teal-600 dark:text-teal-400 border border-teal-200/50 dark:border-teal-900/50 transition-all focus:outline-none"
                        >
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </button>

                        <button
                            type="button"
                            wire:click="confirmDeleteAsset({{ $asset->id }})"
                            title="Delete File"
                            aria-label="Delete File"
                            class="inline-flex cursor-pointer items-center justify-center p-2 rounded-full bg-rose-50 hover:bg-rose-100/80 dark:bg-rose-950/30 dark:hover:bg-rose-900/40 text-rose-600 dark:text-rose-400 border border-rose-200/50 dark:border-rose-900/50 transition-all focus:outline-none"
                        >
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

        </div>
    @else
        <div class="flex h-full items-center justify-center text-xs font-medium text-gray-400">
            Asset details unavailable.
        </div>
    @endif
@else
    {{-- Clean SaaS Empty State --}}
    <div class="flex h-full flex-col items-center justify-center p-8 text-center bg-white dark:bg-gray-950 border-l border-gray-200/80 dark:border-gray-800/80">
        <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-gray-100 dark:bg-gray-900 text-gray-400 mb-3 border border-gray-200/50 dark:border-gray-800">
            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
            </svg>
        </div>
        <h3 class="text-xs font-bold uppercase tracking-wider text-gray-800 dark:text-gray-200">No Asset Selected</h3>
        <p class="mt-1 text-xs text-gray-400 dark:text-gray-500 max-w-[180px]">Select any asset from the main library to manage file details.</p>
    </div>
@endif

