<?php

declare(strict_types=1);

namespace Murkrow\Rag\Contracts;

interface TextNormalizer
{
    public function normalize(string $text): string;
}
