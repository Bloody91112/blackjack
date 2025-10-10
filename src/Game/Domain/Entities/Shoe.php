<?php

namespace Src\Game\Domain\Entities;

use DomainException;
use LogicException;
use Src\Game\Domain\ValueObjects\Card;
use Src\Game\Domain\ValueObjects\Deck;
use Src\Game\Domain\ValueObjects\Ids\ShoeId;

class Shoe
{
    public const MIN_DECKS = 1;
    public const MAX_DECKS = 7;

    /** @var array<Card> $cards */
    private array $cards = [];

    public function __construct(
        private ShoeId $id,
        /** @var array<Deck> $decks */
        private readonly array $decks
    ){
        if (count($this->decks) < self::MIN_DECKS){
            throw new DomainException("Not enough decks:" . count($this->decks) ." . Minimum is: " . self::MIN_DECKS);
        }

        if (count($this->decks) > self::MAX_DECKS){
            throw new DomainException("There are more decks (" . count($this->decks) . ") than maximum (" . self::MAX_DECKS . ")");
        }

        foreach ($this->decks as $deckKey => $deck){
            foreach ($deck->cards() as $card){
                $key = "deck_{$deckKey}_card_{$card->hashKey()}";
                $this->cards[$key] = $card;
            }
        }
    }

    public function shuffle(): void
    {
        $keys = array_keys($this->cards);
        shuffle($keys);
        $random = [];
        foreach ($keys as $key) {
            $random[$key] = $this->cards[$key];
        }
        $this->cards = $random;
    }

    public function draw(): Card
    {
        if (empty($this->cards)){
            throw new LogicException("There is no cards left");
        }

        return array_shift($this->cards);
    }

    public function drawConcrete(Card $card): ?Card
    {
        foreach ($this->cards as $key => $shoeCard){
            if ($shoeCard->equalsTo($card)){
                unset($this->cards[$key]);
                return $shoeCard;
            }
        }

        throw new LogicException("Card $card not found");
    }

    public function collect(Card $card): void
    {
        $this->cards[] = $card;
    }

    public function cards(): array
    {
        return $this->cards;
    }

    public function decks(): array
    {
        return $this->decks;
    }

    public function id(): ShoeId
    {
        return $this->id;
    }
}
