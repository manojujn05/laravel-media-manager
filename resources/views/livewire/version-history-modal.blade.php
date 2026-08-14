<div>
    @if($isOpen && $asset)
        <div class="fixed inset-0 z-50 overflow-hidden" aria-labelledby="slide-over-title" role="dialog" aria-modal="true">
            <div class="fixed inset-0 bg-gray-900 bg-opacity-75 transition-opacity" wire:click="closeModal"></div>

            <div class="fixed inset-y-0 right-0 pl-10 max-w-full flex">
                <div class="w-screen max-w-lg bg-white dark:bg-gray-900 shadow-2xl flex flex-col justify-between">
                    
                    <!-- Header -->
                    <div class="bg-slate-900 px-6 py-5 flex items-center justify-between">
                        <h3 class="text-base font-bold text-white">Version History & Usage</h3>
                        <button wire:click="closeModal" class="text-slate-400 hover:text-white transition">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>

                    <div class="flex-1 overflow-y-auto p-6 space-y-8">
                        <!-- Usages Section -->
                        <div>
                            <h4 class="text-sm font-bold text-gray-500 uppercase tracking-wider mb-4 border-b pb-2 dark:border-gray-700">Active Usages</h4>
                            @if($asset->usages->count() > 0)
                                <ul class="space-y-3">
                                    @foreach($asset->usages as $usage)
                                        <li class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-800 rounded-lg border border-gray-100 dark:border-gray-700">
                                            <div>
                                                <span class="font-bold text-gray-800 dark:text-gray-200">{{ class_basename($usage->usable_type) }}</span>
                                                <span class="text-xs text-gray-500 block">ID: {{ $usage->usable_id }}</span>
                                            </div>
                                            <span class="text-xs bg-indigo-100 text-indigo-800 dark:bg-indigo-900 dark:text-indigo-200 px-2 py-1 rounded">
                                                {{ $usage->field }}
                                            </span>
                                        </li>
                                    @endforeach
                                </ul>
                            @else
                                <p class="text-sm text-gray-500 italic">This asset is not currently linked to any records.</p>
                            @endif
                        </div>

                        <!-- Version History Section -->
                        <div>
                            <h4 class="text-sm font-bold text-gray-500 uppercase tracking-wider mb-4 border-b pb-2 dark:border-gray-700">Version History</h4>
                            @if($asset->versions->count() > 0)
                                <div class="space-y-4 relative before:absolute before:inset-0 before:ml-5 before:-translate-x-px md:before:mx-auto md:before:translate-x-0 before:h-full before:w-0.5 before:bg-gradient-to-b before:from-transparent before:via-slate-300 before:to-transparent">
                                    @foreach($asset->versions->sortByDesc('version') as $version)
                                        <div class="relative flex items-center justify-between md:justify-normal md:odd:flex-row-reverse group is-active">
                                            <div class="flex items-center justify-center w-10 h-10 rounded-full border border-white bg-slate-100 text-slate-500 shadow shrink-0 md:order-1 md:group-odd:-translate-x-1/2 md:group-even:translate-x-1/2 z-10 font-bold">
                                                v{{ $version->version }}
                                            </div>
                                            <div class="w-[calc(100%-4rem)] md:w-[calc(50%-2.5rem)] p-4 rounded border border-slate-200 bg-white dark:bg-gray-800 dark:border-gray-700 shadow-sm">
                                                <div class="flex items-center justify-between mb-1">
                                                    <div class="font-bold text-slate-800 dark:text-gray-200">Version {{ $version->version }}</div>
                                                    <time class="text-xs font-medium text-slate-500">{{ $version->created_at->format('M d, Y h:i A') }}</time>
                                                </div>
                                                <div class="text-slate-500 text-xs mb-3">
                                                    {{ $version->mime_type }} &bull; {{ number_format($version->size / 1024, 2) }} KB
                                                </div>
                                                <button wire:click="restoreVersion({{ $version->id }})" wire:confirm="Are you sure you want to restore this version? The current active file will be archived as a new version." class="text-xs font-semibold text-indigo-600 hover:text-indigo-800 dark:text-indigo-400">
                                                    Restore this version
                                                </button>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <p class="text-sm text-gray-500 italic">No previous versions exist for this asset.</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
