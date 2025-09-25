<?php

namespace Src\Game\Domain\Entities;

use LogicException;
use Src\Game\Domain\Enum\GameState;
use Src\Game\Domain\Enum\PlayerState;
use Src\Game\Domain\ValueObjects\Card;
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

    public function currentPlayer(): Player
    {
        if ($this->currentPlayerIndex === null){
            throw new \DomainException("There is no current player assigned.");
        }
        return $this->players[$this->currentPlayerIndex];
    }

    public function nextPlayer(): void
    {
        if ($this->currentPlayer()->isActive()){
            throw new LogicException("Previous player has not made a turn yet.");
        }

        $this->currentPlayerIndex++;

        if ($this->currentPlayerIndex < count($this->players)) {
            $this->startTurn();
        } else {
            $this->currentPlayerIndex = null;
            $this->state = GameState::DealerTurn;
        }
    }

    public function playersTurnsStage(): void
    {
        $this->state = GameState::PlayersTurn;
        $this->currentPlayerIndex = 0;
        $this->startTurn();
    }

    public function startTurn(): void
    {
        $this->currentPlayer()->startTurn();
    }

    public function playerHit(): void
    {
        $card = $this->shoe->draw();
        $this->currentPlayer()->hit($card);
    }

    public function playerStand(): void
    {
        $this->currentPlayer()->stand();
        $this->nextPlayer();
    }

    public function placeDealerCard(): void
    {
        $card = $this->shoe->draw();
        $this->dealerHand->receiveCard($card);
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

    public function players(): array
    {
        return $this->players;
    }

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

        foreach ($cards as $dealerCard){
            $this->shoe->collect($dealerCard);
        }

        $this->state = GameState::Finished;
    }

}

