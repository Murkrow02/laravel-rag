<?php

declare(strict_types=1);

namespace Murkrow\Rag\Contracts;

use Murkrow\Rag\Data\RetrievalOptions;
use Murkrow\Rag\Data\RetrievalResult;

interface Retriever
{
    public function retrieve(string $question, RetrievalOptions $options = new RetrievalOptions): RetrievalResult;
}
