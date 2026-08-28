<?php

declare(strict_types=1);

namespace Murkrow\Rag\Contracts;

use Generator;
use Illuminate\Support\LazyCollection;
use Murkrow\Rag\Data\DocumentDraft;
use Murkrow\Rag\Data\Segment;
use Murkrow\Rag\Models\Chunk;
use Murkrow\Rag\Models\Document;
use Murkrow\Rag\Sources\ChunkingOverrides;
use Murkrow\Rag\Sources\FilterSet;

/**
 * Adapts host application data to the package.
 *
 * Implementations must stay framework-agnostic in their return types: filters
 * are SourceFilter objects, not Filament fields, so the CLI and the ingestion
 * form can both consume them without the package depending on Filament.
 *
 * Most sources are written by extending `EloquentSource`; implement this
 * directly only for knowledge that is not an Eloquent model at all.
 */
interface KnowledgeSource
{
    public function key(): string;

    public function label(): string;

    public function icon(): ?string;

    /**
     * @param  array<string, mixed>  $filters
     * @return LazyCollection<int, DocumentDraft>
     */
    public function documents(array $filters = []): LazyCollection;

    /**
     * @param  array<string, mixed>  $filters
     */
    public function countDocuments(array $filters = []): int;

    public function findDocument(string $externalId): ?DocumentDraft;

    /**
     * Ordered, memory-safe stream of the document's text segments.
     *
     * @return Generator<int, Segment>
     */
    public function segments(string $externalId): Generator;

    /**
     * The filters this source exposes, keyed by name.
     */
    public function filterSet(): FilterSet;

    public function positionLabel(int $start, int $end): string;

    public function url(Document $document, ?Chunk $chunk = null): ?string;

    /**
     * Per-source chunking parameters merged over the global config block.
     */
    public function chunkingOverrides(): ChunkingOverrides;
}
