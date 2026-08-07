@php
    $usageDetails = $asset->getUsageDetails();
@endphp

<div class="space-y-3 text-xs font-sans">
    <div class="flex items-center justify-between pb-2 border-b border-gray-200 dark:border-gray-800">
        <h4 class="font-bold text-gray-900 dark:text-gray-100 uppercase tracking-wider text-[11px]">Usage Tracker</h4>
        
        @if(count($usageDetails) > 0)
            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-rose-50 dark:bg-rose-950/50 text-rose-600 dark:text-rose-400 border border-rose-200 dark:border-rose-800 text-[10px] font-semibold">
                <span class="w-1.5 h-1.5 rounded-full bg-rose-500 animate-pulse"></span>
                In Use (Locked)
            </span>
        @else
            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-emerald-50 dark:bg-emerald-950/50 text-emerald-600 dark:text-emerald-400 text-[10px] font-semibold">
                Unused (Safe to Delete)
            </span>
        @endif
    </div>

    <!-- Dynamic Usage List -->
    @if(count($usageDetails) > 0)
        <div class="space-y-2 bg-gray-50 dark:bg-gray-950 p-3 rounded-xl border border-gray-200 dark:border-gray-800">
            @foreach($usageDetails as $modelName => $count)
                <div class="flex items-center justify-between">
                    <span class="font-medium text-gray-700 dark:text-gray-300">{{ $modelName }}</span>
                    <span class="px-2 py-0.5 rounded-md font-mono font-bold bg-indigo-100 dark:bg-indigo-950 text-indigo-600 dark:text-indigo-400">
                        {{ $count }}
                    </span>
                </div>
            @endforeach
        </div>
    @else
        <p class="text-gray-400 text-[11px]">This asset is not linked to any module.</p>
    @endif
</div>