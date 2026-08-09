<div class="h-full flex bg-gray-50/50 dark:bg-gray-950 font-sans relative antialiased text-gray-800 dark:text-gray-100">

    <!-- Sidebar -->
    <aside class="w-56 border-r border-gray-200/80 dark:border-gray-800/80 bg-white dark:bg-gray-900 shrink-0 shadow-xs z-10">
        <livewire:asset-manager.browser-sidebar :active-folder="$folder" />
    </aside>

    <!-- Main Content Area -->
    <div class="flex-1 flex flex-col min-w-0 bg-gray-50/50 dark:bg-gray-950">

        <!-- Top Navigation Bar / Header Partial -->
        @include('asset-manager::livewire.partials.navbar')

        <!-- Main Workspace Area -->
        <div class="flex flex-1 overflow-hidden">

            <!-- Assets & Folders Grid Scroll Area -->
            <main class="flex-1 overflow-y-auto p-6 flex flex-col">

                {{-- Styled Breadcrumb Navigation Bar --}}
                <nav class="flex items-center gap-2 text-xs font-medium text-gray-500 dark:text-gray-400 mb-6 bg-white dark:bg-gray-900 p-2.5 rounded-xl border border-gray-200/70 dark:border-gray-800/80 shadow-2xs">
                    <button 
                        type="button" 
                        wire:click="selectFolder(null)" 
                        class="flex items-center gap-1.5 hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors font-semibold"
                    >
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                        </svg>
                        <span>All Assets</span>
                    </button>

                    @if($currentFolder)
                        <svg class="w-3.5 h-3.5 text-gray-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                        <span class="font-bold text-gray-900 dark:text-gray-100 bg-gray-100 dark:bg-gray-800 px-2 py-0.5 rounded-md">
                            {{ $currentFolder->name }}
                        </span>
                    @endif

                </nav>

                {{-- Sub-Folders Section --}}
                @if($folders->count() > 0)
                    <div class="mb-6">
                        <h4 class="text-[11px] font-bold text-gray-400 dark:text-gray-500 uppercase tracking-wider mb-3">Folders</h4>
                        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-3">
                            @foreach($folders as $f)
                                <button
                                    type="button"
                                    wire:click="selectFolder({{ $f->id }})"
                                    class="flex items-center gap-3 p-3 rounded-xl border border-gray-200/80 dark:border-gray-800 bg-white dark:bg-gray-900 hover:border-indigo-500 dark:hover:border-indigo-500 hover:shadow-sm transition-all text-left group"
                                >
                                    <svg class="w-5 h-5 text-amber-500 shrink-0 group-hover:scale-110 transition-transform" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M2 6a2 2 0 012-2h4l2 2h4a2 2 0 012 2v8a2 2 0 01-2 2H4a2 2 0 01-2-2V6z"/>
                                    </svg>
                                    <span class="text-xs font-semibold text-gray-700 dark:text-gray-200 truncate">{{ $f->name }}</span>
                                </button>
                            @endforeach
                        </div>
                    </div>
                @endif

                {{-- Assets Display View --}}
                @if($assets->count() > 0)
                    @if($view === 'list')
                        @include('asset-manager::livewire.partials.list')
                    @else
                        @include('asset-manager::livewire.partials.grid')
                    @endif
                @else
                    {{-- Empty State --}}
                    <div class="flex-1 flex flex-col items-center justify-center py-16 text-center border-2 border-dashed border-gray-200 dark:border-gray-800/80 rounded-2xl bg-white/40 dark:bg-gray-900/40 my-auto">
                        <div class="p-4 rounded-full bg-indigo-50 dark:bg-indigo-950/30 text-indigo-500 mb-3 border border-indigo-100 dark:border-indigo-900/50">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        
                        <h4 class="text-xs font-bold text-gray-800 dark:text-gray-200 uppercase tracking-wider">
                            {{ $search || $favorites ? 'No Assets Found' : ($currentFolder ? $currentFolder->name . ' is Empty' : 'No Assets') }}
                        </h4>
                        <p class="text-xs text-gray-400 mt-1 mb-5 max-w-sm">
                            {{ $search || $favorites ? 'Try adjusting your search terms or active filters.' : 'There are no files uploaded in this directory yet.' }}
                        </p>

                        <div class="flex items-center gap-3">
                            @if($search || $type || $favorites || $folder || $sort !== 'latest')
                                <button 
                                    wire:click="resetFilters" 
                                    type="button" 
                                    class="inline-flex items-center gap-1.5 px-4 py-2 bg-gray-100 dark:bg-gray-800 hover:bg-gray-200 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-xl text-xs font-semibold transition-all active:scale-[0.98]"
                                >
                                    Clear Filters
                                </button>
                            @endif

                            <label class="cursor-pointer inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 active:scale-[0.98] text-white rounded-xl text-xs font-semibold transition-all shadow-xs">
                                <svg class="w-4 h-4 stroke-[2.5]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                                </svg>
                                <span>Upload File</span>
                                <input type="file" wire:model="file" class="hidden" />
                            </label>
                        </div>
                    </div>
                @endif

            </main>

            <!-- Asset Preview Sidebar -->
            <aside class="w-72 border-l border-gray-200/80 dark:border-gray-800/80 bg-white dark:bg-gray-900 shrink-0 shadow-xs">
                @include('asset-manager::livewire.partials.preview')
            </aside>

        </div>

    </div>



    {{-- Bulk Action Partials --}}
    @include('asset-manager::livewire.partials.bulk-action-bar')
    @include('asset-manager::livewire.partials.bulk-modals')
    <livewire:asset-manager.create-folder />

    @script
    <script>
        $wire.on('copy-to-clipboard', (event) => {
            let url = event.url;
            if (Array.isArray(event) && event[0].url) {
                url = event[0].url;
            }
            if (url) {
                navigator.clipboard.writeText(url);
            }
        });
    </script>
    @endscript
</div>