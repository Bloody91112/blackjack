<?php

namespace Src\Game\Domain\Entities;

use DomainException;
use Ramsey\Uuid\Uuid;
use Src\Game\Domain\ValueObjects\Card;
use Src\Game\Domain\ValueObjects\HandValue;
use Src\Game\Domain\ValueObjects\Ids\HandId;

class Hand
{
    /** @var array<Card> */
    private array $cards = [];
    public function __construct(
        private HandId $id,
        private HandValue $value
    ){
        $this->id = new HandId(Uuid::uuid4()->toString());
        $this->value = new HandValue();
    }

    public function receiveCard(Card $card): void
    {
        if ($this->value->isBust()){
            throw new DomainException("Cant take card when it bast");
        }

        if ($this->value->isBlackjack()){
            throw new DomainException("Cant take card when Blackjack");
        }
        $this->cards[] = $card;
        $this->value = $this->value->recount($card);
    }

    public function id(): string
    {
        return $this->id;
    }

    public function value(): HandValue
    {
        return $this->value;
    }

    public function cards(): array
    {
        return $this->cards;
    }

    public function hasAce(): bool
    {
        return $this->value->hasAce();
    }

}
