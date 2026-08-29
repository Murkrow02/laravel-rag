@include('rag::partials.styles')

{{--
    Styled with Filament's own components and plain inline layout only.

    Nothing here uses Tailwind utility classes: Filament ships a precompiled
    stylesheet containing its semantic `fi-*` classes and nothing else, so a
    utility like `grid-cols-5` would simply not exist unless the host app built
    a custom theme. Everything below is either a Filament component or an inline
    style using the colour variables Filament already defines on the page
    (`--gray-500`, `--primary-500`, ...), which keeps this package free of any
    npm step in the applications that install it.

    Auto-fit grids replace responsive breakpoints: they reflow on their own,
    which is both simpler and one less thing to get wrong.
--}}
<x-filament-panels::page>
    @if (empty(config('rag.sources')))
        <x-filament::callout icon="heroicon-o-exclamation-triangle" color="warning">
            <x-slot name="heading">{{ __('rag::rag.dashboard.no_sources') }}</x-slot>

            {{ __('rag::rag.dashboard.no_sources_help') }}
        </x-filament::callout>
    @endif

    {{-- Corpus health --}}
    <div style="display:grid;gap:1rem;grid-template-columns:repeat(auto-fit,minmax(11rem,1fr));">
        @foreach ([
            [
                'label' => __('rag::rag.dashboard.documents'),
                'value' => number_format($stats['documents']),
                'note' => trans_choice('rag::rag.dashboard.chunks_count', $stats['chunks'], ['count' => number_format($stats['chunks'])]),
                'color' => null,
            ],
            [
                'label' => __('rag::rag.dashboard.coverage'),
                'value' => $stats['coverage'].'%',
                'note' => __('rag::rag.dashboard.coverage_note', ['embedded' => number_format($stats['embedded']), 'total' => number_format($stats['chunks'])]),
                'color' => $stats['coverage'] >= 99 ? 'success' : ($stats['coverage'] >= 60 ? 'warning' : 'danger'),
            ],
            [
                'label' => __('rag::rag.dashboard.pending'),
                'value' => number_format($stats['pending']),
                'note' => $stats['pending'] > 0 ? __('rag::rag.dashboard.pending_note') : __('rag::rag.dashboard.pending_none'),
                'color' => $stats['pending'] > 0 ? 'warning' : 'success',
            ],
            [
                'label' => __('rag::rag.dashboard.stale'),
                'value' => number_format($stats['stale']),
                'note' => $stats['stale'] > 0 ? __('rag::rag.dashboard.stale_note') : __('rag::rag.dashboard.stale_none'),
                'color' => $stats['stale'] > 0 ? 'danger' : 'success',
            ],
            [
                'label' => __('rag::rag.dashboard.spend'),
                'value' => $stats['spend'],
                'note' => __('rag::rag.dashboard.spend_note', ['queries' => number_format($stats['queries']), 'ms' => $stats['avgLatency']]),
                'color' => null,
            ],
        ] as $stat)
            <x-filament::card>
                <p style="font-size:.8125rem;color:var(--rag-muted);">{{ $stat['label'] }}</p>
                <p @style([
                    'font-size:1.75rem;line-height:2.25rem;font-weight:600;margin-top:.125rem',
                    'color:var(--'.$stat['color'].'-500)' => $stat['color'] !== null,
                ])>{{ $stat['value'] }}</p>
                <p style="font-size:.75rem;color:var(--rag-muted);">{{ $stat['note'] }}</p>
            </x-filament::card>
        @endforeach
    </div>

    <div style="display:grid;gap:1.5rem;grid-template-columns:repeat(auto-fit,minmax(20rem,1fr));">
        {{-- Embedding throughput --}}
        <x-filament::section :heading="__('rag::rag.dashboard.throughput')" :description="__('rag::rag.dashboard.throughput_help')">
            <div style="display:flex;align-items:flex-end;gap:1px;height:8rem;overflow-x:auto;">
                @foreach ($throughputByHour as $point)
                    <div
                        style="flex:1 1 0;min-width:3px;border-radius:2px 2px 0 0;background:var(--primary-500);opacity:.75;height:{{ max($point['percent'], 2) }}%"
                        title="{{ $point['label'] }} — {{ $point['count'] }}"
                    ></div>
                @endforeach
            </div>
        </x-filament::section>

        {{-- Coverage by source --}}
        <x-filament::section :heading="__('rag::rag.dashboard.coverage_by_source')">
            @forelse ($coverageBySource as $row)
                <div @style(['margin-top:.75rem' => ! $loop->first])>
                    <div style="display:flex;justify-content:space-between;font-size:.8125rem;margin-bottom:.25rem;">
                        <span style="font-weight:500;">{{ $row['source'] }}</span>
                        <span style="color:var(--rag-muted);">{{ number_format($row['embedded']) }} / {{ number_format($row['total']) }}</span>
                    </div>
                    <div style="height:.5rem;border-radius:9999px;overflow:hidden;background:var(--rag-line);">
                        <div style="height:100%;border-radius:9999px;background:var(--primary-500);width:{{ $row['percent'] }}%"></div>
                    </div>
                </div>
            @empty
                <p style="font-size:.875rem;color:var(--rag-muted);">{{ __('rag::rag.dashboard.no_index') }}</p>
            @endforelse
        </x-filament::section>
    </div>

    {{-- Query volume --}}
    <x-filament::section :heading="__('rag::rag.dashboard.questions_per_day')">
        <div style="display:flex;align-items:flex-end;gap:1px;height:8rem;overflow-x:auto;">
            @foreach ($queryVolumeByDay as $day)
                <div
                    style="flex:1 1 0;min-width:6px;display:flex;flex-direction:column-reverse;border-radius:2px 2px 0 0;overflow:hidden;"
                    title="{{ $day['label'] }} — {{ $day['answered'] }} / {{ $day['refused'] }}"
                >
                    <div style="background:var(--success-500);opacity:.75;height:{{ $day['answeredPercent'] }}%"></div>
                    <div style="background:var(--danger-500);opacity:.75;height:{{ $day['refusedPercent'] }}%"></div>
                </div>
            @endforeach
        </div>

        <div style="display:flex;gap:1rem;font-size:.75rem;color:var(--rag-muted);margin-top:.5rem;">
            <span><span style="display:inline-block;width:.5rem;height:.5rem;border-radius:9999px;background:var(--success-500);"></span> {{ __('rag::rag.dashboard.answered') }}</span>
            <span><span style="display:inline-block;width:.5rem;height:.5rem;border-radius:9999px;background:var(--danger-500);"></span> {{ __('rag::rag.dashboard.refused') }}</span>
        </div>
    </x-filament::section>

    {{-- Recent runs --}}
    <x-filament::section :heading="__('rag::rag.dashboard.recent_runs')" :description="__('rag::rag.dashboard.snapshot')">
        @if (empty($recentRuns))
            <p style="font-size:.875rem;color:var(--rag-muted);">{{ __('rag::rag.dashboard.no_runs') }}</p>
        @else
            <div style="overflow-x:auto;">
                <table style="width:100%;text-align:left;font-size:.875rem;border-collapse:collapse;">
                    <thead>
                        <tr style="color:var(--rag-muted);border-bottom:1px solid var(--rag-line);">
                            <th style="padding:.5rem 1rem .5rem 0;font-weight:500;">{{ __('rag::rag.dashboard.source') }}</th>
                            <th style="padding:.5rem 1rem .5rem 0;font-weight:500;">{{ __('rag::rag.dashboard.status') }}</th>
                            <th style="padding:.5rem 1rem .5rem 0;font-weight:500;">{{ __('rag::rag.dashboard.progress') }}</th>
                            <th style="padding:.5rem 1rem .5rem 0;font-weight:500;">{{ __('rag::rag.dashboard.embedded') }}</th>
                            <th style="padding:.5rem 1rem .5rem 0;font-weight:500;">{{ __('rag::rag.dashboard.cost') }}</th>
                            <th style="padding:.5rem 0;font-weight:500;">{{ __('rag::rag.dashboard.started') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($recentRuns as $run)
                            <tr style="border-bottom:1px solid var(--rag-track);">
                                <td style="padding:.5rem 1rem .5rem 0;">{{ $run['source'] }}</td>
                                <td style="padding:.5rem 1rem .5rem 0;">
                                    <x-filament::badge :color="$run['statusColor']" :icon="$run['statusIcon']">
                                        {{ $run['statusLabel'] }}
                                    </x-filament::badge>
                                </td>
                                <td style="padding:.5rem 1rem .5rem 0;">{{ $run['progress'] }}%</td>
                                <td style="padding:.5rem 1rem .5rem 0;">{{ $run['embedded'] }}</td>
                                <td style="padding:.5rem 1rem .5rem 0;">{{ $run['cost'] }}</td>
                                <td style="padding:.5rem 0;color:var(--rag-muted);">{{ $run['started'] }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </x-filament::section>
</x-filament-panels::page>
