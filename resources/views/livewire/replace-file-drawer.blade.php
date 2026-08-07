<div>
    @if($isOpen && $asset)
        <div class="fixed inset-0 z-50 overflow-hidden" aria-labelledby="slide-over-title" role="dialog" aria-modal="true">
            <div class="fixed inset-0 bg-gray-900 bg-opacity-75 transition-opacity" wire:click="closeDrawer"></div>

            <div class="fixed inset-y-0 right-0 pl-10 max-w-full flex">
                <div class="w-screen max-w-md bg-white shadow-2xl flex flex-col justify-between">
                    
                    <!-- Header -->
                    <div>
                        <div class="bg-slate-900 px-6 py-5 flex items-center justify-between">
                            <h3 class="text-base font-bold text-white">Replace File</h3>
                            <button wire:click="closeDrawer" class="text-slate-400 hover:text-white transition">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </div>

                        <!-- Current File Info -->
                        <div class="p-6 bg-slate-50 border-b border-slate-200">
                            <span class="text-xs font-bold text-slate-500 uppercase tracking-wider block mb-2">Current Active File</span>
                            <div class="p-3 bg-white rounded-xl border border-slate-200">
                                <p class="text-sm font-bold text-slate-800">{{ $asset->title }}</p>
                                <p class="text-xs text-slate-500 truncate mt-1">Path: {{ $asset->path }}</p>
                            </div>
                        </div>

                        <!-- Form -->
                        <form wire:submit.prevent="replaceFile" class="p-6 space-y-4">
                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                                    Choose New File
                                </label>
                                <input type="file" wire:model="newFile" class="w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                                @error('newFile') <span class="text-xs text-rose-500 font-semibold mt-1 block">{{ $message }}</span> @enderror
                            </div>
                        </form>
                    </div>

                    <!-- Footer -->
                    <div class="p-6 bg-slate-50 border-t border-slate-200 flex justify-end space-x-3">
                        <button wire:click="closeDrawer" type="button" class="px-4 py-2 text-sm font-semibold text-slate-600 bg-white border border-slate-300 rounded-xl hover:bg-slate-100 transition">
                            Cancel
                        </button>
                        <button wire:click="replaceFile" wire:loading.attr="disabled" class="px-5 py-2 text-sm font-bold text-white bg-indigo-600 rounded-xl hover:bg-indigo-700 transition">
                            <span wire:loading.remove wire:target="replaceFile">Replace</span>
                            <span wire:loading wire:target="replaceFile">Uploading...</span>
                        </button>
                    </div>

                </div>
            </div>
        </div>
    @endif
</div>