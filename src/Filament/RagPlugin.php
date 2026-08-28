<?php

declare(strict_types=1);

namespace Murkrow\Rag\Filament;

use Filament\Contracts\Plugin;
use Filament\Navigation\NavigationItem;
use Filament\Panel;
use Filament\Support\Facades\FilamentView;
use Illuminate\Support\Facades\Route;
use Murkrow\Rag\Chat\ChatAbilities;
use Murkrow\Rag\Filament\Pages\IngestKnowledge;
use Murkrow\Rag\Filament\Pages\RagDashboard;
use Murkrow\Rag\Filament\Pages\RagPlayground;
use Murkrow\Rag\Filament\Pages\RagSettings;
use Murkrow\Rag\Filament\Resources\DocumentResource;
use Murkrow\Rag\Filament\Resources\IngestionRunResource;
use Murkrow\Rag\Filament\Resources\QueryResource;
use Murkrow\Rag\Filament\Widgets\IngestionThroughputChart;
use Murkrow\Rag\Filament\Widgets\KnowledgeStatsOverview;
use Murkrow\Rag\Filament\Widgets\LatestRunsTable;
use Murkrow\Rag\Filament\Widgets\SourceCoverageChart;

/**
 * The control panel.
 *
 * Registered explicitly rather than discovered, because the host panel's
 * `discoverResources()` only scans its own app directories -- a package's
 * classes are invisible to it.
 *
 *     ->plugin(\Murkrow\Rag\Filament\RagPlugin::make())
 *
 * Every page and resource is individually switchable in config, so a host can
 * expose the dashboard to operators while keeping ingestion controls to itself.
 */
class RagPlugin implements Plugin
{
    public static function make(): static
    {
        return app(static::class);
    }

    public function getId(): string
    {
        return 'rag';
    }

    public function register(Panel $panel): void
    {
        if (! config('rag.enabled', true) || ! config('rag.filament.enabled', true)) {
            return;
        }

        $panel
            ->resources($this->resources())
            ->pages($this->pages())
            ->widgets($this->widgets());
    }

    /**
     * A link out to the standalone chat page.
     *
     * A plain URL item rather than a Filament page: the chat is a separate
     * document with its own stylesheet and no Livewire on it at all.
     */
    protected function registerChatLink(Panel $panel): void
    {
        if (! config('rag.filament.pages.chat_link', true) || ! config('rag.chat.enabled', true)) {
            return;
        }

        if (! Route::has('rag.chat.index')) {
            return;
        }

        // A panel in SPA mode puts wire:navigate on every in-app link, and
        // Livewire would then swap this page's body into the panel's document
        // -- the panel's <head>, none of the chat's. The chat has to be a real
        // navigation, so its URLs are declared an exception.
        if (FilamentView::hasSpaMode()) {
            FilamentView::spaUrlExceptions([
                route('rag.chat.index'),
                route('rag.chat.index').'/*',
            ]);
        }

        $panel->navigationItems([
            NavigationItem::make('rag-chat')
                ->label(fn (): string => (string) __('rag::rag.chat.title'))
                ->icon('heroicon-o-chat-bubble-left-right')
                ->group(fn () => config('rag.filament.navigation_group', 'Knowledge'))
                ->sort((int) config('rag.filament.navigation_sort', 90) + 3)
                ->url(fn (): string => route('rag.chat.index'))
                ->visible(fn (): bool => ChatAbilities::allows('view')),
        ]);
    }

    public function boot(Panel $panel): void
    {
        // Not register(): a panel provider builds its panel during the
        // register phase, before this package's boot() has bound the chat
        // routes, so Route::has() there is always false.
        $this->registerChatLink($panel);
    }

    /**
     * @return array<int, class-string>
     */
    protected function resources(): array
    {
        return array_values(array_filter([
            config('rag.filament.resources.runs', true) ? IngestionRunResource::class : null,
            config('rag.filament.resources.documents', true) ? DocumentResource::class : null,
            config('rag.filament.resources.queries', true) ? QueryResource::class : null,
        ]));
    }

    /**
     * @return array<int, class-string>
     */
    protected function pages(): array
    {
        return array_values(array_filter([
            config('rag.filament.pages.dashboard', true) ? RagDashboard::class : null,
            config('rag.filament.pages.ingest', true) ? IngestKnowledge::class : null,
            config('rag.filament.pages.playground', true) ? RagPlayground::class : null,
            config('rag.filament.pages.settings', true) ? RagSettings::class : null,
        ]));
    }

    /**
     * @return array<int, class-string>
     */
    protected function widgets(): array
    {
        if (! config('rag.filament.pages.dashboard', true)) {
            return [];
        }

        return [
            KnowledgeStatsOverview::class,
            IngestionThroughputChart::class,
            SourceCoverageChart::class,
            LatestRunsTable::class,
        ];
    }
}
