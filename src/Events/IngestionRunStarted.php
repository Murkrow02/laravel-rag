<?php

declare(strict_types=1);

namespace Murkrow\Rag\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Murkrow\Rag\Models\IngestionRun;

final class IngestionRunStarted
{
    use Dispatchable;

    public function __construct(public readonly IngestionRun $run) {}
}
