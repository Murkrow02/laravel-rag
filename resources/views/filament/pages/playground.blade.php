<x-filament-panels::page>
    <form wire:submit="run">
        {{ $this->form }}

        <div class="mt-4 flex items-center gap-3">
            <x-filament::button type="submit" icon="heroicon-o-play" wire:loading.attr="disabled">
                Run
            </x-filament::button>

            <span wire:loading wire:target="run" class="text-sm text-gray-500">
                Retrieving...
            </span>
        </div>
    </form>

    @if ($error)
        <x-filament::section>
            <x-slot name="heading">Error</x-slot>
            <p class="text-sm text-danger-600 dark:text-danger-400">{{ $error }}</p>
        </x-filament::section>
    @endif

    @if ($answer !== null)
        <x-filament::section :icon="$refused ? 'heroicon-o-exclamation-triangle' : 'heroicon-o-sparkles'">
            <x-slot name="heading">{{ $refused ? 'Refused' : 'Answer' }}</x-slot>
            @if ($refused)
                <x-slot name="description">
                    Nothing in the retrieved passages supported an answer. That is the
                    intended behaviour: a refusal is preferable to a plausible invention.
                </x-slot>
            @endif

            @php
                // Turn "[#1]" into a markdown link to its passage card below --
                // clicking a citation should jump to the source, not just claim one.
                $linkedAnswer = preg_replace('/\[#(\d+)\]/', '[#$1](#rag-passage-$1)', $answer);
            @endphp

            <div class="prose prose-sm max-w-none dark:prose-invert">
                {!! \Illuminate\Support\Str::markdown($linkedAnswer, ['html_input' => 'strip', 'allow_unsafe_links' => false]) !!}
            </div>
        </x-filament::section>
    @endif

    @if ($passages !== [])
        <x-filament::section>
            <x-slot name="heading">Retrieved passages ({{ count($passages) }})</x-slot>
            <x-slot name="description">
                Ordered as they were given to the model. A passage marked "cited" was
                actually referenced in the answer.
            </x-slot>

            <div class="space-y-4">
                @foreach ($passages as $passage)
                    <x-rag::chunk-card :passage="$passage" />
                @endforeach
            </div>
        </x-filament::section>
    @endif

    @if ($diagnostics !== [])
        <x-filament::section collapsible collapsed>
            <x-slot name="heading">Diagnostics</x-slot>

            <pre class="overflow-x-auto text-xs">{{ json_encode($diagnostics, JSON_PRETTY_PRINT) }}</pre>
        </x-filament::section>
    @endif

    <style>
        @keyframes rag-passage-flash {
            0% { box-shadow: 0 0 0 3px rgba(250, 204, 21, 0.9); background-color: rgba(250, 204, 21, 0.15); }
            100% { box-shadow: 0 0 0 0 rgba(250, 204, 21, 0); background-color: transparent; }
        }
        .rag-passage-highlight {
            animation: rag-passage-flash 1.6s ease-out;
        }
    </style>

    <script>
        // Guarded against Livewire re-rendering this script tag on every "Run".
        if (! window.__ragPassageHighlightBound) {
            window.__ragPassageHighlightBound = true;

            document.addEventListener('click', (event) => {
                const link = event.target.closest('a[href^="#rag-passage-"]');

                if (! link) {
                    return;
                }

                const target = document.querySelector(link.getAttribute('href'));

                if (! target) {
                    return;
                }

                event.preventDefault();
                target.scrollIntoView({behavior: 'smooth', block: 'center'});
                target.classList.remove('rag-passage-highlight');
                // Force a reflow so the animation restarts on repeat clicks.
                void target.offsetWidth;
                target.classList.add('rag-passage-highlight');
            });
        }
    </script>
</x-filament-panels::page>
