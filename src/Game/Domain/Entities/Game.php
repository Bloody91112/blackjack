<?php

namespace Src\Game\Domain\Entities;

use LogicException;
use Src\Game\Domain\Enum\GameState;
use Src\Game\Domain\Enum\PlayerState;
use Src\Game\Domain\ValueObjects\Card;
use Src\Game\Domain\ValueObjects\Ids\GameId;

class Game
{
    private GameState $state;
    private int $currentPlayerIndex = 0;

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

    public function start(): void
    {
        $this->state = GameState::Started;
        foreach ($this->players as $player){
            $player->join();
        }
    }

    public function playersBet(): void
    {
        $this->state = GameState::Betting;
        foreach ($this->players as $player){
            $player->offerToPlaceABet();
        }
    }

    public function currentPlayer(): Player
    {
        return $this->players[$this->currentPlayerIndex];
    }

    public function nextPlayer(): void
    {
        $this->currentPlayer()->finishTurn();

        $this->currentPlayerIndex++;

        if ($this->currentPlayerIndex < count($this->players)) {
            $this->currentPlayer()->startTurn();
        } else {
            $this->state = GameState::DealerTurn;
        }
    }

    public function playersTurns(): void
    {
        $this->state = GameState::PlayersTurn;
        $this->currentPlayerIndex = 0;
        $this->startTurn();
    }

    public function startTurn(): void
    {
        $this->players[$this->currentPlayerIndex]->startTurn();
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

    public function dealerHit(): void
    {
        $card = $this->shoe->draw();
        $this->dealerHand->receiveCard($card);

        if ($this->dealerHand->value()->score() < 17){
            $this->dealerHit();
        }

        if ($this->dealerHand->value()->score() < 21){
            // сравниваются карты игроков и дилера
        }

        if ($this->dealerHand->value()->isBlackjack()){
            foreach ($this->players as $player){
                if ($player->hand()->value()->isBlackjack()){
                    //... игрок проигрывает
                } else {
                    $player->lost();
                }
            }
        }

        if ($this->dealerHand->value()->score() > 21){
            // все оставшиеся игроки выигрывают
        }
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

    public function id(): GameId
    {
        return $this->id;
    }

    public function dealerHand(): Hand
    {
        return $this->dealerHand;
    }

}

