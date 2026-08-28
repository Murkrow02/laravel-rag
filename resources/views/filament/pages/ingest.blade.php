<x-filament-panels::page>
    <form wire:submit="start">
        {{ $this->form }}
    </form>

    @if ($estimate)
        <x-filament::section icon="heroicon-o-calculator">
            <x-slot name="heading">Estimate</x-slot>
            <x-slot name="description">
                Extrapolated from a sample of documents, so treat it as an order of
                magnitude rather than an invoice.
            </x-slot>

            <dl class="grid grid-cols-2 gap-4 sm:grid-cols-4">
                @foreach ([
                    'Documents' => number_format((int) $estimate['documents']),
                    'Segments' => number_format((int) $estimate['segments']),
                    'Chunks' => '~' . number_format((int) $estimate['chunks']),
                    'Tokens' => '~' . number_format((int) $estimate['tokens']),
                ] as $label => $value)
                    <div>
                        <dt class="text-sm text-gray-500 dark:text-gray-400">{{ $label }}</dt>
                        <dd class="text-lg font-semibold tabular-nums">{{ $value }}</dd>
                    </div>
                @endforeach
            </dl>

            <p class="mt-4 text-sm text-gray-500 dark:text-gray-400">
                Estimated embedding cost:
                <span class="font-semibold text-gray-950 dark:text-white">
                    {{ \Murkrow\Rag\Ingestion\CostCalculator::format((int) $estimate['cost_micros'], 4) }}
                </span>
                using {{ config('rag.embeddings.model') }}.
            </p>
        </x-filament::section>
    @endif

    <x-filament::section collapsible collapsed>
        <x-slot name="heading">Before a large run</x-slot>

        <ul class="list-inside list-disc space-y-1 text-sm text-gray-500 dark:text-gray-400">
            <li>
                A worker must be consuming the
                <code>{{ config('rag.queue.queue') }}</code> queue on the
                <code>{{ config('rag.queue.connection') }}</code> connection, otherwise the
                run sits at zero forever.
            </li>
            <li>
                Incremental mode re-embeds only text that changed, so re-running it over an
                unchanged corpus costs nothing.
            </li>
            <li>
                Build the vector index after a bulk load rather than before:
                <code>php artisan rag:vector:reindex</code>.
            </li>
        </ul>
    </x-filament::section>
</x-filament-panels::page>
