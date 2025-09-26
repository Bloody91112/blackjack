<?php

namespace Src\Game\Domain\ValueObjects;

use InvalidArgumentException;

final class Rank
{
    public const TWO = "2";
    public const THREE = "3";
    public const FOUR = "4";
    public const FIVE = "5";
    public const SIX = "6";
    public const SEVEN = "7";
    public const EIGHT = "8";
    public const NINE = "9";
    public const TEN = "10";
    public const JACK = "jack";
    public const QUEEN = "queen";
    public const KING = "king";
    public const ACE = "ace";

    private const ALLOWED = [
        self::TWO,
        self::THREE,
        self::FOUR,
        self::FIVE,
        self::SIX,
        self::SEVEN,
        self::EIGHT,
        self::NINE,
        self::TEN,
        self::JACK,
        self::QUEEN,
        self::KING,
        self::ACE,
    ];

    private const BASE_VALUES = [
        self::ACE => 11,
        self::KING => 10,
        self::QUEEN => 10,
        self::JACK => 10,
        self::TEN => 10,
        self::NINE => 9,
        self::EIGHT => 8,
        self::SEVEN => 7,
        self::SIX => 6,
        self::FIVE => 5,
        self::FOUR => 4,
        self::THREE => 3,
        self::TWO => 2,
    ];

    public function __construct(private readonly string $value)
    {
        if (!in_array($value, self::ALLOWED)){
            throw new InvalidArgumentException("Invalid rank: $value");
        }
    }

    public function equalsTo(self $other): bool
    {
        return $this->value === $other->value;
    }

    public function value(): string
    {
        return $this->value;
    }

    public function baseValue(): int
    {
        return self::BASE_VALUES[$this->value];
    }

    public function __toString(): string
    {
        return $this->value;
    }

    public function isAce(): bool
    {
        return $this->value === self::ACE;
    }

    public static function all(): array
    {
        $all = [];
        foreach (self::ALLOWED as $value){
            $all[] = new self($value);
        }
        return $all;
    }

    public static function two(): self
    {
        return new self(self::TWO);
    }

    public static function three(): self
    {
        return new self(self::THREE);
    }

    public static function four(): self
    {
        return new self(self::FOUR);
    }

    public static function five(): self
    {
        return new self(self::FIVE);
    }

    public static function six(): self
    {
        return new self(self::SIX);
    }

    public static function seven(): self
    {
        return new self(self::SEVEN);
    }

    public static function eight(): self
    {
        return new self(self::EIGHT);
    }

    public static function nine(): self
    {
        return new self(self::NINE);
    }

    public static function ten(): self
    {
        return new self(self::TEN);
    }

    public static function jack(): self
    {
        return new self(self::JACK);
    }

    public static function queen(): self
    {
        return new self(self::QUEEN);
    }

    public static function king(): self
    {
        return new self(self::KING);
    }

    public static function ace(): self
    {
        return new self(self::ACE);
    }

}
