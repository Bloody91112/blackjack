<?php

namespace Src\Game\Domain\Entities;

use DomainException;
use Ramsey\Uuid\Uuid;
use Src\Game\Domain\ValueObjects\Card;
use Src\Game\Domain\ValueObjects\HandValue;

class Hand
{
    private string $id;
    private HandValue $value;

    /** @var array<Card> */
    private array $cards = [];
    public function __construct(
    ){
        $this->id = Uuid::uuid4()->toString();
        $this->value = new HandValue();
    }

    public function getCard(Card $card): void
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

    public function value(): int
    {
        return $this->value->score();
    }

}
