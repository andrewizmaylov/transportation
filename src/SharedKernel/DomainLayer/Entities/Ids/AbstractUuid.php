<?php

declare(strict_types=1);

namespace Src\SharedKernel\DomainLayer\Entities\Ids;

use InvalidArgumentException;
use Ramsey\Uuid\Uuid;

abstract class AbstractUuid
{
    public function __construct(
        public readonly string $id,
    ) {
        if (! Uuid::isValid($this->id)) {
            throw new InvalidArgumentException(sprintf('Id "%s" is not valid', $this->id));
        }
        if (empty($id)) {
            throw new InvalidArgumentException('Entity ID cannot be empty');
        }
    }

    public function value(): string
    {
        return $this->id;
    }

    //    public function equals(EntityId $other): bool
    //    {
    //        return $this->id === $other->id && get_class($this) === get_class($other);
    //    }
}
