<div>

@if($show)

<div class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center">


<div class="bg-white rounded-xl w-5/6 h-5/6 p-5">


<div class="flex justify-between mb-4">

<h2 class="text-xl font-bold">
Media Library
</h2>


<button wire:click="close">
✕
</button>


</div>


<livewire:asset-manager.asset-picker />


</div>

</div>

@endif

</div>