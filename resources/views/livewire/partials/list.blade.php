@php
    use Illuminate\Support\Facades\Storage;
@endphp

<div class="w-full">
    <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm dark:border-gray-800 dark:bg-gray-900">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-800">
            <thead class="bg-gray-50 dark:bg-gray-800/50">
                <tr>
                    <th scope="col" class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400">
                        <span class="sr-only">Select</span>
                    </th>
                    <th scope="col" class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400">Thumbnail</th>
                    <th scope="col" class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400">File Name</th>
                    <th scope="col" class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400">Type</th>
                    <th scope="col" class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400">Size</th>
                    <th scope="col" class="px-4 py-3 text-right text-xs font-semibold text-gray-500 dark:text-gray-400">Uploaded</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 bg-white dark:divide-gray-800 dark:bg-gray-900">
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
                            || in_array($extension, ['jpg', 'jpeg', 'png', 'webp', 'gif', 'svg']);
                    @endphp

                    <tr 
                        wire:key="asset-list-{{ $asset->id }}" 
                        wire:click="preview({{ $asset->id }})"
                        class="group cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-800/60 transition-colors {{ $previewAsset == $asset->id ? 'bg-indigo-50/50 dark:bg-indigo-900/10' : '' }}"
                    >
                        <td class="whitespace-nowrap px-4 py-3 w-12" onclick="event.stopPropagation()">
                            <input 
                                type="checkbox" 
                                value="{{ $asset->id }}"
                                wire:model.live="selectedAssets"
                                class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500 cursor-pointer transition"
                            >
                        </td>
                        
                        <td class="whitespace-nowrap px-4 py-3 w-20">
                            <div class="h-10 w-10 overflow-hidden rounded bg-gray-100 flex items-center justify-center dark:bg-gray-800">
                                @if($isImage)
                                    <img src="{{ $assetUrl }}" alt="{{ $asset->title }}" class="h-full w-full object-cover">
                                @else
                                    <span class="text-[10px] font-bold text-gray-400">{{ strtoupper($extension) }}</span>
                                @endif
                            </div>
                        </td>
                        
                        <td class="whitespace-nowrap px-4 py-3">
                            <div class="text-sm font-medium text-gray-900 dark:text-gray-100 truncate max-w-xs" title="{{ $asset->title ?? basename($asset->path) }}">
                                {{ $asset->title ?? basename($asset->path) }}
                            </div>
                        </td>
                        
                        <td class="whitespace-nowrap px-4 py-3">
                            <span class="inline-flex items-center rounded-md bg-gray-100 px-2 py-0.5 text-xs font-medium text-gray-600 dark:bg-gray-800 dark:text-gray-400">
                                {{ strtoupper($extension) }}
                            </span>
                        </td>
                        
                        <td class="whitespace-nowrap px-4 py-3 text-sm text-gray-500 dark:text-gray-400">
                            {{ $asset->size ? number_format($asset->size / 1024, 1) . ' KB' : '-' }}
                        </td>

                        <td class="whitespace-nowrap px-4 py-3 text-right text-sm text-gray-500 dark:text-gray-400">
                            {{ $asset->created_at?->format('M d, Y') }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="mt-6">
        {{ $assets->links() }}
    </div>
</div>
