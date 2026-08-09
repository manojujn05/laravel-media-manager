<div class="rounded-xl border-2 border-dashed border-gray-300 bg-white p-8 dark:border-gray-700 dark:bg-gray-900">
    <form wire:submit="upload">
        <input type="file" wire:model="files" multiple class="hidden" id="asset-upload">
        <label for="asset-upload" class="flex cursor-pointer flex-col items-center justify-center py-10">
            <svg xmlns="http://www.w3.org/2000/svg" class="mb-4 h-16 w-16 text-blue-500"
                fill="none"
                viewBox="0 0 24 24"
                stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                    d="M7 16V8m0 0l-3 3m3-3l3 3m7 5a2 2 0 002-2V7a2 2 0 00-2-2h-3l-2-2H9L7 5H4a2 2 0 00-2 2v12a2 2 0 002 2h16z"/>
            </svg>
            <h3 class="text-lg font-semibold">
                Drag & Drop Files Here
            </h3>
            <p class="mt-2 text-sm text-gray-500">
                or click to browse files
            </p>
        </label>
        @error('files.*')
            <p class="mt-2 text-sm text-red-500">
                {{ $message }}
            </p>
        @enderror
        <div wire:loading wire:target="files" class="mt-6">
            <div class="mb-2 text-sm">
                Uploading...
            </div>
            <progress class="w-full" max="100" x-bind:value="$wire.__instance.uploadProgress">
            </progress>
        </div>
        @if(count($files))
            <div class="mt-6 space-y-2">
                @foreach($files as $file)
                    <div class="rounded-lg border p-3 text-sm">
                        {{ $file->getClientOriginalName() }}
                    </div>
                @endforeach
            </div>
        @endif
        <button type="submit" class="mt-6 w-full rounded-lg bg-blue-600 py-3 text-white hover:bg-blue-700">
            Upload Files
        </button>
    </form>
</div>