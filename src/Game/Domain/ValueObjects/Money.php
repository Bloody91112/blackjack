<?php

namespace Src\Game\Domain\ValueObjects;

use InvalidArgumentException;

final readonly class Money
{
    public function __construct(private int $amount)
    {
        if ($this->amount <= 0){
            throw new InvalidArgumentException("Amount should be more than 0");
        }
    }

    public function equalsTo(self $other): bool
    {
        return $this->amount === $other->amount;
    }

    public function add(Money $other): self
    {
        return new self($this->amount + $other->amount);
    }

    public function amount(): int
    {
        return $this->amount;
    }

    public function __toString(): string
    {
        return $this->amount;
    }

}
