<?php

declare(strict_types=1);

namespace Src\SharedKernel\PresentationLayer\HTTP\V1\Responder\Contracts;

use Src\SharedKernel\DomainLayer\ValueObjects\PaginatedResult;

interface ResponderInterface
{
    public function composeEntity(object $entity): array;

    public function composePaginatedResults(PaginatedResult $paginatedResults): PaginatedResult;

    public function composeFromModel(object $model): object;
}
