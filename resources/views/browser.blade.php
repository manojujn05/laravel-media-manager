<!DOCTYPE html>
<html>
<head>
    <title>Asset Manager</title>

    <link rel="stylesheet" href="{{ asset('vendor/asset-manager/css/asset-manager.css') }}?v={{ config('asset-manager.version') }}">
    <script src="{{ asset('vendor/asset-manager/js/asset-manager.js') }}?v={{ config('asset-manager.version') }}" defer></script>

    @livewireStyles
</head>
<body>

    <!-- Main Media Browser Component -->
    <livewire:asset-manager.media-browser />

    <!-- Phase 12: Version History & Replace File Drawer Components -->
    <livewire:asset-manager.replace-file-drawer />

    @livewireScripts

</body>
</html>