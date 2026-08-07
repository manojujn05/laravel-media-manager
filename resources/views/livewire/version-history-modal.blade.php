<div>
@if(($isOpen ?? false) && isset($asset))
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <!-- Backdrop -->
            <div class="fixed inset-0 bg-gray-900 bg-opacity-75 transition-opacity" wire:click="closeModal"></div>

            <div class="flex items-center justify-center min-h-screen p-4 text-center sm:p-0">
                <div class="relative bg-white rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:max-w-3xl sm:w-full">
                    
                    <!-- Header -->
                    <div class="bg-slate-900 text-white px-6 py-4 flex justify-between items-center">
                        <div class="flex items-center space-x-3">
                            <div class="p-2 bg-indigo-600 rounded-lg">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-lg font-bold leading-6">Version History</h3>
                                <p class="text-xs text-slate-400">{{ $asset->title }}</p>
                            </div>
                        </div>
                        <button wire:click="closeModal" class="text-slate-400 hover:text-white transition">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>

                    <!-- Flash Message -->
                    @if (session()->has('message'))
                        <div class="bg-emerald-50 border-l-4 border-emerald-500 p-4 mx-6 mt-4 rounded-md">
                            <p class="text-sm font-medium text-emerald-800">{{ session('message') }}</p>
                        </div>
                    @endif

                    <!-- Content -->
                    <div class="p-6 space-y-6">
                        <!-- Active Version Card -->
                        <div class="bg-indigo-50 border border-indigo-200 rounded-xl p-4 flex items-center justify-between">
                            <div class="flex items-center space-x-4">
                                <span class="px-3 py-1 text-xs font-extrabold uppercase tracking-wide bg-indigo-600 text-white rounded-full">
                                    Active Version
                                </span>
                                <div>
                                    <h4 class="text-sm font-bold text-gray-900">{{ $asset->title }}</h4>
                                    <p class="text-xs text-gray-500">
                                        Path: {{ $asset->path }} | Size: {{ number_format($asset->size / 1024, 2) }} KB
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Previous Versions List -->
                        <div>
                            <h4 class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-4">Old Versions</h4>
                            @if($asset->versions->isEmpty())
                                <div class="text-center py-8 text-gray-500 bg-slate-50 rounded-xl border border-dashed border-slate-200">
                                    No previous versions found for this asset.
                                </div>
                            @else
                                <div class="space-y-3">
                                    @foreach($asset->versions as $version)
                                        <div class="p-4 bg-slate-50 border border-slate-200 rounded-xl flex items-center justify-between hover:shadow-md transition">
                                            <div>
                                                <div class="flex items-center space-x-2">
                                                    <span class="text-xs font-bold px-2 py-0.5 bg-slate-200 text-slate-800 rounded">
                                                        Version #{{ $version->version_number }}
                                                    </span>
                                                    <span class="text-xs text-slate-500">
                                                        {{ $version->created_at->format('M d, Y • h:i A') }}
                                                    </span>
                                                </div>
                                                <p class="text-xs text-slate-600 mt-1">Path: {{ $version->path }}</p>
                                            </div>
                                            <button wire:click="rollback({{ $version->id }})" class="px-3 py-1.5 text-xs font-bold text-amber-700 bg-amber-50 border border-amber-300 rounded-lg hover:bg-amber-100 transition">
                                                Rollback
                                            </button>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Footer -->
                    <div class="bg-slate-50 px-6 py-4 flex justify-end">
                        <button wire:click="closeModal" class="px-5 py-2 text-sm font-semibold text-slate-700 bg-white border border-slate-300 rounded-xl hover:bg-slate-100 transition">
                            Close
                        </button>
                    </div>

                </div>
            </div>
        </div>
    @endif
</div>