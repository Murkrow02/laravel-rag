<?php

declare(strict_types=1);

namespace Murkrow\Rag\Ingestion;

use Illuminate\Support\Facades\DB;
use Murkrow\Rag\Contracts\EmbeddingProvider;
use Murkrow\Rag\Contracts\VectorStore;
use Murkrow\Rag\Models\Chunk;
use Murkrow\Rag\Models\Document;
use Murkrow\Rag\Support\Tables;

/**
 * Embeds a group of chunks in one API call and writes the vectors back.
 *
 * Shared by the queued job and by `rag:ingest --sync` so the two paths cannot
 * drift. Idempotent by design: it re-reads the rows and skips anything already
 * embedded, which makes a retried job safe rather than a double charge.
 *
 * @phpstan-type EmbedResult array{embedded: int, tokens: int, cost_micros: int}
 */
final class ChunkEmbedder
{
    public function __construct(
        private readonly EmbeddingProvider $embeddings,
        private readonly VectorStore $store,
    ) {}

    /**
     * @param  array<int, int>  $chunkIds
     * @return array{embedded: int, tokens: int, cost_micros: int}
     */
    public function embed(array $chunkIds): array
    {
        if ($chunkIds === []) {
            return ['embedded' => 0, 'tokens' => 0, 'cost_micros' => 0];
        }

        $model = $this->embeddings->model();
        $dimensions = $this->embeddings->dimensions();

        /** @var \Illuminate\Database\Eloquent\Collection<int, Chunk> $chunks */
        $chunks = Chunk::query()
            ->whereIn('id', $chunkIds)
            ->where(function ($query) use ($model, $dimensions): void {
                $query->whereNull('embedded_at')
                    ->orWhere('embedding_model', '!=', $model)
                    ->orWhere('embedding_dimensions', '!=', $dimensions);
            })
            ->get();

        if ($chunks->isEmpty()) {
            return ['embedded' => 0, 'tokens' => 0, 'cost_micros' => 0];
        }

        $inputs = [];
        $ids = [];

        foreach ($chunks as $chunk) {
            $ids[] = (int) $chunk->id;
            $inputs[] = EmbeddingInput::for($chunk);
        }

        $batch = $this->embeddings->embedBatch($inputs);

        $vectors = [];

        foreach ($batch->vectors as $index => $vector) {
            if (! isset($ids[$index])) {
                continue;
            }

            $vectors[] = ['id' => $ids[$index], 'vector' => $vector];
        }

        $this->store->upsert($vectors, $batch->model, $batch->dimensions);

        $this->refreshDocumentCounters($chunks->pluck('document_id')->unique()->all());

        return [
            'embedded' => count($vectors),
            'tokens' => $batch->tokens,
            'cost_micros' => CostCalculator::embeddingMicros($batch->model, $batch->tokens),
        ];
    }

    /**
     * Recompute embedded_chunk_count in SQL so the dashboard's coverage figure
     * stays correct no matter how many workers touched the document.
     *
     * @param  array<int, int>  $documentIds
     */
    private function refreshDocumentCounters(array $documentIds): void
    {
        if ($documentIds === []) {
            return;
        }

        $documents = Tables::documents();
        $chunks = Tables::chunks();

        $placeholders = implode(',', array_fill(0, count($documentIds), '?'));

        DB::connection(Tables::connection())->update(
            <<<SQL
                UPDATE {$documents} AS d
                SET chunk_count = (
                        SELECT COUNT(*) FROM {$chunks} c WHERE c.document_id = d.id
                    ),
                    embedded_chunk_count = (
                        SELECT COUNT(*) FROM {$chunks} c
                        WHERE c.document_id = d.id AND c.embedded_at IS NOT NULL
                    )
                WHERE d.id IN ({$placeholders})
                SQL,
            array_map(intval(...), array_values($documentIds)),
        );

        Document::query()
            ->whereIn('id', $documentIds)
            ->whereColumn('embedded_chunk_count', '>=', 'chunk_count')
            ->where('chunk_count', '>', 0)
            ->update(['status' => 'embedded']);
    }
}
