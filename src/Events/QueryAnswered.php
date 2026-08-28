<?php

declare(strict_types=1);

namespace Murkrow\Rag\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Murkrow\Rag\Data\AnswerResult;
use Murkrow\Rag\Enums\QueryChannel;

final class QueryAnswered
{
    use Dispatchable;

    public function __construct(
        public readonly AnswerResult $result,
        public readonly QueryChannel $channel,
    ) {}
}
