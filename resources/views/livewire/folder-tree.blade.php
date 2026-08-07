<div>

    <div class="mb-4">
        <button
            wire:click="$dispatch('open-create-folder')"
            class="rounded bg-blue-600 px-3 py-2 text-white"
        >
            + New Folder
        </button>
    </div>

    @foreach($folders as $folder)

        <livewire:asset-manager.folder-node
            :folder="$folder"
            :key="'folder-'.$folder->id"
        />

    @endforeach

    <livewire:asset-manager.create-folder />

</div>