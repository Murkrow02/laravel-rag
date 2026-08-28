<?php

declare(strict_types=1);

namespace Murkrow\Rag\Retrieval\Lexical;

use Murkrow\Rag\Contracts\LexicalSearch;
use Murkrow\Rag\Data\VectorQuery;

/**
 * The default: hybrid retrieval off.
 */
final class NullLexicalSearch implements LexicalSearch
{
    /**
     * @return array<int, int>
     */
    public function candidates(string $query, VectorQuery $filters, int $limit): array
    {
        return [];
    }

    public function isAvailable(): bool
    {
        return false;
    }
}
