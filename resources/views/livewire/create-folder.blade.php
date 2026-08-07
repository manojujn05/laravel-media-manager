<div>

    @if($open)

        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50">

            <div class="w-full max-w-md rounded-lg bg-white p-6">

                <h2 class="mb-4 text-lg font-semibold">
                    Create Folder
                </h2>

                <div class="space-y-4">

                    <div>
                        <label class="block text-sm font-medium">
                            Folder Name
                        </label>

                        <input
                            type="text"
                            wire:model="name"
                            class="mt-1 w-full rounded border p-2"
                        />

                        @error('name')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium">
                            Parent Folder
                        </label>

                        <select
                            wire:model="parentId"
                            class="mt-1 w-full rounded border p-2"
                        >
                            <option value="">Root</option>

                            @foreach($folders as $folder)

                                <option value="{{ $folder->id }}">
                                    {{ $folder->name }}
                                </option>

                            @endforeach

                        </select>
                    </div>

                </div>

                <div class="mt-6 flex justify-end gap-2">

                    <button
                        wire:click="closeModal"
                        class="rounded border px-4 py-2"
                    >
                        Cancel
                    </button>

                    <button
                        wire:click="save"
                        class="rounded bg-blue-600 px-4 py-2 text-white"
                    >
                        Create
                    </button>

                </div>

            </div>

        </div>

    @endif

</div>