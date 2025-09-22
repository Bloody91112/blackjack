<?php

namespace Src\Game\Domain\ValueObjects;


use DomainException;

final class Deck
{
    public const STANDARD_SIZE = 52;

    private array $cards = [];

    /** @var array<Card> $cards */
    public function __construct(array $cards)
    {
        if (count($cards) != self::STANDARD_SIZE){
            throw new DomainException("There should be " . self::STANDARD_SIZE . " cards in deck.");
        }

        foreach ($cards as $card) {
            $key = $card->hashKey();

            if (isset($this->cards[$key])) {
                throw new DomainException("Duplicate card: $key");
            }

            $this->cards[$key] = $card;
        }
    }

    /** @var array<Card> $cards */
    public static function from(array $cards): self
    {
        return new self($cards);
    }

    public function equalsTo(self $other): bool
    {
        foreach ($this->cards as $card){
            $foundSame = false;
            foreach ($other->cards as $otherCard){
                if ($card->equalsTo($otherCard)){
                    $foundSame = true;
                    break;
                };
            }
            if (!$foundSame) return false;
        }
        return true;
    }

    public function cards(): array
    {
        return $this->cards;
    }

}
