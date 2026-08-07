<!-- Top Navigation Bar / Header -->
<header class="sticky top-0 z-20 flex items-center justify-between border-b border-gray-200/80 dark:border-gray-800/80 bg-white/80 dark:bg-gray-900/80 backdrop-blur-md px-6 py-3 shadow-xs">

    <!-- Left Group: Search & Primary Action Tools -->
    <div class="flex items-center gap-3">
        
        <!-- Search Input with Keyboard Shortcut Badge -->
        <div class="relative w-64 sm:w-80 group">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400 group-focus-within:text-indigo-600 dark:group-focus-within:text-indigo-400 transition-colors">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5A7 7 0 111 10a7 7 0 0114 0z"/>
                </svg>
            </div>
            <input
                type="text"
                wire:model.live.debounce.400ms="search"
                placeholder="Search assets..."
                class="w-full rounded-lg border border-gray-200 dark:border-gray-800 bg-gray-50/50 dark:bg-gray-950/50 pl-9 pr-12 py-1.5 text-xs font-medium text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:bg-white dark:focus:bg-gray-950 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 dark:focus:border-indigo-500 transition-all"
            />
            <div class="absolute inset-y-0 right-0 pr-2.5 flex items-center pointer-events-none">
                <kbd class="hidden sm:inline-flex items-center px-1.5 py-0.5 text-[10px] font-sans font-semibold text-gray-400 dark:text-gray-500 bg-white dark:bg-gray-900 rounded border border-gray-200 dark:border-gray-800 shadow-2xs">⌘K</kbd>
            </div>
        </div>

        <!-- Select All Checkbox Badge -->
        <label class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg border border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-900/60 text-xs font-medium text-gray-700 dark:text-gray-300 cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-800/50 hover:border-gray-300 dark:hover:border-gray-700 transition-all select-none shadow-2xs">
            <input 
                type="checkbox" 
                wire:model.live="selectAll" 
                class="rounded border-gray-300 dark:border-gray-700 text-indigo-600 focus:ring-2 focus:ring-indigo-500/20 h-3.5 w-3.5 cursor-pointer accent-indigo-600"
            />
            <span>Select All</span>
        </label>

        <!-- Vertical Separator -->
        <div class="h-4 w-px bg-gray-200 dark:bg-gray-800 hidden sm:block"></div>

        <!-- Primary Upload File Button -->
        <label class="cursor-pointer rounded-lg bg-indigo-600 hover:bg-indigo-500 active:bg-indigo-700 text-white px-3.5 py-1.5 text-xs font-semibold shadow-xs shadow-indigo-600/20 transition-all inline-flex items-center gap-1.5 select-none active:scale-[0.98]">
            <svg class="w-3.5 h-3.5 stroke-[2.5]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
            </svg>
            <span>Upload File</span>
            <input type="file" wire:model="file" class="hidden" />
        </label>

        {{-- Reset Filters Button --}}
        @if($search || $type || $favorites || $folder || $tag || $sort !== 'latest')
            <button
                wire:click="resetFilters"
                type="button"
                class="inline-flex items-center gap-1.5 px-2.5 py-1.5 text-xs font-medium text-rose-600 dark:text-rose-400 bg-rose-50/80 dark:bg-rose-950/40 hover:bg-rose-100 dark:hover:bg-rose-900/60 rounded-lg border border-rose-200/60 dark:border-rose-800/40 transition-all active:scale-[0.98]"
                title="Clear all active search and filters"
            >
                <svg class="w-3.5 h-3.5 stroke-[2.5]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
                <span>Reset Filters</span>
            </button>
        @endif
    </div>

    <!-- Right Group: View controls & Sort -->
    <div class="flex items-center gap-2.5">

        <!-- View Toggle Buttons (Sleek Segmented Control) -->
        <div class="inline-flex p-0.5 rounded-lg bg-gray-100/80 dark:bg-gray-800/60 border border-gray-200/80 dark:border-gray-800">
            <button
                wire:click="gridView"
                type="button"
                title="Grid View"
                class="flex items-center gap-1.5 px-2.5 py-1 text-xs font-medium rounded-md transition-all {{ $view === 'grid' ? 'bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 shadow-2xs font-semibold' : 'text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-200' }}"
            >
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
                </svg>
                <span class="hidden md:inline">Grid</span>
            </button>

            <button
                wire:click="listView"
                type="button"
                title="List View"
                class="flex items-center gap-1.5 px-2.5 py-1 text-xs font-medium rounded-md transition-all {{ $view === 'list' ? 'bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 shadow-2xs font-semibold' : 'text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-200' }}"
            >
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
                <span class="hidden md:inline">List</span>
            </button>
        </div>

        <!-- Vertical Separator -->
        <div class="h-4 w-px bg-gray-200 dark:bg-gray-800"></div>

        <!-- Sort Filter -->
        <div class="relative">
            <select
                wire:model.live="sort"
                class="appearance-none rounded-lg border border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-900 pl-3 pr-7 py-1.5 text-xs font-medium text-gray-700 dark:text-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all cursor-pointer shadow-2xs hover:border-gray-300 dark:hover:border-gray-700"
            >
                <option value="latest">Sort: Latest</option>
                <option value="oldest">Sort: Oldest</option>
                <option value="name">Sort: Name</option>
                <option value="size">Sort: Size</option>
            </select>
            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-2 text-gray-400">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                </svg>
            </div>
        </div>

    </div>

</header>