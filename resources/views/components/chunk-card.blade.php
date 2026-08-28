@props(['passage'])

@php
    $score = (float) ($passage['score'] ?? 0);
    // Scores below ~0.3 are usually noise; the bar makes that visible at a glance.
    $width = max(2, min(100, (int) round($score * 100)));
    $tone = $score >= 0.6 ? 'success' : ($score >= 0.35 ? 'warning' : 'danger');
@endphp

<div
    id="rag-passage-{{ $passage['marker'] }}"
    class="scroll-mt-24 rounded-lg border border-gray-200 p-4 transition-shadow dark:border-white/10"
>
    <div class="flex flex-wrap items-center gap-2">
        <x-filament::badge :color="($passage['used'] ?? false) ? 'success' : 'gray'">
            [#{{ $passage['marker'] }}]{{ ($passage['used'] ?? false) ? ' cited' : '' }}
        </x-filament::badge>

        <span class="text-sm font-medium text-gray-950 dark:text-white">
            {{ $passage['label'] }}
        </span>

        @if (! empty($passage['url']))
            <a href="{{ $passage['url'] }}" target="_blank" rel="noopener"
               title="Open source"
               aria-label="Open source"
               class="text-gray-400 hover:text-primary-600 dark:text-gray-500 dark:hover:text-primary-400">
                <x-filament::icon icon="heroicon-o-arrow-top-right-on-square" class="h-4 w-4" />
            </a>
        @endif

        <span class="ms-auto text-xs tabular-nums text-gray-500 dark:text-gray-400">
            {{ number_format($score, 3) }}
        </span>
    </div>

    <div class="mt-2 h-1 w-full rounded bg-gray-100 dark:bg-white/10">
        <div class="h-1 rounded bg-{{ $tone }}-500" style="width: {{ $width }}%"></div>
    </div>

    <p class="mt-3 text-sm leading-relaxed text-gray-700 dark:text-gray-300">
        {{ $passage['content'] }}
    </p>
</div>
