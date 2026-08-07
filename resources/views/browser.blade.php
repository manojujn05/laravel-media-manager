<!DOCTYPE html>
<html>
<head>
    <title>Asset Manager</title>

    @vite(['resources/css/app.css','resources/js/app.js'])

    @livewireStyles
</head>
<body>

    <!-- Main Media Browser Component -->
    <livewire:asset-manager.media-browser />

    <!-- Phase 12: Version History & Replace File Drawer Components -->
    <livewire:asset-manager.version-history-modal />
    <livewire:asset-manager.replace-file-drawer />

    @livewireScripts

</body>
</html>