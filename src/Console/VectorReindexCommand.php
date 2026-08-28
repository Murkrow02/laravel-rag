<?php

declare(strict_types=1);

namespace Murkrow\Rag\Console;

use Illuminate\Console\Command;
use Murkrow\Rag\Contracts\VectorStore;
use Murkrow\Rag\Models\Chunk;

class VectorReindexCommand extends Command
{
    protected $signature = 'rag:vector:reindex
                            {--force : Skip the confirmation prompt}';

    protected $description = 'Drop and rebuild the approximate-nearest-neighbour index';

    public function handle(VectorStore $store): int
    {
        $chunks = Chunk::query()->whereNotNull('embedded_at')->count();

        $this->components->twoColumnDetail('driver', $store->name());
        $this->components->twoColumnDetail('embedded chunks', number_format($chunks));

        if (! $this->option('force') && ! $this->confirm('Rebuild the vector index? Searches will fall back to a sequential scan while it builds.', true)) {
            return self::SUCCESS;
        }

        // Building after a bulk load produces a better graph than incremental
        // inserts do, and is substantially faster overall.
        $this->components->task('dropping index', static function () use ($store): bool {
            $store->dropIndexes();

            return true;
        });

        $this->components->task('building index', static function () use ($store): bool {
            $store->installIndexes($store->dimensions());

            return true;
        });

        $this->components->info('Done. For large corpora, raising maintenance_work_mem before this command makes the build markedly faster.');

        return self::SUCCESS;
    }
}
