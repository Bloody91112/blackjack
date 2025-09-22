<?php

namespace Src\Game\Domain\ValueObjects;

use InvalidArgumentException;

final class Suit
{

    public const CLUBS = 'clubs';
    public const HEARTS = 'hearts';
    public const DIAMONDS = 'diamonds';
    public const SPADES = 'spades';

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

    public static function all(): array
    {
        $all = [];
        foreach (self::ALLOWED as $value){
            $all[] = new self($value);
        }
        return $all;
    }
}
