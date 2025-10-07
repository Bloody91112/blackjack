<?php

namespace Src\Game\Domain\Entities;

use DomainException;
use LogicException;
use Src\Game\Domain\Enum\GameState;
use Src\Game\Domain\Enum\PlayerState;
use Src\Game\Domain\ValueObjects\Ids\GameId;
use Src\Game\Domain\ValueObjects\Ids\PlayerId;

class Game
{
    private GameState $state;
    private ?int $currentPlayerIndex = null;

    public function __construct(
        private GameId $id,
        private Shoe $shoe,
        private Hand $dealerHand,
        /** @var array<Player> $players */
        private array $players,
    ){
        if (empty($this->players)){
            throw new LogicException("Cant create a game. There are no players to play.");
        }

        $this->state = GameState::Created;

    }

    public function betStart(): void
    {
        $this->state = GameState::Betting;
        foreach ($this->players as $player){
            $player->startBetting();
        }
    }

    public function playersTurnsStage(): void
    {
        foreach ($this->players as $player){
            if ($player->state() !== PlayerState::PlacedABet){
                throw new DomainException("Cant start players turns, player {$player->id()->value()} is not placed a bet");
            }

            if (count($player->hand()->cards()) !== 2){
                throw new DomainException("Cant start players turns, player {$player->id()->value()} doesnt have 2 cards");
            }
        }

        if (count($this->dealerHand->cards()) !== 1){
            throw new DomainException("Cant start players turns, dealer doesnt have card");
        }

        $this->state = GameState::PlayersTurn;

        $this->checkPlayersBlackjack();
        $this->nextPlayerTurn();
    }

    public function currentPlayer(): Player
    {
        if ($this->currentPlayerIndex === null){
            throw new LogicException("Current player not assigned");
        }

        return $this->players[$this->currentPlayerIndex];
    }

    public function nextPlayerTurn(): void
    {
        if ($this->state() !== GameState::PlayersTurn){
            throw new LogicException("Cant assign next player. Wrong game state: {$this->state()->value}");
        }

        if ($this->currentPlayerIndex !== null) {
            if ($this->currentPlayer()->isActive()) {
                throw new LogicException("Previous player has not finished his turn yet.");
            }
            $startIndex = $this->currentPlayerIndex + 1;
        } else {
            $startIndex = 0;
        }

        for ($i = $startIndex; $i < count($this->players); $i++) {
            if ($this->players[$i]->hasFinished()) {
                continue;
            }

            $this->currentPlayerIndex = $i;
            $this->currentPlayer()->startTurn();
            return;
        }

        $this->state = GameState::DealerTurn;
    }

    public function playerHit(PlayerId $playerId): void
    {
        if (!$this->currentPlayer()->id()->equals($playerId)){
            throw new DomainException("Its not player {$playerId->value()} turn.");
        }

        $this->currentPlayer()->hit($this->shoe->draw());

        if ($this->currentPlayer()->state() !== PlayerState::Active){
            $this->nextPlayerTurn();
        }
    }

    public function playerStand(PlayerId $playerId): void
    {
        if (!$this->currentPlayer()->id()->equals($playerId)){
            throw new DomainException("Its not player {$playerId->value()} turn.");
        }
        $this->currentPlayer()->stand();
        $this->nextPlayerTurn();
    }

    public function placeDealerCard(): void
    {
        $card = $this->shoe->draw();
        $this->dealerHand->receiveCard($card);
    }

    public function placeOtherDealerCards(): void
    {
        while ($this->dealerScore() < 17){
            $this->placeDealerCard();
        }
    }

    public function placeBet(PlayerId $playerId, Bet $bet): void
    {
        $this->findPlayer($playerId)->placeBet($bet);
    }

    public function findPlayer(PlayerId $playerId): Player
    {
        foreach ($this->players as $player){
            if ($player->id()->equals($playerId)){
                return $player;
            }
        }

        throw new LogicException("There is no player with id {$playerId} at the game");
    }

    public function state(): GameState
    {
        return $this->state;
    }

    public function shoe(): Shoe
    {
        return $this->shoe;
    }

    /** @return array<Player> */
    public function players(): array
    {
        return $this->players;
    }

    /** @return array<Player> */
    public function standingPlayers(): array
    {
        return array_filter($this->players, fn(Player $player) => $player->isStanding());
    }


    public function id(): GameId
    {
        return $this->id;
    }

    public function dealerHand(): Hand
    {
        return $this->dealerHand;
    }

    public function dealerScore(): int
    {
        return $this->dealerHand()->value()->score();
    }

    private function collectPlayersCards(): array
    {
        $cards = [];
        foreach ($this->players as $player){
            $cards[] = $player->hand()->returnCards();
        }
        return $cards;
    }

    public function finish(): void
    {
        $cards = [...$this->collectPlayersCards(), $this->dealerHand->returnCards()];

        foreach ($cards as $cardInHand){
            foreach ($cardInHand as $card){
                $this->shoe->collect($card);
            }
        }

        $this->state = GameState::Finished;
    }

    private function checkPlayersBlackjack(): void
    {
        foreach ($this->players() as $player){
            if ($player->hand()->hasBlackjack()){
                $player->finishWithBlackjack();
            }
        }
    }

}

