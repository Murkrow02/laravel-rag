<?php

declare(strict_types=1);

namespace Murkrow\Rag\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Murkrow\Rag\Models\IngestionRun;
use Throwable;

final class IngestionRunFailed
{
    use Dispatchable;

    public function __construct(
        public readonly IngestionRun $run,
        public readonly ?Throwable $exception = null,
    ) {}
}
