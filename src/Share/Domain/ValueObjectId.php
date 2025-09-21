<?php

namespace Src\Share\Domain;

use Ramsey\Uuid\Uuid;

abstract class ValueObjectId
{
    public function __construct(private readonly string $id) {}

    public function equals(self $other): bool
    {
        return $this->id === $other->id;
    }

    public function __toString(): string
    {
        return $this->id;
    }

    public function value(): string
    {
        return $this->id;
    }

    public static function generate(): static
    {
        return new static(Uuid::uuid4()->toString());
    }
}
