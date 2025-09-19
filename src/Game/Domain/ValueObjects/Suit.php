<?php

namespace Src\Game\Domain\ValueObjects;

use InvalidArgumentException;

final class Suit
{

    public const CLUBS = 'Clubs';
    public const HEARTS = 'Hearts';
    public const DIAMONDS = 'Diamonds';
    public const SPADES = 'Spades';

    private const ALLOWED = [
        self::CLUBS,
        self::HEARTS,
        self::DIAMONDS,
        self::SPADES,
    ];

    public function __construct(private readonly string $value){
        if (!in_array($value, self::ALLOWED)){
            throw new InvalidArgumentException("Invalid suit: $value");
        }
    }

    public function equalsTo(self $other): bool
    {
        return $this->value === $other->value;
    }

    public static function from(string $value): self
    {
        return new self($value);
    }

    public function value(): string
    {
        return $this->value;
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
