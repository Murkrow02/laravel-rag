<?php

declare(strict_types=1);

namespace Murkrow\Rag\Filament\Resources\QueryResource\Pages;

use Filament\Resources\Pages\ListRecords;
use Murkrow\Rag\Filament\Resources\QueryResource;

class ListQueries extends ListRecords
{
    protected static string $resource = QueryResource::class;
}
