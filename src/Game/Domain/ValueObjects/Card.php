<?php

namespace Src\Game\Domain\ValueObjects;

final readonly class Card
{
    public function __construct(
        private Rank $rank,
        private Suit $suit
    ){}

    public static function from(Rank $rank, Suit $suit): self
    {
        return new self($rank, $suit);
    }

    public function equalsTo(self $other): bool
    {
        return $this->rank->equalsTo($other->rank)
            && $this->suit->equalsTo($other->suit);
    }

    public function __toString(): string
    {
        return "{$this->rank} of {$this->suit}";
    }

    public function suit(): Suit
    {
        return $this->suit;
    }

    public function rank(): Rank
    {
        return $this->rank;
    }
}
