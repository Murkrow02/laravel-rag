<?php

declare(strict_types=1);

namespace Murkrow\Rag\Contracts;

interface TokenEstimator
{
    public function count(string $text): int;
}
