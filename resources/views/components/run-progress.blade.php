@props(['run'])

@php
    $percent = $run->progressPercent();
@endphp

<div class="w-40">
    <div class="flex items-center justify-between text-xs tabular-nums">
        <span>{{ $percent }}%</span>
        <span class="text-gray-500">{{ $run->status->label() }}</span>
    </div>

    <div class="mt-1 h-1.5 w-full rounded bg-gray-100 dark:bg-white/10">
        <div class="h-1.5 rounded bg-{{ $run->status->color() }}-500"
             style="width: {{ max(2, $percent) }}%"></div>
    </div>
</div>
