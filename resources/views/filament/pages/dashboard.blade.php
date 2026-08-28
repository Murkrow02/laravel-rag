<x-filament-panels::page>
    @if (empty(config('rag.sources')))
        <x-filament::section>
            <x-slot name="heading">No knowledge sources configured</x-slot>

            <p class="text-sm text-gray-500 dark:text-gray-400">
                Add at least one source under <code>rag.sources</code> in
                <code>config/rag.php</code>, mapping one of your Eloquent models to an
                ordered stream of text. Until then there is nothing to index.
            </p>
        </x-filament::section>
    @endif

    {{-- Stats overview --}}
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-5">
        <x-filament::card>
            <p class="text-sm text-gray-500 dark:text-gray-400">Documents</p>
            <p class="text-2xl font-semibold">{{ number_format($stats['documents']) }}</p>
            <p class="text-xs text-gray-400">{{ number_format($stats['chunks']) }} chunks</p>
        </x-filament::card>

        <x-filament::card>
            <p class="text-sm text-gray-500 dark:text-gray-400">Coverage</p>
            <p @class([
                'text-2xl font-semibold',
                'text-success-600 dark:text-success-400' => $stats['coverage'] >= 99,
                'text-warning-600 dark:text-warning-400' => $stats['coverage'] >= 60 && $stats['coverage'] < 99,
                'text-danger-600 dark:text-danger-400' => $stats['coverage'] < 60,
            ])>{{ $stats['coverage'] }}%</p>
            <p class="text-xs text-gray-400">{{ number_format($stats['embedded']) }} of {{ number_format($stats['chunks']) }} embedded</p>
        </x-filament::card>

        <x-filament::card>
            <p class="text-sm text-gray-500 dark:text-gray-400">Awaiting embedding</p>
            <p @class([
                'text-2xl font-semibold',
                'text-warning-600 dark:text-warning-400' => $stats['pending'] > 0,
                'text-success-600 dark:text-success-400' => $stats['pending'] === 0,
            ])>{{ number_format($stats['pending']) }}</p>
            <p class="text-xs text-gray-400">{{ $stats['pending'] > 0 ? 'Run an ingestion to finish' : 'Nothing queued' }}</p>
        </x-filament::card>

        <x-filament::card>
            <p class="text-sm text-gray-500 dark:text-gray-400">Stale vectors</p>
            <p @class([
                'text-2xl font-semibold',
                'text-danger-600 dark:text-danger-400' => $stats['stale'] > 0,
                'text-success-600 dark:text-success-400' => $stats['stale'] === 0,
            ])>{{ number_format($stats['stale']) }}</p>
            <p class="text-xs text-gray-400">{{ $stats['stale'] > 0 ? 'Embedded with another model' : 'All current' }}</p>
        </x-filament::card>

        <x-filament::card>
            <p class="text-sm text-gray-500 dark:text-gray-400">Spend to date</p>
            <p class="text-2xl font-semibold">{{ $stats['spend'] }}</p>
            <p class="text-xs text-gray-400">{{ number_format($stats['queries']) }} queries, avg {{ $stats['avgLatency'] }} ms</p>
        </x-filament::card>
    </div>

    <div class="grid grid-cols-1 gap-4 lg:grid-cols-2">
        {{-- Embedding throughput --}}
        <x-filament::section>
            <x-slot name="heading">Embedding throughput (last 24h)</x-slot>

            <div class="flex h-32 items-end gap-px overflow-x-auto">
                @foreach ($throughputByHour as $point)
                    <div
                        class="min-w-[3px] flex-1 rounded-t bg-primary-500/70"
                        style="height: {{ max($point['percent'], 2) }}%"
                        title="{{ $point['label'] }}: {{ $point['count'] }} chunks"
                    ></div>
                @endforeach
            </div>
            <p class="mt-2 text-xs text-gray-400">A flat line during a run means workers have stalled or the provider is throttling.</p>
        </x-filament::section>

        {{-- Coverage by source --}}
        <x-filament::section>
            <x-slot name="heading">Coverage by source</x-slot>

            <div class="space-y-3">
                @forelse ($coverageBySource as $row)
                    <div>
                        <div class="mb-1 flex items-center justify-between text-sm">
                            <span class="font-medium">{{ $row['source'] }}</span>
                            <span class="text-gray-400">{{ number_format($row['embedded']) }} / {{ number_format($row['total']) }}</span>
                        </div>
                        <div class="h-2 w-full overflow-hidden rounded-full bg-gray-100 dark:bg-gray-700">
                            <div class="h-full rounded-full bg-primary-500" style="width: {{ $row['percent'] }}%"></div>
                        </div>
                    </div>
                @empty
                    <p class="text-sm text-gray-400">No sources indexed yet.</p>
                @endforelse
            </div>
        </x-filament::section>
    </div>

    {{-- Query volume --}}
    <x-filament::section>
        <x-slot name="heading">Questions per day (last 30d)</x-slot>

        <div class="flex h-32 items-end gap-px overflow-x-auto">
            @foreach ($queryVolumeByDay as $day)
                <div
                    class="flex min-w-[6px] flex-1 flex-col-reverse rounded-t overflow-hidden"
                    title="{{ $day['label'] }}: {{ $day['answered'] }} answered, {{ $day['refused'] }} refused"
                >
                    <div class="bg-success-500/70" style="height: {{ $day['answeredPercent'] }}%"></div>
                    <div class="bg-danger-500/70" style="height: {{ $day['refusedPercent'] }}%"></div>
                </div>
            @endforeach
        </div>
        <p class="mt-2 flex gap-4 text-xs text-gray-400">
            <span><span class="inline-block h-2 w-2 rounded-full bg-success-500/70"></span> Answered</span>
            <span><span class="inline-block h-2 w-2 rounded-full bg-danger-500/70"></span> Refused</span>
        </p>
    </x-filament::section>

    {{-- Recent runs --}}
    <x-filament::section>
        <x-slot name="heading">Recent ingestion runs</x-slot>

        @if (empty($recentRuns))
            <p class="text-sm text-gray-400">No runs yet.</p>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead>
                        <tr class="border-b border-gray-100 text-gray-500 dark:border-gray-700 dark:text-gray-400">
                            <th class="py-2 pr-4 font-medium">Source</th>
                            <th class="py-2 pr-4 font-medium">Status</th>
                            <th class="py-2 pr-4 font-medium">Progress</th>
                            <th class="py-2 pr-4 font-medium">Embedded</th>
                            <th class="py-2 pr-4 font-medium">Cost</th>
                            <th class="py-2 font-medium">Started</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($recentRuns as $run)
                            <tr class="border-b border-gray-50 last:border-0 dark:border-gray-800">
                                <td class="py-2 pr-4">{{ $run['source'] }}</td>
                                <td class="py-2 pr-4">
                                    <x-filament::badge :color="$run['statusColor']" :icon="$run['statusIcon']">
                                        {{ $run['statusLabel'] }}
                                    </x-filament::badge>
                                </td>
                                <td class="py-2 pr-4">{{ $run['progress'] }}%</td>
                                <td class="py-2 pr-4">{{ $run['embedded'] }}</td>
                                <td class="py-2 pr-4">{{ $run['cost'] }}</td>
                                <td class="py-2 text-gray-400">{{ $run['started'] }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif

        <p class="mt-3 text-xs text-gray-400">
            Static snapshot as of page load --
            <button
                type="button"
                onclick="window.location.reload()"
                class="text-primary-600 underline dark:text-primary-400"
            >refresh</button>
            to update.
        </p>
    </x-filament::section>
</x-filament-panels::page>
