<div>
    @foreach($folders as $folder)
        <livewire:asset-manager.folder-node
            :folder="$folder"
            :key="'folder-'.$folder->id"
        />
    @endforeach
</div>