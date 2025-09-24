<?php

namespace Src\Game\Domain\Entities;

use LogicException;
use Src\Game\Domain\Enum\PlayerResult;
use Src\Game\Domain\Enum\PlayerState;
use Src\Game\Domain\ValueObjects\Card;
use Src\Game\Domain\ValueObjects\Ids\PlayerId;

final class Player
{
    private ?Hand $hand = null;
    private ?Bet $bet = null;

    private PlayerState $state = PlayerState::Free;
    private ?PlayerResult $result = null;

    public function __construct(
        private PlayerId $id,
        private string $nickname,
    )
    {}

    public function joinTable(): void
    {
        $this->state = PlayerState::SittingAtTheTable;
    }


    public function startBetting(): void
    {
        if ($this->state !== PlayerState::SittingAtTheTable){
            throw new LogicException("Player can't start betting in state $this->state");
        }
        $this->state = PlayerState::ChoosingABet;
    }

    public function startTurn(): void
    {
        $this->state = PlayerState::Active;
    }

    public function isActive(): bool
    {
        return $this->state === PlayerState::Active;
    }

    public function isStanding(): bool
    {
        return $this->state === PlayerState::Standing;
    }

    public function stand(): void
    {
        $this->state = PlayerState::Standing;
    }

    public function hit(Card $card): void
    {
        $this->hand->receiveCard($card);

        if ($this->hand->value()->isBlackjack()){
            $this->finished(PlayerResult::Blackjack);
        } else if ($this->hand->value()->isBust()){
            $this->finished(PlayerResult::Bust);
        }
    }

    public function finished(PlayerResult $result): void
    {
        $this->state = PlayerState::Finished;
        $this->result = $result;
    }

    public function lost(): void
    {
        $this->finished(PlayerResult::Lost);
    }

    public function won(): void
    {
        $this->finished(PlayerResult::Won);
    }

    public function push(): void
    {
        $this->finished(PlayerResult::Push);
    }

    public function assignHand(Hand $hand): void
    {
        if ($this->hand !== null){
            throw new LogicException("Hand is already assigned.");
        }
        $this->hand = $hand;
    }

    public function hand(): Hand
    {
        if ($this->hand === null){
            throw new LogicException("Player doesnt have assigned card yet.");
        }
        return $this->hand;
    }

    public function placeBet(Bet $bet): void
    {
        if ($this->state !== PlayerState::ChoosingABet){
            throw new LogicException("Player can't place a bet in state $this->state");
        }

        $this->bet = $bet;
        $this->state = PlayerState::PlacedABet;
    }

    public function bet(): Bet
    {
        if ($this->bet === null){
            throw new LogicException("Player doesnt have assigned bet yet.");
        }
        return $this->bet;
    }

    public function id(): PlayerId
    {
        return $this->id;
    }

    public function nickname(): string
    {
        return $this->nickname;
    }

    public function state(): PlayerState
    {
        return $this->state;
    }

    public function result(): PlayerResult
    {
        return $this->result;
    }

    public function hasBlackjack(): bool
    {
        return $this->hand()->value()->isBlackjack();
    }


}
